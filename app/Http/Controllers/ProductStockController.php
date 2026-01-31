<?php

namespace App\Http\Controllers;

use App\Models\ProductLogs;
use App\Models\Store;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;

class ProductStockController extends Controller
{

    protected function saveLogProductStock($pst_id, $p_id, $user_id, $levels, $target_column, $source, $data_before, $data_after)
    {
        $productLog = new \App\Models\ProductLogs();
        $timestamp = date('Y-m-d H:i:s');
        $productLog->storeProductStockLog(
            $pst_id,
            $p_id,
            $user_id,
            $levels,
            $target_column,
            $source,
            $data_before,
            $data_after,
            $timestamp
        );
    }

    protected function UserActivity($u_id, $activity, $key_identifier)
    {
        if (!empty($u_id)) {
            UserActivity::create([
                'user_id' => $u_id,
                'ua_description' => $activity,
                'identifier' => 'data-products',
                'key_identifier' => $key_identifier,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    public function checkProductStock(Request $request)
    {
        $product_stock = new ProductStock;
        $select = ['product_stocks.id as psid', 'p_id', 'sz_id', 'sz_name', 'ps_qty', 'ps_barcode', 'ps_sell_price', 'ps_purchase_price', 'ps_price_tag', 'ps_running_code'];
        $where = [
            'p_id' => $request->_p_id
        ];
        $check_data = $product_stock->getAllData($select, $where);
        if (!empty($check_data->first()->sz_id)) {
            $r['data'] = $check_data;
        } else {
            $r['data'] = '400';
        }
        return json_encode($r);
        //        $st_id = Auth::user()->st_id;
        //
        //        $article = $request->_art;
        //
        //        $st_code = Store::where('id', $st_id)->get()->first()->st_code;
        //
        //        $data = DB::table('product_location_setups as t1')
        //            ->join('product_locations as t2', 't1.pl_id', '=', 't2.id')
        //            ->join('stores as t3', 't2.st_id', '=', 't3.id')
        //            ->join('product_stocks as t4', 't1.pst_id', '=', 't4.id')
        //            ->join('products as t5', 't4.p_id', '=', 't5.id')
        //            ->join('sizes as ts', 't4.sz_id', '=', 'ts.id')
        //            ->select('t1.pl_id', 't4.ps_barcode', 'ts.sz_name', DB::raw('SUM(pls_qty) as qty'), 'sz_id', 'ps_price_tag')
        //            ->where('t5.article_id', '=', $article)
        //            ->where('t3.st_code', '=',$st_code)
        //            ->groupBy('t1.pl_id', 't4.ps_barcode', 'sz_name')
        //            ->orderByRaw('CASE sz_name
        //                            WHEN "S" THEN 1
        //                            WHEN "M" THEN 2
        //                            WHEN "L" THEN 3
        //                            WHEN "XL" THEN 4
        //                            WHEN "2XL" THEN 5
        //                            WHEN "3XL" THEN 6
        //                            WHEN "4XL" THEN 7
        //                            WHEN "5XL" THEN 8
        //                            ELSE 9
        //                        END')
        //            ->orderBy('sz_name', 'ASC')
        //            ->get();
        //
        //        if ($data->count() > 0) {
        //            $r['data'] = $data;
        //            $r['cek'] = $data->count();
        //        } else {
        //            $r['data'] = $article;
        //            $r['cek'] = $data->count();
        //        }
        //        return json_encode($r);
    }


    public function updatePriceTag(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = new User;
        $product = new \App\Models\Product;

        $is_mdcx = $user->isMDCX($user_id);
        $is_finance = $user->isFintech($user_id);
        $is_admin = $user->isAdmin($user_id);

        $fintechCanChange = $product::$fintechCanChange;
        $mdcxCanChange = $product::$mdcxCanChange;

        if (!$is_admin) {
            $restricted = [];
            $key = 'ps_price_tag';
            $value = $request->_price_tag;
            if (
                (in_array($key, $fintechCanChange) && !$is_finance) ||
                (in_array($key, $mdcxCanChange) && !$is_mdcx)
            ) {
                $current = ProductStock::where('ps_barcode', $request->_barcode)->value($key);
                if ($current != $value) {
                    $restricted[] = $key;
                }
            }

            if (!empty($restricted)) {
                return json_encode([
                    'status' => '403',
                    'message' => 'Anda tidak memiliki izin untuk mengubah kolom Harga Bandrol'
                ]);
            }
        }

        $get_data =  ProductStock::where(['ps_barcode' => $request->_barcode])->first();
        $check = ProductStock::where(['ps_barcode' => $request->_barcode])->update(['ps_price_tag' => $request->_price_tag]);
        if (!empty($check)) {
            $this->saveLogProductStock(
                $get_data->id,
                $get_data->p_id,
                $user_id,
                ProductLogs::LEVEL_SKU,
                'ps_price_tag',
                '/data_produk',
                $get_data->ps_price_tag,
                $request->_price_tag
            );

            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function updateSellPrice(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = new User;
        $product = new \App\Models\Product;

        $is_mdcx = $user->isMDCX($user_id);
        $is_finance = $user->isFintech($user_id);
        $is_admin = $user->isAdmin($user_id);

        $fintechCanChange = $product::$fintechCanChange;
        $mdcxCanChange = $product::$mdcxCanChange;

        if (!$is_admin) {
            $restricted = [];
            $key = 'ps_sell_price';
            $value = $request->_sell_price;

            if (
                (in_array($key, $fintechCanChange) && !$is_finance) ||
                (in_array($key, $mdcxCanChange) && !$is_mdcx)
            ) {
                $current = ProductStock::where('ps_barcode', $request->_barcode)->value($key);
                if ($current != $value) {
                    $restricted[] = $key;
                }
            }
            if (!empty($restricted)) {
                return json_encode([
                    'status' => '403',
                    'message' => 'Anda tidak memiliki izin untuk mengubah kolom Harga Jual'
                ]);
            }
        }

        $get_data =  ProductStock::where(['ps_barcode' => $request->_barcode])->first();
        $check = ProductStock::where(['ps_barcode' => $request->_barcode])->update(['ps_sell_price' => $request->_sell_price]);
        if (!empty($check)) {
            $this->saveLogProductStock(
                $get_data->id,
                $get_data->p_id,
                $user_id,
                ProductLogs::LEVEL_SKU,
                'ps_sell_price',
                '/data_produk',
                $get_data->ps_sell_price,
                $request->_sell_price
            );
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function updatePurchasePrice(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = new User;
        $product = new \App\Models\Product;

        $is_mdcx = $user->isMDCX($user_id);
        $is_finance = $user->isFintech($user_id);
        $is_admin = $user->isAdmin($user_id);

        $fintechCanChange = $product::$fintechCanChange;
        $mdcxCanChange = $product::$mdcxCanChange;

        if (!$is_admin) {
            $restricted = [];
            $key = 'ps_purchase_price';
            $value = $request->_purchase_price;

            if (
                (in_array($key, $fintechCanChange) && !$is_finance) ||
                (in_array($key, $mdcxCanChange) && !$is_mdcx)
            ) {
                $current = ProductStock::where('ps_barcode', $request->_barcode)->value($key);
                if ($current != $value) {
                    $restricted[] = $key;
                }
            }

            if (!empty($restricted)) {
                return json_encode([
                    'status' => '403',
                    'message' => 'Anda tidak memiliki izin untuk mengubah kolom Harga Beli'
                ]);
            }
        }

        $get_data =  ProductStock::where(['ps_barcode' => $request->_barcode])->first();
        $check = ProductStock::where(['ps_barcode' => $request->_barcode])->update(['ps_purchase_price' => $request->_purchase_price]);
        if (!empty($check)) {
            $this->saveLogProductStock(
                $get_data->id,
                $get_data->p_id,
                $user_id,
                ProductLogs::LEVEL_SKU,
                'ps_purchase_price',
                '/data_produk',
                $get_data->ps_purchase_price,
                $request->_purchase_price
            );
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
