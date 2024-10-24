<?php

namespace App\Http\Controllers;

use App\Models\PreOrderArticle;
use App\Models\PreOrderArticleDetails;
use App\Models\PurchaseOrderArticle;
use App\Models\PurchaseOrderArticleDetail;
use Illuminate\Http\Request;

class PreOrderArticleController extends Controller
{
    public function saveDiscount(Request $request)
    {
        $discount = $request->_discount;
        $id = $request->_id;
        $save = PreOrderArticle::where(['id' => $id])->update(['poa_discount' => $discount]);
        if (!empty($save)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveExtraDiscount(Request $request)
    {
        $extra_discount = $request->_extra_discount;
        $id = $request->_id;
        $save = PreOrderArticle::where(['id' => $id])->update(['poa_extra_discount' => $extra_discount]);
        if (!empty($save)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveReminder(Request $request)
    {
        $reminder = $request->_reminder;
        $id = $request->_id;
        $save = PreOrderArticle::where(['id' => $id])->update(['poa_reminder' => $reminder]);
        if (!empty($save)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $poa = new PreOrderArticle;
        $poad = new PreOrderArticleDetails;
        $id = $request->input('_id');
        $delete_poad = PreOrderArticleDetails::where(['poa_id' => $id])->delete();
        $delete_poa = $poa->deleteData($id);
        if ($delete_poa) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
