<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PurchaseOrderArticleDetailStatus;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductLocation;
use App\Models\ProductLocationSetup;
use Intervention\Image\Facades\Image;

class PurchaseOrderArticleDetailStatusController extends Controller
{ 
    public function storeData(Request $request)
    {
        $st_id = $request->_st_id;
        $pst_id = $request->_pst_id;
        $stkt_id = $request->_stkt_id;
        $tax_id = $request->_tax_id;
        $poad_id = $request->_poad_id;
        $poads_qty = $request->_poads_qty;
        $poads_discount = $request->_poads_discount;
        $poads_cogs = $request->_poads_cogs;
        $poads_extra_discount = $request->_poads_extra_discount;
        $poads_purchase_price = $request->_poads_purchase_price;
        $poads_total_price = $poads_qty * $poads_purchase_price;

        $receive_date = $request->receive_date;
        $receive_invoice = $request->receive_invoice;
        $invoice_date = $request->invoice_date;
        $shipping_cost = $request->shipping_cost ?? 0;


        $check = DB::table('purchase_order_article_detail_statuses')->insertGetId([
            'stkt_id' => $stkt_id,
            'tax_id' => $tax_id,
            'poad_id' => $poad_id,
            'poads_qty' => $poads_qty,
            'poads_discount' => $poads_discount,
            'poads_extra_discount' => $poads_extra_discount,
            'poads_purchase_price' => $poads_purchase_price,
            'poads_total_price' => $poads_total_price,
            'poads_type' => 'IN',
            'u_id_receive' => Auth::user()->id,
            'poads_invoice' => $receive_invoice,
            'invoice_date' => $invoice_date,
            'COGS' => $poads_cogs,
            'shipping_cost' => $shipping_cost ,
            'created_at' => $receive_date.' '.date('H:i:s'),
            'updated_at' => $receive_date.' '.date('H:i:s'),
        ]);

        if ($request->hasFile('invoiceImage')) {
            $image = $request->file('invoiceImage');
            $input['fileName'] = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/upload/poads/receive');
            $img = Image::make($image->path());
            $img->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$input['fileName']);

            DB::table('purchase_order_article_detail_statuses')->where('id', $check)->update([
                'invoice_image' => $input['fileName']
            ]);
        }

        if($request->hasFile('packetImage')) {
            $image = $request->file('packetImage');
            $input['fileName'] = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/upload/poads/receive');
            $img = Image::make($image->path());
            $img->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$input['fileName']);

            DB::table('purchase_order_article_detail_statuses')->where('id', $check)->update([
                'packet_image' => $input['fileName']
            ]);
        }

        if (!empty($check)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PurchaseOrderArticleDetailStatus::select('purchase_order_article_detail_statuses.id as poads_id', 'purchase_order_article_detail_statuses.created_at as created_at', 'stkt_id', 'stkt_name', 'tx_code', 'poads_qty', 'poads_discount', 'poads_extra_discount', 'poads_purchase_price', 'poads_total_price')
            ->leftJoin('stock_types', 'stock_types.id', '=', 'purchase_order_article_detail_statuses.stkt_id')
            ->leftJoin('taxes', 'taxes.id', '=', 'purchase_order_article_detail_statuses.tax_id')
            ->where('poad_id', '=', $request->poad_id))
            ->editColumn('created_at_show', function($data){
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->editColumn('poads_purchase_price_show', function($data){
                return number_format($data->poads_purchase_price);
            })
            ->editColumn('poads_total_price_show', function($data){
                return number_format($data->poads_total_price);
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function revisionData(Request $request)
    {
        $id = $request->_poads_id;
        $stkt_id = $request->stkt_id_edit;
        $poads_qty = $request->poads_qty;
        $poads_discount = $request->poads_discount;
        $poads_extra_discount = $request->poads_extra_discount;
        $poads_purchase_price = $request->poads_purchase_price;
        $poads_total_price = $request->poads_total_price;
        $p_id = PurchaseOrderArticleDetailStatus::select('product_stocks.p_id as p_id')
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
        ->where('purchase_order_article_detail_statuses.id', '=', $id)
        ->groupBy('product_stocks.p_id')
        ->get()->first()->p_id;
        Product::where('id', '=', $p_id)->update([
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $update = PurchaseOrderArticleDetailStatus::where('id', $id)->update([
            'stkt_id' => $stkt_id,
            'poads_qty' => $poads_qty,
            'poads_discount' => $poads_discount,
            'poads_extra_discount' => $poads_extra_discount,
            'poads_purchase_price' => $poads_purchase_price,
            'poads_total_price' => $poads_total_price
        ]);
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
    
    public function deleteData(Request $request)
    {
        $id = $request->_id;
        $delete = PurchaseOrderArticleDetailStatus::where('id', $id)->delete();
        if (!empty($delete)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
    
    public function checkInvoice(Request $request)
    {
        $invoice = $request->post('invoice');
        $check = DB::table('purchase_order_article_detail_statuses')->where('poads_invoice', '=', $invoice)->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    private function uploadImage($image, $path)
    {
        $input['fileName'] = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path($path);
        $img = Image::make($image->path());
        $img->resize(400, 400, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$input['fileName']);
        return $input['fileName'];
    }
}
