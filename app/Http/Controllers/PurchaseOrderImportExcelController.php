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

//                    dd($processData);

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
//        dd($data);
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

                $price_tag = ProductStock::where('id', $value['pst_id'])->first()->ps_price_tag;
                $total_disc = (float)$value['disc'] + (float)$value['ex_disc'] + (float)$value['sub_disc'];
                $disc_value = ($total_disc / 100) * $price_tag;
                $new_cogs = $price_tag - $disc_value;
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
