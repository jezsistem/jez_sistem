<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductStock;

class ProductStockController extends Controller
{
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
