<?php

namespace App\Http\Controllers;

use App\Imports\PurchaseOrderExcelImport;
use App\Models\ProductStock;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderArticle;
use App\Models\PurchaseOrderArticleDetail;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderImportExcelController extends Controller
{
    public function importExcel(Request $request)
    {
        $po_id = PurchaseOrder::query()->where('po_invoice', $request->_po_invoice_label)->first()->id;

        try {
            if ($request->hasFile('importFile')) {

                $file = $request->file('importFile');
                $nama_file = rand() . $file->getClientOriginalName();
                $file->move('excel', $nama_file);
                $import = new PurchaseOrderExcelImport;
                Excel::import($import, public_path('excel/' . $nama_file));
                //                dd($import);
                unlink(public_path('excel/' . $nama_file));
                if ($import->getRowCount() >= 0) {
                    $processData = $this->processImportData($import->getData(), $po_id);

                    $r['process_data'] = $processData;

                    if (isset($processData['status']) && $processData['status'] === 'error') {
                        $r['status'] = '404';
                        $r['not_found'] = $processData['not_found'];
                        return json_encode($r);
                    }

                    if (isset($processData['status']) && $processData['status'] === 'duplicate') {
                        $r['status'] = '403';
                        $r['duplicate_items'] = $processData['duplicate_items'];
                        return json_encode($r);
                    }

                    $r['data'] = $import;
                    $r['status'] = '200';
                    $r['po_id'] = $po_id;
                } else {
                    $r['status'] = '419';
                }
            } else {
                $r['status'] = '400';
            }

            return json_encode($r);
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    public function processImportData(array $data, $po_id)
    {
        // dd($data);
        $notFoundItems = array_filter($data, fn($value) => $value['status'] === 'Not Found');
        if (!empty($notFoundItems)) {
            return [
                'status' => 'error',
                'not_found' => array_map(fn($item) => [
                    'sku' => $item['sku'],
                    'qty' => $item['poad_qty'],
                ], $notFoundItems),
            ];
        }

        $duplicateItems = array_filter($data, function ($value) use ($po_id) {
            $poa_id = PurchaseOrderArticle::where([
                'po_id' => $po_id,
                'p_id' => $value['p_id'],
            ])->value('id');

            return PurchaseOrderArticleDetail::where([
                'poa_id' => $poa_id,
                'pst_id' => $value['pst_id'],
            ])->exists();
        });

        if (!empty($duplicateItems)) {
            return [
                'status' => 'duplicate',
                'duplicate_items' => array_map(fn($item) => [
                    'sku' => $item['sku'],
                    'qty' => $item['poad_qty'],
                ], $duplicateItems),
            ];
        }

        try {
            DB::beginTransaction();
            $poid = $po_id;
            foreach ($data as $key => $value) {
                $check_poa = PurchaseOrderArticle::where([
                    'po_id' => $poid,
                    'p_id' => $value['p_id'],
                ])->exists();
                if (!$check_poa) {
                    $poa_id = DB::table('purchase_order_articles')->insertGetId([
                        'po_id' => $poid,
                        'p_id' => $value['p_id'],
                        'poa_discount' => $value['disc'],
                        'poa_extra_discount' => $value['ex_disc'],
                        'poa_sub_discount' => $value['sub_disc'],
                    ]);
                } else {
                    $poa_id = DB::table('purchase_order_articles')->select('id')->where([
                        'po_id' => $poid,
                        'p_id' => $value['p_id'],
                    ])->get()->first()->id;
                }

                $check_poad = PurchaseOrderArticleDetail::where([
                    'poa_id' => $poa_id,
                    'pst_id' => $value['pst_id'],
                ])->exists();

                if ($value['purchase_price'] == 0) {

                    $price_tag = ProductStock::where('id', $value['pst_id'])->first()->ps_price_tag;
                    $new_price = $price_tag;
                    if ($value['disc']) {
                        $new_price = $new_price - ($new_price * ((float)$value['disc'] / 100));
                    }

                    if ($value['ex_disc']) {
                        $new_price = $new_price - ($new_price * ((float)$value['ex_disc'] / 100));
                    }

                    if ($value['sub_disc']) {
                        $new_price = $new_price - ($new_price * ((float)$value['sub_disc'] / 100));
                    }
                    $new_cogs = $new_price;
                } else {
                    $new_cogs = (float)$value['purchase_price'];
                }

                $total_cogs = $new_cogs *  (float)$value['poad_qty'];

                if (!$check_poad) {
                    DB::table('purchase_order_article_details')->insert([
                        'poa_id' => $poa_id,
                        'pst_id' => $value['pst_id'],
                        'poad_qty' => $value['poad_qty'],
                        'poad_purchase_price' => $new_cogs,
                        'poad_total_price' => $total_cogs,
                        'poad_draft' => 1,
                    ]);
                }
            }
            DB::commit();
            return $data;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
