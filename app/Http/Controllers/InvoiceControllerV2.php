<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceControllerV2 extends Controller
{
    public function printInvoice(Request $request)
    {
        $pt_id = decrypt($request->id);
        
        $check = PosTransaction::where(['id' => $pt_id])->exists();
        $dropshipper = null;
        $customer = null;
        $cust_province = null;
        $cust_city = null;
        $cust_subdistrict = null;
        $transaction = null;
        $transaction_detail = null;
        $cust_province = '';
        $cust_city = '';
        $cust_subdistrict = '';
        if ($check) {
            $transaction = PosTransaction::select(
                'pos_discount', 'is_website', 'pos_unique_code', 'pos_courier',
                'pos_transactions.id as pt_id', 'cust_id', 'cust_province', 'cust_city',
                'cust_subdistrict', 'sub_cust_id', 'u_name', 'pm_name', 'dv_name', 'cr_name',
                'pos_another_cost', 'pos_ref_number', 'pos_card_number', 'cust_name', 'cust_phone',
                'cust_address', 'pos_invoice', 'st_name', 'st_phone', 'st_address', 'pos_shipping',
                'cr_id', 'pos_transactions.created_at as pos_created',
                'pos_total_discount', 'pos_order_number','pos_payment', 'pos_real_price', 'pos_discount_seller')
            ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
            ->leftJoin('couriers', 'couriers.id', '=', 'pos_transactions.cr_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pos_transactions.pm_id')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
            ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
            ->leftJoin('pos_transaction_details', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->where(['pos_transactions.id' => $pt_id])
            ->groupBy('pos_transactions.id')->get()->first();
            if (!empty($transaction)) {
                if (!empty($transaction->cust_id) AND !empty($transaction->sub_cust_id)) {
                    $dropshipper = Customer::select('cust_name', 'cust_address', 'cust_phone', 'cust_store', 'cust_province', 'cust_city', 'cust_subdistrict')->where('id', $transaction->cust_id)->get()->first();
                    $customer = Customer::select('cust_name', 'cust_address', 'cust_phone', 'cust_store', 'cust_province', 'cust_city', 'cust_subdistrict')->where('id', $transaction->sub_cust_id)->get()->first();
                } else {
                    $customer = Customer::select('cust_name', 'cust_address', 'cust_phone', 'cust_store', 'cust_province', 'cust_city', 'cust_subdistrict', 'cust_city_ro_id', 'cust_subdistrict_ro_id')->where('id', $transaction->cust_id)->get()->first();
                }
                if (!empty($customer)) {
                if (!empty($customer->cust_city_ro_id)) {
                  $cust_province = DB::table('ro_provinces')->select('province_name')
                  ->leftJoin('ro_cities', 'ro_cities.province_id', '=', 'ro_provinces.province_id')->where('city_id', '=', $customer->cust_city_ro_id)->get()->first()->province_name;
                  $cust_city = DB::table('ro_cities')->select('city_name')->where('city_id', '=', $customer->cust_city_ro_id)->get()->first()->city_name;
                  $cust_subdistrict = DB::table('ro_subdistricts')->select('subdistrict_name')->where('subdistrict_id', '=', $customer->cust_subdistrict_ro_id)->get()->first()->subdistrict_name;
                } else {
                    if (!empty($customer->cust_subdistrict)) {
                  $cust_province = Wilayah::select('nama')->where('kode', $customer->cust_province)->get()->first()->nama;
                  $cust_city = Wilayah::select('nama')->where('kode', $customer->cust_city)->get()->first()->nama;
                  $cust_subdistrict = Wilayah::select('nama')->where('kode', $customer->cust_subdistrict)->get()->first()->nama;
                    }
                }
                }
                $transaction_detail = PosTransactionDetail::
                leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(['pt_id' => $transaction->pt_id])
                ->where('pos_td_reject', '!=', '1')
                ->with('productStock')
                ->get();
            }
        }

        //get discount platform from online transactions

        $discount_platform = DB::table('online_transactions')
        ->join('online_transaction_details', 'online_transaction_details.to_id', '=', 'online_transactions.id')
        ->selectRaw('SUM(ts_online_transaction_details.discount_platform) as total_discount_platform')
        ->where('online_transactions.order_number', $transaction->pos_order_number)->first()->total_discount_platform;

        if (!$discount_platform) {
            $discount_platform = 0;
        }

        $data = [
            'title' => 'Invoice '.$pt_id,
            'invoice' => $transaction->pos_invoice,
            'dropshipper' => $dropshipper,
            'customer' => $customer,
            'cust_province'=> $cust_province,
            'cust_city' => $cust_city,
            'cust_subdistrict' => $cust_subdistrict,
            'transaction' => $transaction,
            'transaction_detail' => $transaction_detail,
            'discount_platform' => $discount_platform,
            'segment' => request()->segment(1)
        ];

        return view('app.invoice.print_invoice', compact('data'));
    }
}
