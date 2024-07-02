<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;

class ProductStockController extends Controller
{
    public function checkProductStock(Request $request)
    {
//        $product_stock = new ProductStock;
//        $select = ['product_stocks.id as psid', 'p_id', 'sz_id', 'sz_name', 'ps_qty', 'ps_barcode', 'ps_sell_price', 'ps_purchase_price', 'ps_price_tag', 'ps_running_code'];
//        $where = [
//            'p_id' => $request->_p_id
//        ];
//        $check_data = $product_stock->getAllData($select, $where);
//        if (!empty($check_data->first()->sz_id)) {
//            $r['data'] = $check_data;
//        } else {
//            $r['data'] = '400';
//        }
//        return json_encode($r);
        $st_id = Auth::user()->st_id;

        $article = $request->_art;

        $st_code = Store::where('id', $st_id)->get()->first()->st_code;

        $data = DB::table('product_location_setups as t1')
            ->join('product_locations as t2', 't1.pl_id', '=', 't2.id')
            ->join('stores as t3', 't2.st_id', '=', 't3.id')
            ->join('product_stocks as t4', 't1.pst_id', '=', 't4.id')
            ->join('products as t5', 't4.p_id', '=', 't5.id')
            ->join('sizes as ts', 't4.sz_id', '=', 'ts.id')
            ->select('t1.pl_id', 't4.ps_barcode', 'ts.sz_name', DB::raw('SUM(pls_qty) as qty'), 'sz_id', 'ps_price_tag')
            ->where('t5.article_id', '=', $article)
            ->where('t3.st_code', '=',$st_code)
            ->groupBy('t1.pl_id', 't4.ps_barcode', 'sz_name')
            ->orderByRaw('CASE sz_name
                            WHEN "S" THEN 1
                            WHEN "M" THEN 2
                            WHEN "L" THEN 3
                            WHEN "XL" THEN 4
                            WHEN "2XL" THEN 5
                            WHEN "3XL" THEN 6
                            WHEN "4XL" THEN 7
                            WHEN "5XL" THEN 8
                            ELSE 9
                        END')
            ->orderBy('sz_name', 'ASC')
            ->get();

        if (!empty($data->first()->sz_id)) {
            $r['data'] = $article;
            $r['cek'] = $data;
        } else {
            $r['data'] = $article;
            $r['cek'] = $data;
        }
        return json_encode($r);
    }


    public function updatePriceTag(Request $request)
    {
        $check = ProductStock::where(['ps_running_code' => $request->_barcode])->update(['ps_price_tag' => $request->_price_tag]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function updateSellPrice(Request $request)
    {
        $check = ProductStock::where(['ps_running_code' => $request->_barcode])->update(['ps_sell_price' => $request->_sell_price]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function updatePurchasePrice(Request $request)
    {
        $check = ProductStock::where(['ps_running_code' => $request->_barcode])->update(['ps_purchase_price' => $request->_purchase_price]);
        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
