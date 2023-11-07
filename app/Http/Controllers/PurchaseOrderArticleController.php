<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PurchaseOrderArticle;
use App\Models\PurchaseOrderArticleDetail;

class PurchaseOrderArticleController extends Controller
{
    public function saveDiscount(Request $request)
    {
        $discount = $request->_discount;
        $id = $request->_id;
        $save = PurchaseOrderArticle::where(['id' => $id])->update(['poa_discount' => $discount]);
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
        $save = PurchaseOrderArticle::where(['id' => $id])->update(['poa_extra_discount' => $extra_discount]);
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
        $save = PurchaseOrderArticle::where(['id' => $id])->update(['poa_reminder' => $reminder]);
        if (!empty($save)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $poa = new PurchaseOrderArticle;
        $poad = new PurchaseOrderArticleDetail;
        $id = $request->input('_id');
        $delete_poad = PurchaseOrderArticleDetail::where(['poa_id' => $id])->delete();
        $delete_poa = $poa->deleteData($id);
        if ($delete_poa) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
