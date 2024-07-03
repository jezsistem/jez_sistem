<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PurchaseOrderArticleDetail;

class PurchaseOrderArticleDetailController extends Controller
{
    public function deleteData(Request $request)
    {
        $poad = new PurchaseOrderArticleDetail;
        $id = $request->input('_id');
        $delete = $poad->deleteData($id);
        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function savePurchasePrice(Request $request)
    {
        $purchase_price = $request->_purchase_price;
        $poad_id = $request->_poad_id;
        $save = PurchaseOrderArticleDetail::where(['id' => $poad_id])->update(['poad_purchase_price' => $purchase_price]);
        if (!empty($save)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveQtyTotal(Request $request)
    {
        $qty = $request->_qty;
        $total = $request->_total;
        $poad_id = $request->_poad_id;
        $poad_purchase_price = $request->_purchase_price;
        // dd($poad_purchase_price);
        $save = PurchaseOrderArticleDetail::where(['id' => $poad_id])
        ->update(['poad_qty' => $qty, 'poad_total_price' => $total, 'poad_purchase_price' => $poad_purchase_price]);

        if (!empty($save)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
