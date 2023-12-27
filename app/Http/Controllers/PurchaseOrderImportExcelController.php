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

                unlink(public_path('excel/' . $nama_file));
                if ($import->getRowCount() >= 0) {
                    $processData = $this->processImportData($import->getData(), $po_id);

                    $r['data'] = $processData;
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
            throw $e;
        }
    }

    private function processImportData(array $data, $po_id)
    {
        // Todo : ID Purchase Orders : 4138
        /** Todo
         *  1.  Looping data
         *  2.  Ambil array ke 2 ( data SKU )
         *  3.  lakukan pengecekan apakah sku ada pada table ts_product_stocks ( ps_barcode )
         *  4.  Ambil array ke 1 ( size )
         *  5.  lakukan pengecekan pada table ts_product_stocks relasi sz_id apakah  data ada
         *  6.  Ambil array ke 3 ( order )
         *  7.  lakukan pengecekan pada table ts_product_stocks ( ps_qty ) //WARNING : ragu-ragu
         *  8.  Ambil id produk dari table ts_product_stocks setelah data semua cocok ( p_id )
         *  9.  Insert Data kedalam table purchase_order_articles (po_id and p_id), Get Id nya juga
         *  10. Insert Data kedalam table purchase_order_article_details
         *      => poa_id didapatkan dari insert data ke table  purchase_order_articles sebelumnya
         *      => pst_id didapatkan dari table ts_product_stocks p_id yang sudah didapatkan sebelumnya
         *      => poad_qty didapatkan dari array ke 3 ( order )
         *      => poad_purchase_price didapatkan dari array ke 4 ( harga brandol )
         *      => poad_total_price didapatkan dari poad_qty * poad_purchase_price
         *      => poad_draft value 1
         */

        /**
         * Example Array
         * array:5 [▼
                "p_id" => 6813
                "pst_id" => 41154
                "poad_qty" => 1
                "poad_purchase_price" => 429000
                "poad_total_price" => 429000
            ]
         */
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

                if (!$check_poad) {
                    DB::table('purchase_order_article_details')->insert([
                        'poa_id' => $poa_id,
                        'pst_id' => $value['pst_id'],
                        'poad_qty' => $value['poad_qty'],
                        'poad_purchase_price' => $value['poad_purchase_price'],
                        'poad_total_price' => $value['poad_total_price'],
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
