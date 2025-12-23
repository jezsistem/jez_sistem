<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PurchaseOrderArticle;
use App\Models\PurchaseOrderArticleDetail;
use App\Models\PurchaseOrderLog;
use Illuminate\Support\Facades\DB;

class PurchaseOrderArticleController extends Controller
{
    public function saveDiscount(Request $request)
    {
        $discount = $request->_discount;
        $id = $request->_id;

        try {
            DB::beginTransaction();

            //get po
            $po = PurchaseOrder::join('purchase_order_articles', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
                ->where('purchase_order_articles.id', $id)
                ->select('purchase_orders.id as po_id')
                ->first();

            $poa = PurchaseOrderArticle::find($id);
            $poa_discount_before = $poa->poa_discount ?? 0;

            $save = PurchaseOrderArticle::where(['id' => $id])->update(['poa_discount' => $discount]);

            $timestamp = date('Y-m-d H:i:s');

            // Log the action
            $purchaseOrderItemLog = new PurchaseOrderLog();
            $purchaseOrderItemLog->storePOItemLog(
                $po->po_id,
                auth()->id(),
                PurchaseOrderLog::TYPE_ARTICLE,
                'discount',
                $poa->id,
                $poa_discount_before,
                $discount,
                $timestamp
            );

            DB::commit();
            $r['status'] = '200';
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function saveExtraDiscount(Request $request)
    {
        $extra_discount = $request->_extra_discount;
        $id = $request->_id;

        try {
            DB::beginTransaction();

            //get po
            $po = PurchaseOrder::join('purchase_order_articles', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
                ->where('purchase_order_articles.id', $id)
                ->select('purchase_orders.id as po_id')
                ->first();

            $poa = PurchaseOrderArticle::find($id);
            $poa_extra_discount_before = $poa->poa_extra_discount ?? 0;

            $save = PurchaseOrderArticle::where(['id' => $id])->update(['poa_extra_discount' => $extra_discount]);

            $timestamp = date('Y-m-d H:i:s');

            // Log the action
            $purchaseOrderItemLog = new PurchaseOrderLog();
            $purchaseOrderItemLog->storePOItemLog(
                $po->po_id,
                auth()->id(),
                PurchaseOrderLog::TYPE_ARTICLE,
                'extra_discount',
                $poa->id,
                $poa_extra_discount_before,
                $extra_discount,
                $timestamp
            );

            DB::commit();
            $r['status'] = '200';
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function saveSubDiscount(Request $request)
    {
        $sub_discount = $request->_sub_discount;
        $id = $request->_id;

        try {
            DB::beginTransaction();

            //get po
            $po = PurchaseOrder::join('purchase_order_articles', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
                ->where('purchase_order_articles.id', $id)
                ->select('purchase_orders.id as po_id')
                ->first();

            $poa = PurchaseOrderArticle::find($id);
            $poa_sub_discount_before = $poa->poa_sub_discount ?? 0;

            $save = PurchaseOrderArticle::where(['id' => $id])->update(['poa_sub_discount' => $sub_discount]);

            $timestamp = date('Y-m-d H:i:s');

            // Log the action
            $purchaseOrderItemLog = new PurchaseOrderLog();
            $purchaseOrderItemLog->storePOItemLog(
                $po->po_id,
                auth()->id(),
                PurchaseOrderLog::TYPE_ARTICLE,
                'sub_discount',
                $poa->id,
                $poa_sub_discount_before,
                $sub_discount,
                $timestamp
            );

            DB::commit();
            $r['status'] = '200';
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }

        return json_encode($r);
    }

    public function saveReminder(Request $request)
    {
        $reminder = $request->_reminder;
        $id = $request->_id;

        try {
            DB::beginTransaction();

            //get po
            $po = PurchaseOrder::join('purchase_order_articles', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
                ->where('purchase_order_articles.id', $id)
                ->select('purchase_orders.id as po_id')
                ->first();

            $poa = PurchaseOrderArticle::find($id);
            $poa_reminder_before = $poa->poa_reminder ?? '';

            $save = PurchaseOrderArticle::where(['id' => $id])->update(['poa_reminder' => $reminder]);

            $timestamp = date('Y-m-d H:i:s');

            // Log the action
            $purchaseOrderItemLog = new PurchaseOrderLog();
            $purchaseOrderItemLog->storePOItemLog(
                $po->po_id,
                auth()->id(),
                PurchaseOrderLog::TYPE_ARTICLE,
                'reminder',
                $poa->id,
                $poa_reminder_before,
                $reminder,
                $timestamp
            );

            DB::commit();
            $r['status'] = '200';
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
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
