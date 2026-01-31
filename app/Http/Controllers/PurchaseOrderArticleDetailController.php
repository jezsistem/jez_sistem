<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PurchaseOrderArticleDetail;
use App\Models\PurchaseOrderLog;
use Illuminate\Support\Facades\Log;

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
        DB::beginTransaction();
        try {
            $purchase_price = $request->_purchase_price;
            $poad_id = $request->_poad_id;

            // Get PO ID
            $po = PurchaseOrder::join('purchase_order_articles', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
                ->join('purchase_order_article_details', 'purchase_order_article_details.poa_id', '=', 'purchase_order_articles.id')
                ->where('purchase_order_article_details.id', $poad_id)
                ->select('purchase_orders.id as po_id')
                ->first();

            $poad = PurchaseOrderArticleDetail::find($poad_id);

            $poad_purchase_price_before = $poad->poad_purchase_price ?? 0;
            $poad_total_price_before = $poad->poad_total_price ?? 0;

            $poad_total_price = $poad->poad_qty * $purchase_price;

            $save = PurchaseOrderArticleDetail::where(['id' => $poad_id])->update([
                'poad_purchase_price' => $purchase_price,
                'poad_total_price' => $poad_total_price
            ]);

            $timestamp = date('Y-m-d H:i:s');

            // Log the action
            $purchaseOrderItemLog = new PurchaseOrderLog();

            if ($poad_purchase_price_before != $purchase_price) {
                $purchaseOrderItemLog->storePOItemLog(
                    $po->po_id,
                    Auth::id(),
                    PurchaseOrderLog::TYPE_ITEMS,
                    'poad_purchase_price',
                    $poad_id,
                    $poad_purchase_price_before,
                    $purchase_price,
                    $timestamp
                );
            }

            if ($poad_total_price_before != $poad_total_price) {
                $purchaseOrderItemLog->storePOItemLog(
                    $po->po_id,
                    Auth::id(),
                    PurchaseOrderLog::TYPE_ITEMS,
                    'poad_total_price',
                    $poad_id,
                    $poad_total_price_before,
                    $poad_total_price,
                    $timestamp
                );
            }

            DB::commit();
            $r['status'] = '200';
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving purchase price: ' . $e->getMessage());
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }
        return json_encode($r);
    }

    public function saveQtyTotal(Request $request)
    {
        DB::beginTransaction();
        try {
            $qty = $request->_qty;
            $total = $request->_total;
            $poad_id = $request->_poad_id;
            $poad_purchase_price = $request->_purchase_price;

            //get po id

            $po = PurchaseOrder::join('purchase_order_articles', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
                ->join('purchase_order_article_details', 'purchase_order_article_details.poa_id', '=', 'purchase_order_articles.id')
                ->where('purchase_order_article_details.id', $poad_id)
                ->select('purchase_orders.id as po_id')
                ->first();

            $poad = PurchaseOrderArticleDetail::where(['id' => $poad_id])->first();

            $qty_before = $poad->poad_qty ?? 0;

            $poad_purchase_price_before = $poad->poad_purchase_price ?? 0;

            $poad_total_price_before = $poad->poad_total_price ?? 0;

            $save = PurchaseOrderArticleDetail::where(['id' => $poad_id])
                ->update([
                    'poad_qty' => $qty,
                    'poad_total_price' => $total,
                    'poad_purchase_price' => $poad_purchase_price
                ]);

            $timestamp = date('Y-m-d H:i:s');

            // Log the action
            $purchaseOrderItemLog = new PurchaseOrderLog();

            if ($qty_before != $qty) {
                $purchaseOrderItemLog->storePOItemLog(
                    $po->po_id,
                    Auth::id(),
                    PurchaseOrderLog::TYPE_ITEMS,
                    'poad_qty',
                    $poad_id,
                    $qty_before,
                    $qty,
                    $timestamp
                );
            }

            if ($poad_purchase_price_before != $poad_purchase_price) {
                $purchaseOrderItemLog->storePOItemLog(
                    $po->po_id,
                    Auth::id(),
                    PurchaseOrderLog::TYPE_ITEMS,
                    'poad_purchase_price',
                    $poad_id,
                    $poad_purchase_price_before,
                    $poad_purchase_price,
                    $timestamp
                );
            }

            if ($poad_total_price_before != $total) {
                $purchaseOrderItemLog->storePOItemLog(
                    $po->po_id,
                    Auth::id(),
                    PurchaseOrderLog::TYPE_ITEMS,
                    'poad_total_price',
                    $poad_id,
                    $poad_total_price_before,
                    $total,
                    $timestamp
                );
            }

            DB::commit();
            $r['status'] = '200';
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving qty total: ' . $e->getMessage());
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
        }
        return json_encode($r);
    }
}
