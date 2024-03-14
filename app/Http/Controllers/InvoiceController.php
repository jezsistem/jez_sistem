<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\Customer;
use App\Models\Wilayah;
use App\Models\PaymentMethod;
use App\Models\Store;

class InvoiceController extends Controller
{
    protected function validateAccess()
    {
        $validate = DB::table('user_menu_accesses')
        ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
            'u_id' => Auth::user()->id,
            'ma_slug' => request()->segment(1)
        ])->exists();
        if (!$validate) {
            dd("Anda tidak memiliki akses ke menu ini, hubungi Administrator");
        }
    }

    protected function sidebar()
    {
        $ma_id = DB::table('user_menu_accesses')->select('ma_id')
        ->where('u_id', Auth::user()->id)->get();
        $ma_id_arr = array();
        if (!empty($ma_id)) {
            foreach ($ma_id as $row) {
                array_push($ma_id_arr, $row->ma_id);
            }
        }

        $sidebar = array();
        $mt = DB::table('menu_titles')->orderBy('mt_sort')->get();
        if (!empty($mt->first())) {
            foreach ($mt as $row) {
                $ma = DB::table('menu_accesses')
                ->where('mt_id', '=', $row->id)
                ->whereIn('id', $ma_id_arr)
                ->orderBy('ma_sort')->get();
                if (!empty($ma->first())) {
                    $row->ma = $ma;
                    array_push($sidebar, $row);
                }
            }
        }
        return $sidebar;
    }
    
    public function index()
    {
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.invoice.invoice', compact('data'));
    }

    public function checkInvoice(Request $request)
    {
        $invoice = $request->invoice;
        $check = PosTransaction::where(['pos_invoice' => $invoice])->exists();
        $get_invoice = array();
        $dropshipper = null;
        $customer = null;
        $cust_province = '';
        $cust_city = '';
        $cust_subdistrict = '';
        if ($check) {
            $check_transaction = PosTransaction::select('pos_transactions.id as pt_id', 'is_website', 'pos_unique_code', 'pos_courier', 'cust_id', 'sub_cust_id', 'cr_name', 'u_name', 'pm_name', 'dv_name', 'pos_ref_number', 'pos_card_number', 'pos_another_cost', 'cust_name', 'cust_phone', 'cust_address', 'pos_invoice', 'st_phone', 'st_address', 'pos_shipping', 'cr_id', 'pos_transactions.created_at as pos_created')
            ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
            ->leftJoin('couriers', 'couriers.id', '=', 'pos_transactions.cr_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pos_transactions.pm_id')
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'pos_transactions.stt_id')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
            ->where(['pos_invoice' => $invoice, 'pos_transactions.stt_id' => 1])->get();
            foreach ($check_transaction as $row) {
                if (!empty($row->cust_id) AND !empty($row->sub_cust_id)) {
                    $dropshipper = Customer::where('id', $row->cust_id)->get()->first();
                    $customer = Customer::where('id', $row->sub_cust_id)->get()->first();
                } else {
                    $customer = Customer::where('id', $row->cust_id)->get()->first();
                }
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
                $check_transaction_detail = PosTransactionDetail::
                leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(['pt_id' => $row->pt_id])
                ->where('pos_td_reject', '!=', '1')->get();
                if (!empty($check_transaction_detail)) {
                    $row->subitem = $check_transaction_detail;
                    array_push($get_invoice, $row);
                }
            }
        }
        $data = [
            'title' => 'Invoice '.$invoice,
            'invoice' => $invoice,
            'dropshipper' => $dropshipper,
            'customer' => $customer,
            'cust_province'=> $cust_province,
            'cust_city' => $cust_city,
            'cust_subdistrict' => $cust_subdistrict,
            'invoice_data' => $get_invoice,
            'segment' => request()->segment(1)
        ];
        return view('app.invoice.invoice', compact('data'));
    }

    public function checkOfflineInvoice(Request $request)
    {
        $invoice = $request->invoice;
        $check = PosTransaction::where(['pos_invoice' => $invoice])->exists();
        $get_invoice = array();
        $customer = null;
        if ($check) {
            $check_transaction = PosTransaction::select('pos_transactions.id as pt_id', 'cust_id', 'sub_cust_id', 'cr_name', 'u_name', 'pm_name', 'dv_name', 'pos_ref_number', 'pos_card_number', 'cust_name', 'cust_phone', 'cust_address', 'pos_invoice', 'st_phone', 'st_address', 'pos_shipping', 'cr_id', 'pos_transactions.created_at as pos_created')
            ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
            ->leftJoin('couriers', 'couriers.id', '=', 'pos_transactions.cr_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pos_transactions.pm_id')
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
            ->where(['pos_invoice' => $invoice])->get();
            foreach ($check_transaction as $row) {
                if (!empty($row->cust_id)) {
                    $customer = Customer::where('id', $row->cust_id)->get()->first();
                }
                $check_transaction_detail = PosTransactionDetail::
                leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(['pt_id' => $row->pt_id])->get();
                if (!empty($check_transaction_detail)) {
                    $row->subitem = $check_transaction_detail;
                    array_push($get_invoice, $row);
                }
            }
        }
        $data = [
            'title' => 'Invoice '.$invoice,
            'invoice' => $invoice,
            'customer' => $customer,
            'invoice_data' => $get_invoice,
            'segment' => request()->segment(1)
        ];
        return view('app.invoice.offline_invoice', compact('data'));
    }

    public function printInvoice(Request $request)
    {
        $invoice = $request->invoice;
        $check = PosTransaction::where(['pos_invoice' => $invoice])->exists();
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
                'pos_total_discount')
            ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
            ->leftJoin('couriers', 'couriers.id', '=', 'pos_transactions.cr_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pos_transactions.pm_id')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
            ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
            ->leftJoin('pos_transaction_details', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->where(['pos_invoice' => $invoice])
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


        $data = [
            'title' => 'Invoice '.$invoice,
            'invoice' => $invoice,
            'dropshipper' => $dropshipper,
            'customer' => $customer,
            'cust_province'=> $cust_province,
            'cust_city' => $cust_city,
            'cust_subdistrict' => $cust_subdistrict,
            'transaction' => $transaction,
            'transaction_detail' => $transaction_detail,
            'segment' => request()->segment(1)
        ];

        return view('app.invoice.print_invoice', compact('data'));
    }

    public function checkSync(Request $request) {
        $id = $request->get('id');
        $plst = DB::table('product_location_setup_transactions')
        ->where('pt_id', '=', $id)->count('id');

        $ptd = DB::table('pos_transaction_details')
        ->where('pt_id', '=', $id)->count('id');

        if ($plst > $ptd) {
            $dt = DB::table('product_location_setup_transactions')
            ->select('pl_id', 'pst_id', 'plst_qty', 'ps_sell_price', 'p_sell_price', 'ps_price_tag', 'p_price_tag')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->groupBy('product_location_setup_transactions.id')
            ->where('pt_id', '=', $id)->get();
            if (!empty($dt->first())) {
                foreach ($dt as $row) {
                    $exist = DB::table('pos_transaction_details')->where([
                        'pt_id' => $id,
                        'pl_id' => $row->pl_id,
                        'pst_id' => $row->pst_id,
                        'pos_td_qty' => $row->plst_qty
                    ])->exists();
                    if (!$exist) {
                        $std_id = '17';
                        $set_discount_check = DB::table('product_discount_details')->select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                        ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                        ->where('pst_id', '=', $row->pst_id)
                        ->where('std_id', '=', $std_id)
                        ->where('product_discounts.st_id', '=', Auth::user()->st_id)
                        ->where('pd_type', '=', 'b1g1')
                        ->where('pd_date', '>=', date('Y-m-d'))
                        ->orderByDesc('product_discount_details.created_at')
                        ->exists();
                        if ($set_discount_check) {
                            $set_discount = DB::table('product_discount_details')->select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                            ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                            ->where('pst_id', '=', $row->pst_id)
                            ->where('std_id', '=', $std_id)
                            ->where('product_discounts.st_id', '=', Auth::user()->st_id)
                            ->where('pd_type', '=', 'b1g1')
                            ->where('product_discounts.pd_date', '>=', date('Y-m-d'))
                            ->orderByDesc('product_discount_details.created_at')
                            ->first();
                        } else {
                            $set_discount = DB::table('product_discount_details')->select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                            ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                            ->where('pst_id', '=', $row->pst_id)
                            ->where('pd_date', '>=', date('Y-m-d'))
                            ->where('product_discounts.st_id', '=', Auth::user()->st_id)
                            ->where('std_id', '=', $std_id)->where('pd_type', '!=', 'b1g1')
                            ->orderByDesc('product_discount_details.created_at')
                            ->first();
                            if (empty($set_discount)) {
                                $set_discount = DB::table('product_discount_details')->select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                                ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                                ->where('pst_id', '=', $row->pst_id)
                                ->whereNull('product_discounts.st_id')
                                ->where('pd_date', '>=', date('Y-m-d'))
                                ->where('std_id', '=', $std_id)->where('pd_type', '!=', 'b1g1')
                                ->orderByDesc('product_discount_details.created_at')
                                ->first();
                            }
                        }
                        if (!empty($row->ps_sell_price)) {
                            $sell_price = $row->ps_sell_price;
                        } else {
                            $sell_price = $row->p_sell_price;
                        }
                        if (!empty($set_discount)) {
                            if (date('Y-m-d') <= $set_discount->pd_date) {
                                if (!empty($row->ps_price_tag)) {
                                    $price_tag = $row->ps_price_tag;
                                } else {
                                    $price_tag = $row->p_price_tag;
                                }
                                if ($set_discount->pd_type == 'percent') {
                                    $sell_price = $price_tag - ($price_tag/100 * $set_discount->pd_value);
                                } else if ($set_discount->pd_type == 'amount') {
                                    $sell_price = $price_tag - $set_discount->pd_value;
                                } else {
                                    $sell_price = $price_tag;
                                }
                            }
                        }
                        DB::table('pos_transaction_details')->insert([
                            'pt_id' => $id,
                            'pl_id' => $row->pl_id,
                            'pst_id' => $row->pst_id,
                            'pos_td_qty' => $row->plst_qty,
                            'pos_td_sell_price' => $sell_price,
                            'pos_td_discount_price' => ($row->plst_qty*$sell_price),
                            'pos_td_nameset' => '0',
                            'pos_td_total_price' => ($row->plst_qty*$sell_price),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        }
        $r['status'] = 200;
        return json_encode($r);
    }

    public function printOfflineInvoice(Request $request)
    {
        $invoice = $request->invoice;
        $check = PosTransaction::where(['pos_invoice' => $invoice])->exists();
        $get_invoice = array();
        $dropshipper = null;

        if ($check) {
            $trx = PosTransaction::select(
                'pos_transactions.id as pt_id', 'cust_id', 'pos_cc_charge', 'cust_province', 'cust_city',
                'cust_subdistrict', 'sub_cust_id', 'u_name', 'pm_name', 'pm_id_partial', 'dv_name', 'cr_name',
                'pos_another_cost', 'pos_payment', 'pos_payment_partial', 'pos_ref_number', 'pos_card_number',
                'cust_name', 'cust_phone', 'cust_address', 'pos_invoice', 'st_name', 'st_phone', 'st_address',
                'pos_shipping', 'cr_id', 'pos_transactions.created_at as pos_created',
                'pos_transactions.pos_total_vouchers', 'pos_total_discount')
            ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
            ->leftJoin('couriers', 'couriers.id', '=', 'pos_transactions.cr_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pos_transactions.pm_id')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
            ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
            ->where(['pos_invoice' => $invoice])
            ->first();

            $check_transaction_detail = PosTransactionDetail::
            leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where(['pt_id' => $trx->pt_id])->get();
            if (!empty($check_transaction_detail)) {
                $trx->subitem = $check_transaction_detail;
                if (!empty($trx->pm_id_partial)) {
                    $trx->pm_name_partial = PaymentMethod::select('pm_name')
                    ->where('id', $trx->pm_id_partial)->get()->first()->pm_name;
                }
                array_push($get_invoice, $trx);
            }
        }
        $stores = Auth::user()->st_id;

        $data_stores = Store::where('id', $stores)->get()->first();

        $stores_code = $data_stores->st_code;
        $data = [
            'title' => 'Invoice '.$invoice,
            'invoice' => $invoice,
            'invoice_data' => $get_invoice,
            'store_code' => $stores_code,
            'segment' => request()->segment(1)
        ];

        return view('app.invoice.print_invoice_offline', compact('data'));
    }
    
    public function searchInvoice (Request $request)
    {
        $invoice = $request->invoice;
        $invoice_data = '';
        $pt_id = PosTransaction::select('id')->where('pos_invoice', '=', $invoice)->get()->first();
        if (!empty($pt_id)) {
          $invoice_pt_id = PosTransaction::select('pos_invoice')->where('pt_id_ref', '=', $pt_id->id)->get();
          if (!empty($invoice_pt_id->first())) {
            foreach ($invoice_pt_id as $row) {
              $invoice_data .= $row->pos_invoice.' ';
            }
            $r['invoice'] = $invoice_data;
            $r['status'] = '200';
          } else {
            $r['status'] = '400';
          }
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }
}
