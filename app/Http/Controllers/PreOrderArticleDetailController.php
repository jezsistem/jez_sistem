<?php

namespace App\Http\Controllers;

use App\Models\PreOrderArticleDetails;
use App\Models\PurchaseOrderArticleDetail;
use Illuminate\Http\Request;

class PreOrderArticleDetailController extends Controller
{
    public function deleteData(Request $request)
    {
        $poad = new PreOrderArticleDetails();
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
        $save = PreOrderArticleDetails::where(['id' => $poad_id])->update(['poad_purchase_price' => $purchase_price]);
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
        $save = PreOrderArticleDetails::where(['id' => $poad_id])->update(['poad_qty' => $qty, 'poad_total_price' => $total]);
        if (!empty($save)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
