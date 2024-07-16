<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserShift;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\StoreTypeDivision;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\ProductLocation;
use App\Models\ProductLocationSetup;
use App\Models\ProductLocationSetupTransaction;
use App\Models\PurchaseOrderArticleDetailStatus;
use App\Models\PaymentMethod;
use App\Models\Courier;
use App\Models\CardProvider;
use App\Models\ProductDiscount;
use App\Models\ProductDiscountDetail;
use App\Models\ExceptionLocation;
use App\Models\BuyOneGetOne;
use App\Models\UserRating;

class PointOfSaleController extends Controller
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
        $store = Store::where('id', Auth::user()->st_id)->get()->first();
        //        dd(Auth::user()->st_id);
        $payment_method = PaymentMethod::where('pm_delete', '!=', '1')->where('st_id', Auth::user()->st_id)->orderByDesc('pm_name')->pluck('pm_name', 'id');

        $time_start = UserShift::where('start_time', '!=', null)
            ->where('end_time', null)
            ->where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')->first();
        $data = [
            'app_title' => 'JEZ SYSTEM',
            'title' => 'POINT OF SALE',
            'user' => $user_data,
            'store' => $store,
            'starting_date' => $time_start,
            'pt_id' => null,
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'std_id' => StoreTypeDivision::where('dv_delete', '!=', '1')->orderByDesc('id')->pluck('dv_name', 'id'),
            'cust_id' => Customer::selectRaw('id, CONCAT(cust_name," (",cust_phone,")") as name')
                ->where('cust_delete', '!=', '1')
                ->orderBy('cust_name')->pluck('name', 'id'),
            'ct_id' => CustomerType::where('ct_delete', '!=', '1')->orderByDesc('id')->pluck('ct_name', 'id'),
            'cp_id' => CardProvider::orderBy('cp_name')->pluck('cp_name', 'id'),
            'segment' => request()->segment(1),
            'p_id' => ProductStock::selectRaw('ts_product_stocks.id as pst_id, CONCAT(p_name," (",ps_barcode,")") as product')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->where('p_delete', '!=', '1')
                ->orderByDesc('p_name')->pluck('product', 'pst_id'),
            'payment_method' => $payment_method,
            'courier' => Courier::where('cr_delete', '!=', '1')->orderByDesc('cr_name')->pluck('cr_name', 'id'),
            'cust_province' => DB::table('wilayah')->select('kode', 'nama')->whereRaw('length(kode) = 2')->orderBy('nama')->pluck('nama', 'kode'),
            'b1g1_bin' => DB::table('buy_one_get_ones')->select('product_locations.id as id', 'pl_code')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'buy_one_get_ones.pl_id')->orderBy('pl_code')->pluck('pl_code', 'id'),
            'pst_custom' => ProductStock::where('ps_barcode', '=', 'CUSTOM')->first(),
            'psc_custom' => Product::where('p_name', 'LIKE', '%CUSTOM%')->first(),
            'pl_custom' => ProductLocation::where('st_id', '=', Auth::user()->st_id)->where('pl_code', 'LIKE', '%TOKO%')->first(),
            'shift_status' => UserShift::where('user_id', '=', Auth::user()->id)->whereNotNull('start_time')->whereNull('end_time')->where('created_at', 'LIKE', date('Y-m-d') . '%')->orderBy('id', 'DESC')->count(),
            //            'kasir' => DB::table('users')->select('id', 'u_name')->where('st_id', '=', Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'))->pluck('id', 'u_name')
            'kasir' => User::where('st_id', Auth::user()->st_id)->pluck('id', 'u_name')
        ];

        if ($data['shift_status'] > 0) {
            $var = 1;
        } else {
            $var = 0;
        }
        //        dd($data['kasir']);


        if (strtolower($user_data->stt_name) == 'online') {
            return view('app.pos.pos', compact('data'));
        } else {
            return view('app.offline_pos.offline_pos', compact('data'));
        }
    }

    public function detailShift(Request $request)
    {
        try {

            $data['name'] = Auth::user()->u_name;
            $data['start_time'] = $request->start_time_original;
            $data['end_time'] = '';
            $data['store'] = $request->st_name;
            $data['total_pos_real_price'] = '';
            $data['total_pos_payment_price'] = '';
            $data['laba_shift'] = '';
            $data['difference'] = '';
            $data['user_id'] = Auth::user()->id;
            $data['st_id'] = Auth::user()->st_id;
            $data['st_name'] = Auth::user()->st_name;

            $currencyValue = $request->laba_shift;
            $laba_shift = (int)preg_replace('/\D/', '', $currencyValue);
            $data['laba_shift'] = $laba_shift;

            $paymentMethods = PaymentMethod::select(
                'payment_methods.pm_name',
                'pos_transactions.pos_payment_partial',
                'payment_methods.id as pm_id',
                DB::raw('SUM(CASE WHEN ts_pos_transactions.st_id = ts_payment_methods.st_id AND ts_pos_transactions.stt_id = ts_payment_methods.stt_id AND ts_pos_transactions.pos_refund = "0" THEN ts_pos_transactions.pos_payment_partial ELSE 0 END) as total_pos_payment_partials'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.st_id = ts_payment_methods.st_id THEN ts_pos_transactions.pos_payment_partial ELSE 0 END) as total_pos_partials'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.st_id = ts_payment_methods.st_id AND ts_pos_transactions.stt_id = ts_payment_methods.stt_id AND ts_pos_transactions.pos_refund = "0" THEN ts_pos_transactions.pos_payment ELSE 0 END) as total_pos_payment'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.st_id = ts_payment_methods.st_id AND ts_pos_transactions.stt_id = ts_payment_methods.stt_id AND ts_pos_transactions.pos_refund = "1" THEN ts_pos_transactions.pos_payment ELSE 0 END) as total_pos_payment_refund'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.st_id = ts_payment_methods.st_id AND ts_pos_transactions.stt_id = ts_payment_methods.stt_id AND ts_pos_transactions.pos_refund = "0" THEN ts_pos_transactions.pos_real_price ELSE 0 END) as total_pos_payment_expected'),
            )
                ->join('pos_transactions', 'pos_transactions.pm_id', '=', 'payment_methods.id')
                ->where('pos_transactions.u_id', '=', $data['user_id'])
                ->where('pos_transactions.st_id', "=", $data['st_id'])
                //                ->join('user_shifts', 'pos_transactions.u_id', '=', 'user_shifts.user_id')
                ->whereBetween('pos_transactions.created_at', [$data['start_time'], Carbon::now()])
                ->groupBy('payment_methods.id')
                ->get();
            //            return $paymentMethods;
            $cashMethods = [];
            $methodsPartials = [];
            $bcaMethods = [];
            $bniMethods = [];
            $briMethods = [];

            $transferBca = [];
            $transferBni = [];
            $transferBri = [];

            $PaymentPartials = PosTransaction::where('u_id', $data['user_id'])
                ->join('payment_methods', 'payment_methods.id', '=', 'pos_transactions.pm_id')
                ->where('pos_transactions.u_id', '=', $data['user_id'])
                ->where('pos_transactions.st_id', $data['st_id'])
                ->whereBetween('pos_transactions.created_at', [$data['start_time'], $data['end_time']])
                ->get();

            foreach ($paymentMethods as $paymentMethod) {
                if (stripos($paymentMethod->pm_name, 'CASH') !== false) {
                    $cashMethods = $paymentMethod;
                }
                if (stripos($paymentMethod->pm_name, 'EDC BCA') !== false) {
                    $bcaMethods = $paymentMethod;
                }
                if (stripos($paymentMethod->pm_name, 'EDC BNI') !== false) {
                    $bniMethods = $paymentMethod;
                }
                if (stripos($paymentMethod->pm_name, 'EDC BRI') !== false) {
                    $briMethods = $paymentMethod;
                }

                if (stripos($paymentMethod->pm_name, 'TRANSFER BCA') !== false) {
                    $transferBca = $paymentMethod;
                }
                if (stripos($paymentMethod->pm_name, 'TRANSFER BNI') !== false) {
                    $transferBni = $paymentMethod;
                }
                if (stripos($paymentMethod->pm_name, 'TRANSFER BRI') !== false) {
                    $transferBri = $paymentMethod;
                }
            }
            $total_sold_items = $this->totalProductSold($data, $request->id, $data['start_time'], $data['end_time']) ?? 0;
            $total_refund_items = $this->totalProductRefund($data, $request->id, $data['start_time'], $data['end_time']) ?? 0;

            // create total expected payment
            $total_expected_payment = 0;
            $total_actual_payment = 0;
            $total_payment_two = 0;
            foreach ($paymentMethods as $paymentMethod) {
                $total_expected_payment += $paymentMethod->total_pos_payment_expected;
                $total_actual_payment += $paymentMethod->total_pos_payment;
                $total_payment_two += $paymentMethod->total_pos_partials;
            }

            return view(
                'app.report.shift._shift_detail',
                compact(
                    'data',
                    'cashMethods',
                    'bcaMethods',
                    'bniMethods',
                    'briMethods',
                    'transferBca',
                    'transferBni',
                    'transferBri',
                    'total_sold_items',
                    'total_refund_items',
                    'total_payment_two',
                    'total_expected_payment',
                    'total_actual_payment',
                    'methodsPartials'
                )
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function refundReturDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(PosTransactionDetail::select(
                'pos_transaction_details.id as ptd_id',
                'product_stocks.id as pst_id',
                'pos_transactions.id as pt_id',
                'ps_barcode',
                'pos_invoice',
                'p_name',
                'pos_td_discount',
                'ps_qty',
                'pl_id',
                'p_color',
                'sz_name',
                'br_name',
                'pos_td_qty',
                'pos_td_total_price',
                'pos_td_sell_price',
                'pos_td_nameset_price',
                'pos_td_marketplace_price',
                'pos_transactions.created_at as p_created'
            )
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('pos_transactions.id', '=', $request->pt_id))
                ->editColumn('article', function ($data) {
                    return '<span class="btn-sm btn-primary" style="white-space: nowrap;">[' . $data->br_name . '] ' . $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name . '</span>';
                })
                ->editColumn('datetime', function ($data) {
                    return '<span style="white-space: nowrap;">' . date('d-m-Y H:i:s', strtotime($data->p_created)) . '</span>';
                })
                ->editColumn('qty', function ($data) {
                    return $data->pos_td_qty;
                })
                ->editColumn('price', function ($data) {
                    return number_format($data->pos_td_total_price);
                })
                ->editColumn('action', function ($data) use ($request) {
                    $pls_id = ProductLocationSetup::select('id')->where('pst_id', $data->pst_id)->where('pl_id', $data->pl_id)->get()->first()->id;
                    $plst_id = ProductLocationSetupTransaction::select('id', 'plst_status', 'plst_qty')
                        ->whereIn('plst_status', ['DONE', 'COMPLAINT'])
                        ->where('pls_id', $pls_id)
                        ->where('pt_id', $request->pt_id)->get()->first();
                    if (!empty($plst_id)) {
                        if ($plst_id->plst_status == 'DONE') {
                            return '<input class="form-control col-md-6" data-discount="' . $data->pos_td_discount . '" data-total_price="' . $data->pos_td_total_price . '" data-item_qty="' . $data->pos_td_qty . '" data-pt_id="' . $data->pt_id . '" data-marketplace_price="' . $data->pos_td_marketplace_price . '" data-nameset_price="' . $data->pos_td_nameset_price . '" data-sell_price="' . $data->pos_td_sell_price . '" data-p_name="[' . $data->br_name . '] ' . $data->article_id . ' ' . $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name . '" data-plst_id="' . $plst_id->id . '" data-pl_id="' . $data->pl_id . '" data-ps_qty="' . $data->ps_qty . '" data-pst_id="' . $data->pst_id . '" data-ptd_id="' . $data->ptd_id . '" type="checkbox" id="select_refund_exchange_item"/>';
                        } else {
                            return '<input class="form-control col-m d-6" data-discount="' . $data->pos_td_discount . '" data-total_price="' . $data->pos_td_total_price . '" data-item_qty="' . $data->pos_td_qty . '" data-pt_id="' . $data->pt_id . '" data-marketplace_price="' . $data->pos_td_marketplace_price . '" data-nameset_price="' . $data->pos_td_nameset_price . '" data-sell_price="' . $data->pos_td_sell_price . '" data-p_name="[' . $data->br_name . '] ' . $data->article_id . ' ' . $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name . '" data-plst_id="' . $plst_id->id . '" data-pl_id="' . $data->pl_id . '" data-ps_qty="' . $data->ps_qty . '" data-pst_id="' . $data->pst_id . '" data-ptd_id="' . $data->ptd_id . '" type="checkbox" id="select_refund_exchange_item" checked/>';
                        }
                    } else {
                        return '-';
                    }
                })
                ->rawColumns(['article', 'datetime', 'qty', 'price', 'action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function reloadRefund()
    {
        $data = [
            'invoice' => PosTransaction::select('pos_transactions.id as p_id', 'pos_invoice', 'plst_status', 'pos_refund', 'pos_status', 'pos_transactions.created_at')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->where('st_id', '=', Auth::user()->st_id)
                ->where('pos_refund', '!=', '1')
                ->whereIn('pos_status', ['DONE', 'SHIPPING NUMBER', 'IN DELIVERY'])
                ->whereIn('plst_status', ['DONE', 'COMPLAINT', 'WAITING FOR PACKING'])
                ->orderBy('pos_transactions.created_at')->pluck('pos_invoice', 'p_id'),
        ];
        return view('app.pos._reload_refund', compact('data'));
    }

    public function reloadRefundOffline()
    {
        $data = [
            'invoice' => PosTransaction::select('id', 'pos_invoice', 'created_at')
                //            ->whereRaw('created_at  >= now() - INTERVAL 7 DAY')
                ->whereIn('stt_id', ['1', '2'])
                ->where('st_id', '=', Auth::user()->st_id)
                ->where('pos_refund', '!=', '1')
                ->whereIn('pos_status', ['DONE'])
                ->orderBy('created_at')->pluck('pos_invoice', 'id'),
        ];

        return view('app.offline_pos._reload_refund', compact('data'));
    }

    public function refundExchangeList(Request $request)
    {
        $type = $request->_type;
        $plst_id = $request->_plst_id;
        $pt_id = $request->_pt_id;
        if ($type == 'add') {
            $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('pt_id', $pt_id)->where('plst_status', '=', 'DONE')->update([
                'plst_status' => 'COMPLAINT',
                'u_id_refund' => Auth::user()->id
            ]);
        } else {
            $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('pt_id', $pt_id)->where('plst_status', '=', 'COMPLAINT')->update([
                'plst_status' => 'DONE',
                'u_id_refund' => ''
            ]);
        }
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkComplaint()
    {
        $check = ProductLocationSetupTransaction::select('pos_invoice', 'plst_status')
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'product_location_setup_transactions.pt_id')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->where('product_locations.st_id', '=', Auth::user()->st_id)
            ->where('product_location_setup_transactions.u_id_refund', '=', Auth::user()->id)
            ->where('plst_status', '=', 'COMPLAINT')
            ->where('pls_qty', '>=', '0')
            ->get();
        $invoice = '';
        if (!empty($check)) {
            foreach ($check as $row) {
                $invoice .= $row->pos_invoice . ' ';
            }
        }
        if (!empty($invoice)) {
            $r['invoice'] = $invoice;
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkOfflineComplaint()
    {
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

        $check = ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id', 'product_discounts.st_id as st_id', 'pd_date', 'pd_type', 'pd_value', 'pt_id', 'p_name', 'br_name', 'p_color', 'p_sell_price', 'p_price_tag', 'ps_price_tag', 'ps_sell_price', 'sz_name', 'ps_qty', 'plst_qty', 'pls_qty', 'product_stocks.id as pst_id', 'product_locations.id as pl_id')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_discount_details', 'product_discount_details.pst_id', '=', 'product_stocks.id')
            ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
            ->where('product_locations.st_id', '=', Auth::user()->st_id)
            ->where('product_location_setup_transactions.u_id_refund', '=', Auth::user()->id)
            ->where('plst_status', '=', 'COMPLAINT')
            ->where('pls_qty', '>=', '0')
            ->whereNotIn('pl_code', $exception)
            ->groupBy('product_stocks.id')
            ->get();
        if (!empty($check)) {
            $data = [
                'pos_complaint' => $check,
            ];
        } else {
            $data = [
                'pos_complaint' => null,
            ];
        }
        return view('app.offline_pos._reload_complaint', compact('data'));
    }

    public function checkBarcode(Request $request)
    {
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();


        $barcode = $request->_barcode;
        $type = $request->type;
        $std_id = $request->_std_id;
        $check_barcode = ProductStock::where('ps_qty', '>', '0')->where('ps_barcode', $barcode)->exists();
        if ($check_barcode) {
            $check = ProductStock::select('p_name', 'br_name', 'p_color', 'p_sell_price', 'p_price_tag', 'ps_price_tag', 'ps_sell_price', 'sz_name', 'ps_qty', 'product_stocks.id as pst_id')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->join('brands', 'brands.id', '=', 'products.br_id')
                ->join('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('ps_barcode', $barcode)
                ->where('product_locations.st_id', '=', Auth::user()->st_id)
                ->where('pls_qty', '>', '0')
                ->whereNotIn('pl_code', $exception)
                ->get();
            if (!empty($check)) {
                $check_setup = ProductLocationSetup::select('product_locations.id as pl_id', 'product_location_setups.id as pls_id', 'pl_code', 'pl_name', 'pls_qty')
                    ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->where('pst_id', $check->first()->pst_id)
                    ->where('pls_qty', '>', '0')
                    ->whereNotIn('pl_code', $exception)->get();
                $bin = '';
                if (!empty($check_setup)) {
                    $bin .= '<select class="form-control col-10 mr-4" id="pl_id">';
                    foreach ($check_setup as $row) {
                        $bin .= '<option value="' . $row->pl_id . '">[' . strtoupper($row->pl_code) . '] [' . $row->pls_qty . ']</option>';
                    }
                    $bin .= '</select>';
                }
                $sell_price = 0;
                $sell_price_discount = 0;
                if ($type == 'RESELLER') {
                    if (!empty($check->first()->ps_price_tag)) {
                        $sell_price = $check->first()->ps_price_tag;
                    } else {
                        $sell_price = $check->first()->p_price_tag;
                    }
                } else {
                    if (!empty($check->first()->ps_sell_price)) {
                        $sell_price = $check->first()->ps_sell_price;
                    } else {
                        $sell_price = $check->first()->p_sell_price;
                    }
                }
                $set_discount = ProductDiscountDetail::select('pd_type', 'pd_value', 'std_id', 'pd_date')
                    ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                    ->where('pst_id', '=', $check->first()->pst_id)
                    ->where('std_id', '=', $std_id)->get()->first();
                if (!empty($set_discount)) {
                    if (date('Y-m-d') <= $set_discount->pd_date) {
                        if (!empty($check->first()->ps_price_tag)) {
                            $price_tag = $check->first()->ps_price_tag;
                        } else {
                            $price_tag = $check->first()->p_price_tag;
                        }
                        if ($set_discount->pd_type == 'percent') {
                            $sell_price_discount = $price_tag / 100 * $set_discount->pd_value;
                            $sell_price = $price_tag - ($price_tag / 100 * $set_discount->pd_value);
                        } else {
                            $sell_price_discount = $set_discount->pd_value;
                            $sell_price = $price_tag - $set_discount->pd_value;
                        }
                    }
                }
                $r['p_name'] = '[' . $check->first()->br_name . '] ' . $check->first()->p_name . ' ' . $check->first()->p_color . ' ' . $check->first()->sz_name;
                $r['ps_qty'] = $check->first()->ps_qty;
                $r['sell_price'] = $sell_price;
                $r['pst_id'] = $check->first()->pst_id;
                $r['bin'] = $bin;
                $r['status'] = '200';
            } else {
                $r['status'] = '419';
            }
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function reloadItemTotal(Request $request)
    {
        $pt_id = $request->_pt_id;
        $check_ptd = PosTransactionDetail::select('pos_td_qty', 'pos_td_total_price')->where([
            'pt_id' => $pt_id,
        ])->get();
        if (!empty($check_ptd)) {
            $total_item = 0;
            $total_price = 0;
            foreach ($check_ptd as $row) {
                $total_item += $row->pos_td_qty;
                $total_price += $row->pos_td_total_price;
            }
            $r['total_item'] = $total_item;
            $r['total_price'] = $total_price;
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function updateOngkir(Request $request)
    {
        $pt_id = $request->pt_id;
        $courier = $request->courier;
        $shipping_cost = $request->shipping_cost;
        $update = PosTransaction::where([
            'id' => $pt_id,
        ])->update(['pos_shipping' => $shipping_cost, 'pos_shipping_description' => $courier]);
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveTransaction(Request $request)
    {
        $pm_id = $request->_pm_id;
        $cp_id = $request->_cp_id;
        $std_id = $request->_std_id;
        $cust_id = $request->_cust_id;
        $cross = $request->_cross;
        if ($cust_id == null) {
            $cust_id = 1;
        }
        $st_id = $request->_st_id;
        $pt_id_complaint = $request->_pt_id_complaint;
        $exchange = $request->_exchange;
        if (!empty($pt_id_complaint)) {
            if ($exchange == 'true') {
                DB::table('pos_transactions')->where('id', $pt_id_complaint)->update([
                    'pos_refund' => '1',
                    'pos_status' => 'EXCHANGE'
                ]);
            } else {
                DB::table('pos_transactions')->where('id', $pt_id_complaint)->update([
                    'pos_refund' => '1',
                    'pos_status' => 'REFUND'
                ]);
            }
        }
        $sub_cust_id = $request->_sub_cust_id;
        $unique_code = $request->_unique_code;
        $real_price = $request->_real_price;
        $admin_cost = $request->_admin_cost;
        $another_cost = $request->_another_cost;
        $order_code = $request->_order_code;
        $shipping_cost = $request->_shipping_cost;
        $ref_number = $request->_ref_number;
        $pos_total_discount = $request->_total_discount_side;

        $rand = str_pad(rand(0, pow(10, 3) - 1), 3, '0', STR_PAD_LEFT);
        $cr_id = $request->_cr_id;
        $note = $request->_note;
        $prefix = WebConfig::select('config_value')->where('config_name', 'pos_prefix')->get()->first()->config_value;

        $store_prefix = Store::select('st_description')->where('id', '=', Auth::user()->st_id)->get()->first()->st_description;
        $div_prefix = StoreTypeDivision::select('dv_description')->where('id', '=', $std_id)->get()->first()->dv_description;
        $sorting_number = PosTransaction::where([
            'std_id' => $std_id,
            'st_id' => Auth::user()->st_id,
        ])->whereDate('created_at', date('Y-m-d'))->count('id');

        $invoice = $prefix . date('YmdHis') . $rand . $store_prefix . $div_prefix . ($sorting_number + 1);

        $stt_id = Auth::user()->stt_id;
        $u_id = Auth::user()->id;
        if ($cross == 'true') {
            $cross_order = '1';
            $st_id_ref = $st_id;
        } else {
            $cross_order = '0';
            $st_id_ref = null;
        }

        $st_omset = Auth::user()->st_id;

        $voc_pst_id = $request->voc_pst_id;
        $voc_value = $request->voc_value;
        $voc_id = $request->voc_id;

        $discount_seller = $request->_discount_seller;

        $insert_get_id = DB::table('pos_transactions')->insertGetId([
            'u_id' => $u_id,
            'st_id' => $st_omset,
            'stt_id' => $stt_id,
            'pm_id' => $pm_id,
            'cp_id' => $cp_id,
            'std_id' => $std_id,
            'cust_id' => $cust_id,
            'pt_id_ref' => $pt_id_complaint,
            'sub_cust_id' => $sub_cust_id,
            'pos_admin_cost' => $admin_cost,
            'pos_another_cost' => $another_cost,
            'pos_real_price' => $real_price,
            'pos_order_number' => $order_code,
            'pos_invoice' => $invoice,
            'pos_unique_code' => $unique_code,
            'pos_shipping' => $shipping_cost,
            'pos_ref_number' => $ref_number,
            'pos_total_discount' => $pos_total_discount,
            'pos_discount_seller' => $discount_seller,
            'cr_id' => $cr_id,
            'pos_note' => $note,
            'created_at' => date('Y-m-d H:i:s'),
            'pos_refund' => '0',
            'st_id_ref' => $st_id_ref,
            'cross_order' => $cross_order,
        ]);

        if (!empty($insert_get_id)) {
            if (!empty($voc_pst_id)) {
                $used_voucher = DB::table('voucher_transactions')->insert([
                    'vc_id' => $voc_id,
                    'pt_id' => $insert_get_id,
                    'u_id' => Auth::user()->id,
                    'cust_id' => $cust_id,
                    'pst_id' => $voc_pst_id,
                    'value' => $voc_value,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ]);
            }
            $r['status'] = '200';
            $r['pt_id'] = $insert_get_id;
            $r['invoice'] = $invoice;
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveTransactionOffline(Request $request)
    {
        try {
            $pm_id = $request->_pm_id;
            $pm_id_two = $request->_pm_id_two;
            $ur_id = $request->_ur_id;
            $std_id = $request->_std_id;
            $cust_id = $request->_cust_id;
            if ($cust_id == null) {
                $cust_id = 1;
            }
            $pt_id_complaint = $request->_pt_id_complaint;
            $exchange = $request->_exchange;
            $card_number = $request->_card_number;
            $ref_number = $request->_ref_number;
            $card_number_two = $request->_card_number_two;
            $ref_number_two = $request->_ref_number_two;
            $secret_code = $request->_access_code;
            $cp_id = $request->_cp_id;
            $cp_id_two = $request->_cp_id_two;
            $charge = $request->_charge;
            $unique_code = $request->_unique_code;
            $another_cost = $request->_another_cost;
            $admin_cost = $request->_admin_cost;
            if (empty($admin_cost)) {
                $admin_cost = 0;
            }
            $total_payment = $request->_total_payment;
            $real_price = $request->_real_price;
            $total_payment_two = $request->_total_payment_two;
            $note = $request->_note;
            $shipping_cost = $request->_shipping_cost;
            $cr_id = $request->_cr_id;
            $pos_total_discount = $request->_total_discount_side;
            $dp_checkbox = $request->_dp_checkBox;
            $cross = $request->_cross;            
            $st_id = $request->_st_id ?? Auth::user()->st_id;

            $rand = str_pad(rand(0, pow(10, 3) - 1), 3, '0', STR_PAD_LEFT);
            //            $u_id = User::select('id')->where('u_secret_code', $secret_code)->get()->first()->id;
            $u_id = Auth::user()->id;
            $prefix = WebConfig::select('config_value')->where('config_name', 'pos_prefix')->get()->first()->config_value;

            $store_prefix = Store::select('st_description')->where('id', '=', Auth::user()->st_id)->get()->first()->st_description;
            $div_prefix = StoreTypeDivision::select('dv_description')->where('id', '=', $std_id)->get()->first()->dv_description;
            $sorting_number = PosTransaction::where([
                'std_id' => $std_id,
                'st_id' => $st_id,
            ])->whereDate('created_at', date('Y-m-d'))->count('id');

            $invoice = $prefix . date('YmdHis') . $rand . $store_prefix . $div_prefix . ($sorting_number + 1);
            if ($std_id == '14' || $std_id == '18') {
                $stt_id = '1';
            } else {
                $stt_id = Auth::user()->stt_id;
            }
            if (!empty($pt_id_complaint)) {
                $check_cust_id = PosTransaction::select('cust_id')->where('id', $pt_id_complaint)->get()->first();
                if (!empty($check_cust_id)) {
                    $cust_id = $check_cust_id->cust_id;
                }
                if ($exchange == 'true') {
                    DB::table('pos_transactions')->where('id', $pt_id_complaint)->update([
                        'pos_refund' => '1',
                        'pos_status' => 'EXCHANGE'
                    ]);
                } else {
                    DB::table('pos_transactions')->where('id', $pt_id_complaint)->update([
                        'pos_refund' => '1',
                        'pos_status' => 'REFUND'
                    ]);
                }
            }

            $voc_pst_id = $request->voc_pst_id;
            $voc_value = $request->voc_value;
            $voc_id = $request->voc_id;
            $total_voc_value = $request->_pos_total_vouchers;

            if ($cross == 'true') {
                $cross_order = '1';
                $st_id_ref = $st_id;
            } else {
                $cross_order = '0';
                $st_id_ref = null;
            }

            $insert_get_id = DB::table('pos_transactions')->insertGetId([
                'u_id' => $u_id,
                'st_id' => Auth::user()->st_id,
                'stt_id' => $stt_id,
                'pm_id' => $pm_id,
                'pm_id_partial' => $pm_id_two,
                'cp_id' => $cp_id,
                'cp_id_partial' => $cp_id_two,
                'std_id' => $std_id,
                'cust_id' => $cust_id,
                'pt_id_ref' => $pt_id_complaint,
                'pos_unique_code' => $unique_code,
                'pos_another_cost' => $another_cost,
                'pos_admin_cost' => $admin_cost,
                'pos_real_price' => $real_price,
                'pos_invoice' => $invoice,
                'pos_card_number' => $card_number,
                'pos_card_number_two' => $card_number_two,
                'pos_ref_number' => $ref_number,
                'pos_ref_number_two' => $ref_number_two,
                'pos_cc_charge' => $charge,
                'pos_payment' => $total_payment,
                'pos_payment_partial' => $total_payment_two,
                'pos_shipping' => $shipping_cost,
                'pos_total_vouchers' => $total_voc_value,
                'pos_total_discount' => $pos_total_discount,
                'cr_id' => $cr_id,
                'pos_note' => $note,
                'created_at' => date('Y-m-d H:i:s'),
                'pos_refund' => '0',
                'st_id_ref' => $st_id_ref,
                'cross_order' => $cross_order,
            ]);


            if ($dp_checkbox == "true") {
                DB::table('pos_transactions')->where('id', $insert_get_id)->update([
                    'pos_status' => 'DP'
                ]);
            }

            if (!empty($ur_id)) {
                $update_rating = UserRating::where('id', '=', $ur_id)->update([
                    'pt_id' => $insert_get_id,
                    'ur_status' => 'DONE'
                ]);
            }
            if (!empty($insert_get_id)) {
                if (!empty($voc_pst_id)) {
                    $used_voucher = DB::table('voucher_transactions')->insert([
                        'vc_id' => $voc_id,
                        'pt_id' => $insert_get_id,
                        'u_id' => Auth::user()->id,
                        'cust_id' => $cust_id,
                        'pst_id' => $voc_pst_id,
                        'value' => $voc_value,
                        'created_at' => date('Y-m-d'),
                        'updated_at' => date('Y-m-d'),
                    ]);
                }
                $r['status'] = '200';
                $r['pt_id'] = $insert_get_id;
                $r['invoice'] = $invoice;
            } else {
                $r['status'] = '400';
            }
            return json_encode($r);
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    public function saveTransactionDetail(Request $request)
    {
        $pt_id = $request->_pt_id;
        $pt_id_complaint = $request->_pt_id_complaint;
        $exchange = $request->_exchange;
        $pl_id = $request->_pl_id;
        $pst_id = $request->_pst_id;
        $plst_id = $request->_plst_id;
        $price = $request->_price;
        $item_qty = $request->_item_qty;
        $cross = $request->_cross;
        if (empty($item_qty)) {
            $item_qty = 1;
        }
        $discount = $request->_discount;
        $discount_number = $request->_discount_number;
        $sell_price_item = $request->_sell_price_item;
        $marketplace_price = $request->_marketplace_price;
        $subtotal_item = $request->_subtotal_item;
        $nameset_price = $request->_nameset_price;
        $final_price = $request->_final_price;
        $voc_pst_id = $request->voc_pst_id;
        $voc_value = $request->voc_value;
        $price_item_discount = $request->_price_item_discount ?? 0;
        if (!empty($nameset_price)) {
            $nameset = '1';
        } else {
            $nameset = '0';
        }
        $pos_td_description = null;
        if (session()->get('voc_item') != Auth::user()->id . '-' . $pst_id) {
            if ($pst_id == $voc_pst_id) {
                session()->put('voc_item', Auth::user()->id . '-' . $pst_id);
                if ($item_qty > 1) {
                    $pos_td_discount_price = (($item_qty - 1) * $price) + $voc_value;
                    $pos_td_description = ($item_qty - 1) . " x " . $price . " | 1 x " . $voc_value . "";
                } else {
                    $pos_td_discount_price = $item_qty * $voc_value;
                    $price = $voc_value;
                    $pos_td_description = "1 x " . $voc_value . "";
                }
            } else {
                $pos_td_discount_price = $item_qty * $price;
            }
        } else {
            $pos_td_discount_price = $item_qty * $price;
        }

        $create = PosTransactionDetail::create([
            'pt_id' => $pt_id,
            'pst_id' => $pst_id,
            'pl_id' => $pl_id,
            'pos_td_qty' => $item_qty,
            'pos_td_sell_price' => $price,
            'pos_td_discount' => $discount,
            'pos_td_discount_number' => $discount_number,
            'pos_td_discount_price' => $pos_td_discount_price,
            'pos_td_marketplace_price' => $marketplace_price,
            'pos_td_nameset_price' => $nameset_price,
            'pos_td_nameset' => $nameset,
            'pos_td_description' => $pos_td_description,
            'pos_td_price_item_discount' => $price_item_discount,
            'pos_td_total_price' => $pos_td_discount_price + $nameset_price,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        if (!empty($create)) {
            $sold = DB::table('products')->select('products.id as p_id', 'sold')
                ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                ->where('product_stocks.id', '=', $pst_id)
                ->get()->first();
            if (!empty($sold)) {
                $update_sold = DB::table('products')->where('id', '=', $sold->p_id)
                    ->update([
                        'sold' => ($sold->sold + $item_qty)
                    ]);
            }
            $pls_id = ProductLocationSetup::select('id')->where('pst_id', $pst_id)->where('pl_id', $pl_id)->get()->first()->id;
            $check_plst = ProductLocationSetupTransaction::where([
                'id' => $plst_id,
                'plst_status' => 'COMPLAINT'
            ])->exists();
            if ($check_plst) {
                $plst_data = ProductLocationSetupTransaction::select('pls_id', 'u_id_helper', 'u_id_packer', 'pt_id')->where([
                    'id' => $plst_id,
                ])->get()->first();
                if ($final_price < 0 and $exchange != 'true') {
                    $insert = ProductLocationSetupTransaction::insert([
                        'pt_id' => $plst_data->pt_id,
                        'pls_id' => $plst_data->pls_id,
                        'u_id_packer' => $plst_data->u_id_packer,
                        'u_id_helper' => $plst_data->u_id_helper,
                        'u_id' => Auth::user()->id,
                        'u_id_refund' => Auth::user()->id,
                        'plst_qty' => abs($item_qty),
                        'plst_type' => 'OUT',
                        'plst_status' => 'REFUND',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $update = ProductLocationSetupTransaction::where([
                        'id' => $plst_id,
                        'pt_id' => $pt_id_complaint,
                        'plst_status' => 'COMPLAINT'
                    ])->update([
                        'plst_status' => 'DONE',
                    ]);
                } else {
                    $insert = ProductLocationSetupTransaction::insert([
                        'pt_id' => $plst_data->pt_id,
                        'pls_id' => $plst_data->pls_id,
                        'u_id_packer' => $plst_data->u_id_packer,
                        'u_id_helper' => $plst_data->u_id_helper,
                        'u_id' => Auth::user()->id,
                        'u_id_refund' => Auth::user()->id,
                        'plst_qty' => abs($item_qty),
                        'plst_type' => 'OUT',
                        'plst_status' => 'EXCHANGE',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $update = ProductLocationSetupTransaction::where([
                        'id' => $plst_id,
                        'pt_id' => $pt_id_complaint,
                        'plst_status' => 'COMPLAINT'
                    ])->update([
                        'plst_status' => 'DONE',
                    ]);
                }
            } else {
                $activity = DB::table('product_location_setup_transactions')->insert([
                    'pls_id' => $pls_id,
                    'u_id' => Auth::user()->id,
                    'pt_id' => $pt_id,
                    'plst_qty' => $item_qty,
                    'plst_type' => 'OUT',
                    'plst_status' => 'WAITING ONLINE',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                if ($cross != 'true') {
                    if ($nameset_price > 0) {
                        DB::table('pos_transactions')->where('id', $pt_id)->update([
                            'pos_status' => 'NAMESET',
                        ]);
                    } else {
                        $cnameset = DB::table('pos_transactions')->where('id', $pt_id)
                            ->where('pos_status', '=', 'NAMESET')->exists();
                        if (!$cnameset) {
                            DB::table('pos_transactions')->where('id', $pt_id)->update([
                                'pos_status' => 'SHIPPING NUMBER',
                            ]);
                        }
                    }
                    $product_setup_location_qty = ProductLocationSetup::select('pls_qty')->where('id', $pls_id)->get()->first()->pls_qty;
                    $product_setup_location = ProductLocationSetup::where('id', $pls_id)->update([
                        'pls_qty' => ($product_setup_location_qty - $item_qty)
                    ]);

                    //                    product_stock qty 9
                    //                    $products_stock = ProductLocationSetup::select('pst_id')->where('id', $pls_id)->get()->first()->pst_id;
                    //                    $products_stock_qty = ProductStock::select('ps_qty')->where('id', $products_stock)->get()->first()->ps_qty;
                    //                    $products_stock_qty_final = ProductStock::where('id', $products_stock)->update([
                    //                        'ps_qty' => ($products_stock_qty - $item_qty)
                    //                    ]);
                } else {
                    DB::table('pos_transactions')->where('id', $pt_id)->update([
                        'pos_status' => 'WAITING FOR CONFIRMATION',
                    ]);
                }
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveTransactionDetailOffline(Request $request)
    {
        $b1g1_setup = BuyOneGetOne::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'buy_one_get_ones.pl_id')->get()->toArray();
        $b1g1_setup = array_merge($b1g1_setup, [
            'pl_code' => 'TOKO'
        ]);

        $pt_id = $request->_pt_id;
        $plst_id = $request->_plst_id;
        $pt_id_complaint = $request->_pt_id_complaint;
        $pl_id = $request->_pl_id;
        $pst_id = $request->_pst_id;
        $item_qty = $request->_item_qty;
        if (empty($item_qty)) {
            $item_qty = 1;
        }
        $price = $request->_price;
        $discount = $request->_discount;
        $discount_number = $request->_discount_number;
        $sell_price_item = $request->_sell_price_item;
        $nameset_price = $request->_nameset_price;
        $subtotal_item = $request->_subtotal_item;
        $secret_code = $request->_access_code;
        $final_price = $request->_final_price;
        $exchange = $request->_exchange;
        $voc_pst_id = $request->voc_pst_id;
        $voc_value = $request->voc_value;
        if (!empty($nameset_price)) {
            $nameset = '1';
        } else {
            $nameset = '0';
        }
        if($nameset_price == NULL){
            $nameset_price = 0;
        }
        $u_id = User::select('id')->where('u_secret_code', $secret_code)->get()->first()->id;
        $pl_code = ProductLocation::select('pl_code')->where('id', $pl_id)->get()->first()->pl_code;
        $pls_id = ProductLocationSetup::select('id')->where('pst_id', $pst_id)->where('pl_id', $pl_id)->get()->first()->id;
        $check_plst = ProductLocationSetupTransaction::where([
            'id' => $plst_id,
            'plst_status' => 'COMPLAINT'
        ])->exists();

        if ($check_plst) {
            $plst_data = ProductLocationSetupTransaction::select('pls_id', 'u_id_helper', 'u_id_packer', 'pt_id')->where([
                'id' => $plst_id,
            ])->get()->first();
            $stts = '';
            if ($final_price < 0 and $exchange != 'true') {
                if ($subtotal_item < 0) {
                    $stts = 'REFUND';
                }
            } else {
                if ($subtotal_item < 0) {
                    $stts = 'EXCHANGE';
                }
            }
            if (strtoupper($pl_code) == 'TOKO') {
                $stts = 'INSTOCK';
            } else if (in_array(['pl_code' => $pl_code], $b1g1_setup)) {
                $stts = 'INSTOCK';
            }
            $insert = ProductLocationSetupTransaction::insert([
                'pt_id' => $plst_data->pt_id,
                'pls_id' => $plst_data->pls_id,
                'u_id_packer' => $plst_data->u_id_packer,
                'u_id_helper' => $plst_data->u_id_helper,
                'u_id' => Auth::user()->id,
                'u_id_refund' => Auth::user()->id,
                'plst_qty' => abs($item_qty),
                'plst_type' => 'OUT',
                'plst_status' => $stts,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $update = ProductLocationSetupTransaction::where('id', $plst_id)->update([
                'plst_status' => 'DONE',
            ]);
        } else {
            if ($nameset_price > 0) {
                $update = ProductLocationSetupTransaction::where([
                    'id' => $plst_id,
                    'plst_status' => 'WAITING FOR CHECKOUT'
                ])->update([
                    'plst_status' => 'WAITING FOR NAMESET',
                    'plst_qty' => $item_qty,
                    'pt_id' => $pt_id,
                    'u_id_packer' => $u_id
                ]);
                DB::table('pos_transactions')->where('id', $pt_id)->update([
                    'pos_status' => 'NAMESET',
                ]);
            } else {
                $update = ProductLocationSetupTransaction::where([
                    'id' => $plst_id,
                    'plst_status' => 'WAITING FOR CHECKOUT'
                ])->update([
                    'plst_status' => 'DONE',
                    'plst_qty' => $item_qty,
                    'pt_id' => $pt_id,
                    'u_id_packer' => $u_id
                ]);
            }
        }
        if (!empty($update)) {
            if (strtoupper($pl_code) == 'TOKO') {
                $pls_qty = ProductLocationSetup::select('pls_qty', 'pst_id')->where('pl_id', '=', $pl_id)->where('pst_id', '=', $pst_id)
                    ->get()->first();
                $pst = ProductStock::select('id', 'ps_qty')->where('id', '=', $pst_id)->get()->first();
                if (!empty($pls_qty)) {
                    if ($item_qty < 0) {
                        $f_item_qty = $pls_qty->pls_qty + abs($item_qty);

                        $f_item_qty_pst = $pst->ps_qty + abs($item_qty);
                    } else {
                        $f_item_qty = ($pls_qty->pls_qty + 1) - $item_qty;

                        $f_item_qty_pst = ($pst->ps_qty + 1) - $item_qty;
                    }
                    $updt = ProductLocationSetup::where('pl_id', '=', $pl_id)->where('pst_id', '=', $pst_id)
                        ->update([
                            'pls_qty' => $f_item_qty
                        ]);

                    $update_pst = ProductStock::where('id', '=', $pls_qty->pst_id)->update([
                        'ps_qty' =>  $f_item_qty_pst
                    ]);
                }
            } else if (in_array(['pl_code' => $pl_code], $b1g1_setup)) {
                $pls_qty = ProductLocationSetup::select('pls_qty')->where('pl_id', '=', $pl_id)->where('pst_id', '=', $pst_id)
                    ->get()->first();
                if (!empty($pls_qty)) {
                    if ($item_qty < 0) {
                        $f_item_qty = $pls_qty->pls_qty + abs($item_qty);
                    } else {
                        $f_item_qty = ($pls_qty->pls_qty + 1) - $item_qty;
                    }
                    $updt = ProductLocationSetup::where('pl_id', '=', $pl_id)->where('pst_id', '=', $pst_id)
                        ->update([
                            'pls_qty' => $f_item_qty
                        ]);
                }
            }
            $sold = DB::table('products')->select('products.id as p_id', 'sold')
                ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                ->where('product_stocks.id', '=', $pst_id)
                ->get()->first();
            if (!empty($sold)) {
                $update_sold = DB::table('products')->where('id', '=', $sold->p_id)
                    ->update([
                        'sold' => ($sold->sold + $item_qty)
                    ]);
            }
            $pos_td_description = null;
            if (session()->get('voc_item') != Auth::user()->id . '-' . $pst_id) {
                if ($pst_id == $voc_pst_id) {
                    session()->put('voc_item', Auth::user()->id . '-' . $pst_id);
                    if ($item_qty > 1) {
                        $pos_td_discount_price = (($item_qty - 1) * $price) + $voc_value;
                        $pos_td_description = ($item_qty - 1) . " x " . $price . " | 1 x " . $voc_value . "";
                    } else {
                        $pos_td_discount_price = $item_qty * $voc_value;
                        $price = $voc_value;
                        $pos_td_description = "1 x " . $voc_value . "";
                    }
                } else {
                    $pos_td_discount_price = $item_qty * $price;
                }
            } else {
                $pos_td_discount_price = $item_qty * $price;
            }

            $create = PosTransactionDetail::create([
                'pt_id' => $pt_id,
                'pst_id' => $pst_id,
                'pl_id' => $pl_id,
                'pos_td_qty' => $item_qty,
//                'pos_td_sell_price' => $final_price,
                'pos_td_sell_price' => ($pos_td_discount_price + $nameset_price) - $discount_number,
                'pos_td_discount' => $discount,
                'pos_td_discount_number' => $discount_number,
                'pos_td_discount_price' => $pos_td_discount_price,
                'pos_td_nameset_price' => $nameset_price,
                'pos_td_nameset' => $nameset,
                'pos_td_description' => $pos_td_description,
                'pos_td_total_price' => $pos_td_discount_price + $nameset_price,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function orderListByInvoice(Request $request)
    {
        $invoice = $request->_qr;
        $check = PosTransaction::select('id')->where('pos_invoice', $invoice)
            ->where('pos_status', '!=', 'CANCEL')->exists();
        if ($check) {
            $pt_id = PosTransaction::select('pos_transactions.id as pt_id')
                ->leftJoin('store_types', 'store_types.id', '=', 'pos_transactions.stt_id')
                ->where('stt_name', '=', 'online')->where('pos_invoice', '=', $invoice)->get()->first()->pt_id;
            $get_order_list = PosTransactionDetail::select('pos_transaction_details.id as ptd_id', 'product_locations.id as pl_id', 'pos_td_nameset', 'pst_id', 'p_name', 'pl_code', 'pl_name', 'p_color', 'sz_name', 'pos_td_qty', 'pos_td_qty_pickup')
                ->join('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->join('product_locations', 'product_locations.id', '=', 'pos_transaction_details.pl_id')
                ->where('pt_id', $pt_id)
                ->where('pos_td_reject', '!=', '1')
                ->orderBy('product_locations.pl_code')->get();
        } else {
            $get_order_list = null;
        }
        $data = [
            'order_list' => $get_order_list
        ];
        return view('app.dashboard.helper._order_list', compact('data'));
    }

    public function packingListByInvoice(Request $request)
    {
        $invoice = $request->_qr;
        $check = PosTransaction::select('id')->where('pos_invoice', $invoice)
            ->where('pos_status', '!=', 'CANCEL')->exists();
        if ($check) {
            $pt_id = PosTransaction::select('pos_transactions.id as pt_id')
                ->leftJoin('store_types', 'store_types.id', '=', 'pos_transactions.stt_id')
                ->where('stt_name', '=', 'online')->where('pos_invoice', '=', $invoice)->get()->first()->pt_id;
            $packing_list = ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id', 'p_name', 'p_color', 'sz_name', 'plst_qty', 'plst_status')
                ->join('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->whereIn('plst_status', ['WAITING FOR PACKING', 'DONE'])
                ->where('product_location_setup_transactions.pt_id', $pt_id)
                ->orderBy('product_locations.pl_code')->get();
        } else {
            $packing_list = null;
        }
        $data = [
            'packing_list' => $packing_list,
        ];
        return view('app.dashboard.helper._packing_list', compact('data'));
    }

    private function onlineCrossAging($st_id, $age)
    {
        $r = true;
        $check = DB::table('online_cross_agings')
            ->where('st_id', '=', $st_id)->first();
        if (!empty($check)) {
            if ($age < $check->oca_age) {
                $r = false;
            }
        }
        return $r;
    }

    private function checkAging($st_id, $pst_id)
    {
        $r = true;
        $age = 0;
        $check_poad = PurchaseOrderArticleDetailStatus::select('purchase_order_article_detail_statuses.created_at as poads_created')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('purchase_order_articles', 'purchase_order_article_details.poa_id', '=', 'purchase_order_articles.id')
            ->leftJoin('purchase_orders', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
            ->where('purchase_order_article_details.pst_id', '=', $pst_id)
            ->where('purchase_orders.st_id', '=', $st_id)
            ->orderByDesc('purchase_order_article_detail_statuses.id')
            ->get()->first();

        $stf = DB::table('stock_transfer_detail_statuses')->select('stock_transfer_detail_statuses.created_at as created_at')
            ->leftJoin('stock_transfer_details', 'stock_transfer_details.id', '=', 'stock_transfer_detail_statuses.stfd_id')
            ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
            ->where('stock_transfers.st_id_end', '=', $st_id)
            ->where('product_stocks.id', '=', $pst_id)
            ->orderByDesc('stock_transfer_detail_statuses.id')
            ->whereNotNull('stock_transfer_detail_statuses.created_at')
            ->get()->first();
        $days_remain_po = 99999;
        $days_remain_tf = 99999;

        if (!empty($check_poad)) {
            $date1_remain_po = $check_poad->poads_created;
            $date2_remain_po = date('Y-m-d H:i:s');
            $diff_remain_po = abs(strtotime($date1_remain_po) - strtotime($date2_remain_po));
            if ($date1_remain_po > $date2_remain_po) {
                $diff_remain_po = - ($diff_remain_po);
            }
            $days_remain_po = round($diff_remain_po / 86400);
        }
        if (!empty($stf)) {
            $date1_remain_tf = $stf->created_at;
            $date2_remain_tf = date('Y-m-d H:i:s');
            $diff_remain_tf = abs(strtotime($date1_remain_tf) - strtotime($date2_remain_tf));
            if ($date1_remain_tf > $date2_remain_tf) {
                $diff_remain_tf = - ($diff_remain_tf);
            }
            $days_remain_tf = round($diff_remain_tf / 86400);
        }
        if ($days_remain_po <= $days_remain_tf) {
            $age = $days_remain_po;
        } else {
            $age = $days_remain_tf;
        }
        if ($this->onlineCrossAging($st_id, $age)) {
            $r = true;
        } else {
            $r = false;
        }
        return $r;
    }

    function fetch(Request $request)
    {

        if ($request->get('query')) {
            $exception = ExceptionLocation::select('pl_code')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

            $query = $request->get('query');
            $type = $request->get('type');
            $std_id = $request->get('_std_id');
            $st_id = $request->get('_st_id');
            $b1g1_id = null;
            $b1g1_price = null;
            if (!empty($st_id)) {
                $st_id = $st_id;
            } else {
                $st_id = Auth::user()->st_id;
            }
            if ($st_id != Auth::user()->st_id) {
                $cross = 'true';
            } else {
                $cross = 'false';
            }
            $data = ProductStock::select('p_name', 'p_color', 'p_sell_price', 'p_price_tag', 'products.psc_id', 'ps_price_tag', 'ps_sell_price', 'sz_name', 'pls_qty', 'ps_qty', 'br_name', 'product_stocks.id as pst_id')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->join('brands', 'brands.id', '=', 'products.br_id')
                ->join('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_locations.st_id', '=', $st_id)
                //                    ->where('pls_qty', '>=', '0')
                ->whereNotIn('pl_code', $exception)
                ->whereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name," ", article_id) LIKE ?', "%$query%")
                ->orWhere('ps_barcode', 'LIKE', "%$query%")
                ->groupBy('product_stocks.id')
                ->limit(13)
                ->get();
            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach ($data as $row) {
                    $check_setup = ProductLocationSetup::select('product_locations.id as pl_id', 'product_location_setups.id as pls_id', 'pl_code', 'pl_name', 'pls_qty')
                        ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('product_locations.st_id', '=', $st_id)
                        ->where('pst_id', $row->pst_id)
                        //                    ->where('pls_qty', '>', '0')
                        ->whereNotIn('pl_code', $exception)->get();
                    $bin = '';
                    $bin_list = '';
                    $sell_price = 0;
                    $sell_price_discount = 0;
                    $bandrol = 0;
                    if (!empty($row->ps_price_tag)) {
                        $bandrol = $row->ps_price_tag;
                    } else {
                        $bandrol = $row->p_price_tag;
                    }
                    if ($type == 'RESELLER') {
                        if (!empty($row->ps_price_tag)) {
                            $sell_price = $row->ps_price_tag;
                        } else {
                            $sell_price = $row->p_price_tag;
                        }
                    } else {
                        if (!empty($row->ps_sell_price)) {
                            $sell_price = $row->ps_sell_price;
                        } else {
                            $sell_price = $row->p_sell_price;
                        }
                    }
                    $set_discount = ProductDiscountDetail::select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                        ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                        ->where('pst_id', '=', $row->pst_id)
                        ->where('std_id', '=', $std_id)
                        ->orderByDesc('product_discounts.created_at')
                        ->where('product_discounts.pd_date', '>=', date('Y-m-d'))
                        ->get()->first();
                    if (!empty($set_discount)) {
                        if (date('Y-m-d') <= $set_discount->pd_date) {
                            if (empty($set_discount->st_id)) {
                                if (!empty($row->ps_price_tag)) {
                                    $price_tag = $row->ps_price_tag;
                                } else {
                                    $price_tag = $row->p_price_tag;
                                }
                                if ($set_discount->pd_type == 'percent') {
                                    $sell_price_discount = $price_tag / 100 * $set_discount->pd_value;
                                    $sell_price = $price_tag - ($price_tag / 100 * $set_discount->pd_value);
                                } else if ($set_discount->pd_type == 'amount') {
                                    $sell_price_discount = $set_discount->pd_value;
                                    $sell_price = $price_tag - $set_discount->pd_value;
                                } else {
                                    $sell_price = $price_tag;
                                    $b1g1_id = $row->pst_id;
                                    $b1g1_price = $sell_price;
                                }
                            } else {
                                if (Auth::user()->st_id == $set_discount->st_id) {
                                    if (!empty($row->ps_price_tag)) {
                                        $price_tag = $row->ps_price_tag;
                                    } else {
                                        $price_tag = $row->p_price_tag;
                                    }
                                    if ($set_discount->pd_type == 'percent') {
                                        $sell_price_discount = $price_tag / 100 * $set_discount->pd_value;
                                        $sell_price = $price_tag - ($price_tag / 100 * $set_discount->pd_value);
                                    } else if ($set_discount->pd_type == 'amount') {
                                        $sell_price_discount = $set_discount->pd_value;
                                        $sell_price = $price_tag - $set_discount->pd_value;
                                    } else {
                                        $sell_price = $price_tag;
                                        $b1g1_id = $row->pst_id;
                                        $b1g1_price = $sell_price;
                                    }
                                }
                            }
                        }
                    }
                    if (!empty($check_setup)) {
                        foreach ($check_setup as $brow) {
                            $bin .= '<span class="btn-lg btn-info">[' . strtoupper($brow->pl_code) . '] (' . $brow->pls_qty . ')</span> ';
                        }
                        $bin_list .= '<select class="col-12 mr-5 text-white font-weight-bold" id="pl_id" style="background-color:#986923; border-radius:10px;">';
                        foreach ($check_setup as $blrow) {
                            $bin_list .= '<option class="col-12" data-pl_code="' . $blrow->pl_code . '" value="' . $blrow->pl_id . '">[' . strtoupper($blrow->pl_code) . '] [' . $blrow->pls_qty . ']</option>';
                        }
                        $bin_list .= '</select>';
                    }
                    $ok = $this->checkAging($st_id, $row->pst_id);
                    if ($bin != '') {
                        $output .= '
                        <li><a class="btn btn-sm btn-inventory col-12" data-cross="' . $cross . '" data-ok="' . $ok . '" data-sell_price="' . $sell_price . '" data-sell_price_discount="' . $sell_price_discount . '" data-psc_id="' . $row->psc_id . '" data-bandrol="' . $bandrol . '" data-pls_qty="' . $row->pls_qty . '" data-ps_qty="' . $row->ps_qty . '" data-pst_id="' . $row->pst_id . '" data-bin="' . htmlspecialchars($bin_list) . '" data-p_name="[' . $row->br_name . '] ' . $row->article_id . ' ' . $row->p_name . ' ' . $row->p_color . ' ' . $row->sz_name . '" id="add_to_item_list"><span style="float-left;"><span class="btn-lg btn-primary">[' . strtoupper($row->br_name) . '] ' . strtoupper($row->p_name) . ' ' . strtoupper($row->p_color) . ' [' . strtoupper($row->sz_name) . ']</span> ' . $bin . '</span></a></li>
                        ';
                    }
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function deleteRating()
    {
        UserRating::where([
            'st_id' => Auth::user()->st_id,
            'stt_id' => Auth::user()->stt_id,
            'ur_status' => 'WAITING FOR REVIEW',
        ])->delete();
        UserRating::where([
            'st_id' => Auth::user()->st_id,
            'stt_id' => Auth::user()->stt_id,
            'ur_status' => 'WAITING FOR CHECKOUT',
        ])->update([
            'ur_status' => 'DONE',
        ]);
        $r['status'] = '200';
        return json_encode($r);
    }

    function fetchWaiting(Request $request)
    {
        //return $request->all();

        if ($request->get('query')) {
            $exception = ExceptionLocation::select('pl_code')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

            $b1g1_setup = BuyOneGetOne::select('pl_code')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'buy_one_get_ones.pl_id')->get()->toArray();

            $fs = null;
            $b1g1_id = null;
            $b1g1_price = null;
            $free_sock = ProductLocationSetup::select('pls_qty')->where([
                'pst_id' => '15118',
                'pl_id' => '306'
            ])->get()->first();
            if (!empty($free_sock)) {
                $fs = $free_sock->pls_qty;
            }

            $st_id = $request->get('_st_id');
            if (!empty($st_id)) {
                $st_id = $st_id;
            } else {
                $st_id = Auth::user()->st_id;
            }

            $location_store = ProductLocation::select('id')->where('st_id', $st_id)->where('pl_code', 'TOKO')->get()->first();

            $query = $request->get('query');
            $type = $request->get('type');
            $std_id = $request->get('_std_id');
            $item_type = $request->get('_item_type');
            $sell_price_discount = 0;
            $plst_status_new = [
                'WAITING OFFLINE',
                'INSTOCK APPROVAL'
            ];
            if ($item_type == 'waiting') {
                $data = ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id', 'pl_code', 'products.psc_id', 'p_name', 'p_color', 'p_sell_price', 'p_price_tag', 'ps_price_tag', 'ps_sell_price', 'sz_name', 'ps_qty', 'pls_qty', 'br_name', 'plst_status', 'product_stocks.id as pst_id', 'product_locations.id as pl_id', 'products.article_id as article_id')
                    ->join('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                    ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                    ->join('products', 'products.id', '=', 'product_stocks.p_id')
                    ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                    ->join('bra nds', 'brands.id', '=', 'products.br_id')
                    ->where('pls_qty', '>=', '0')
                    ->whereNotIn('pl_code', $exception)
                    ->where('product_locations.st_id', '=', $st_id)
                    ->whereIn('plst_status', $plst_status_new)
                    ->whereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$query%")
                    ->orWhereRaw('ts_products.article_id LIKE ?', "%$query%")
                    ->orWhereRaw('ts_product_stocks.ps_barcode LIKE ?', "%$query%")
                    ->orWhereRaw('ts_products.p_name LIKE ?', "%$query%")
                    ->orWhereRaw('ts_brands.br_name LIKE ?', "%$query%")
                    ->limit(13)
                    ->get();
            } else if ($item_type == 'b1g1') {
                $data = ProductStock::select('product_locations.id as pl_id', 'products.psc_id', 'pl_code', 'p_name', 'p_color', 'p_sell_price', 'p_price_tag', 'ps_price_tag', 'ps_sell_price', 'sz_name', 'ps_qty', 'pls_qty', 'br_name', 'product_stocks.id as pst_id', 'products.article_id as article_id')
                    ->join('products', 'products.id', '=', 'product_stocks.p_id')
                    ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                    ->join('brands', 'brands.id', '=', 'products.br_id')
                    ->join('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                    ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    // ->where('product_locations.st_id', '=', Auth::user()->st_id)
                    ->where('product_locations.st_id', '=', $st_id)
                    ->where('pls_qty', '>', '0')
                    ->whereNotIn('pl_code', $exception)
                    ->whereIn('pl_code', $b1g1_setup)
                    ->whereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$query%")
                    ->orWhereRaw('ts_products.article_id LIKE ?', "%$query%")
                    ->orWhereRaw('ts_product_stocks.ps_barcode LIKE ?', "%$query%")
                    ->orWhereRaw('ts_products.p_name LIKE ?', "%$query%")
                    ->orWhereRaw('ts_brands.br_name LIKE ?', "%$query%")
                    ->groupBy('product_stocks.id')
                    ->limit(13)
                    ->get();
            } else if ($item_type == 'store') {
                //                $data = ProductStock::select('product_locations.id as pl_id', 'products.psc_id', 'p_name', 'pl_code', 'p_color', 'p_sell_price', 'p_price_tag', 'ps_price_tag', 'ps_sell_price', 'sz_name', 'ps_qty', 'pls_qty', 'br_name', 'product_stocks.id as pst_id', 'products.article_id as article_id')
                //                    ->join('products', 'products.id', '=', 'product_stocks.p_id')
                //                    ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                //                    ->join('brands', 'brands.id', '=', 'products.br_id')
                //                    ->join('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                //                    ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ////                    ->where('product_locations.st_id', '=', Auth::user()->st_id)
                //                    ->where('product_locations.id', '=', $location_store)
                //                    ->where('pls_qty', '>=', '0')
                ////                    ->whereNotIn('pl_code', $exception)
                //                    ->whereIn('pl_code', ['TOKO'])
                ////                    ->where('product_locations.id', '=', '1001')
                //                    ->whereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$query%")
                //                    ->orWhereRaw('ts_products.article_id LIKE ?', "%$query%")
                //                    ->orWhereRaw('ts_product_stocks.ps_barcode LIKE ?', "%$query%")
                //                    ->orWhereRaw('ts_products.p_name LIKE ?', "%$query%")
                //                    ->orWhereRaw('ts_brands.br_name LIKE ?', "%$query%")
                //                    ->groupBy('product_stocks.id')
                ////                    ->limit(13)
                //                    ->get();
                $data = ProductLocationSetup::select(
                    'pl_id',
                    'products.psc_id',
                    'products.p_name',
                    'products.p_color',
                    'products.p_sell_price',
                    'products.p_price_tag',
                    'product_stocks.ps_price_tag',
                    'product_stocks.ps_sell_price',
                    'sizes.sz_name',
                    'product_stocks.ps_qty',
                    'product_location_setups.pls_qty',
                    'brands.br_name',
                    'product_stocks.id as pst_id',
                    'products.article_id as article_id'
                )
                    ->join('product_stocks', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                    ->join('products', 'product_stocks.p_id', '=', 'products.id')
                    ->join('sizes', 'product_stocks.sz_id', '=', 'sizes.id')
                    ->join('brands', 'products.br_id', '=', 'brands.id')
                    ->where('product_location_setups.pl_id', $location_store->id)
                    ->where('product_stocks.ps_barcode', 'LIKE', "%$query%")
                    //                    ->whereRaw('ts_products.article_id LIKE ?', "%$query%")
                    //                    ->orWhereRaw('ts_product_stocks.ps_barcode LIKE ?', "%$query%")
                    //                    ->orWhereRaw('ts_products.p_name LIKE ?', "%$query%")
                    //                    ->orWhereRaw('ts_brands.br_name LIKE ?', "%$query%")
                    ->get();
            }

            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach ($data as $row) {
                    $bin = '';
                    $sell_price = 0;
                    $bandrol = 0;
                    if (!empty($row->ps_price_tag)) {
                        $bandrol = $row->ps_price_tag;
                    } else {
                        $bandrol = $row->p_price_tag;
                    }
                    if ($type == 'RESELLER') {
                        if (!empty($row->ps_price_tag)) {
                            $sell_price = $row->ps_price_tag;
                        } else {
                            $sell_price = $row->p_price_tag;
                        }
                    } else {
                        if (!empty($row->ps_sell_price)) {
                            $sell_price = $row->ps_sell_price;
                        } else {
                            $sell_price = $row->p_sell_price;
                        }
                    }
                    $set_discount_check = ProductDiscountDetail::select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                        ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                        ->where('pst_id', '=', $row->pst_id)
                        ->where('std_id', '=', $std_id)
                        ->where('product_discounts.st_id', '=', Auth::user()->st_id)
                        ->where('pd_type', '=', 'b1g1')
                        ->where('pd_date', '>=', date('Y-m-d'))
                        ->orderByDesc('product_discount_details.created_at')
                        ->exists();
                    if ($set_discount_check) {
                        $set_discount = ProductDiscountDetail::select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                            ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                            ->where('pst_id', '=', $row->pst_id)
                            ->where('std_id', '=', $std_id)
                            ->where('product_discounts.st_id', '=', Auth::user()->st_id)->where('pd_type', '=', 'b1g1')
                            ->where('product_discounts.pd_date', '>=', date('Y-m-d'))
                            ->orderByDesc('product_discount_details.created_at')
                            ->get()->first();
                    } else {
                        $set_discount = ProductDiscountDetail::select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                            ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                            ->where('pst_id', '=', $row->pst_id)
                            ->where('pd_date', '>=', date('Y-m-d'))
                            ->where('product_discounts.st_id', '=', Auth::user()->st_id)
                            ->where('std_id', '=', $std_id)->where('pd_type', '!=', 'b1g1')
                            ->orderByDesc('product_discount_details.created_at')->get()->first();
                        if (empty($set_discount)) {
                            $set_discount = ProductDiscountDetail::select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                                ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                                ->where('pst_id', '=', $row->pst_id)
                                ->whereNull('product_discounts.st_id')
                                ->where('pd_date', '>=', date('Y-m-d'))
                                ->where('std_id', '=', $std_id)->where('pd_type', '!=', 'b1g1')
                                ->orderByDesc('product_discount_details.created_at')->get()->first();
                        }
                    }
                    if (!empty($set_discount)) {
                        if (date('Y-m-d') <= $set_discount->pd_date) {
                            if (!empty($row->ps_price_tag)) {
                                $price_tag = $row->ps_price_tag;
                            } else {
                                $price_tag = $row->p_price_tag;
                            }
                            if ($set_discount->pd_type == 'percent') {
                                $sell_price_discount = $price_tag / 100 * $set_discount->pd_value;
                                $sell_price = $price_tag - ($price_tag / 100 * $set_discount->pd_value);
                            } else if ($set_discount->pd_type == 'amount') {
                                $sell_price_discount = $set_discount->pd_value;
                                $sell_price = $price_tag - $set_discount->pd_value;
                            } else {
                                $sell_price = $price_tag;
                                $b1g1_id = $row->pst_id;
                                $b1g1_price = $sell_price;
                            }
                        }
                    }
                    if ($item_type == 'waiting') {
                        $status = '<span class="btn-lg btn-warning">' . $row->plst_status . '</span>';
                        $bin = '<span class="btn-lg btn-info">' . strtoupper($row->pl_code) . '</span>';
                    } else {
                        $status = '';
                        $bin = '<span class="btn-lg btn-info">' . $row->pls_qty  . '</span>';
                    }
                    $output .= '
                    <li>
                    <a class="btn btn-sm btn-inventory col-12" data-b1g1_id="' . $b1g1_id . '" data-b1g1_price="' . $b1g1_price . '" data-fs="' . $fs . '" data-psc_id="' . $row->psc_id . '" data-pls_qty="' . $row->pls_qty . '" data-plst_id="' . $row->plst_id . '" data-pl_id="' . $row->pl_id . '" data-sell_price="' . $sell_price . '" data-sell_price_discount="' . $sell_price_discount . '" data-bandrol="' . $bandrol . '" data-ps_qty="' . $row->ps_qty . '" data-pst_id="' . $row->pst_id . '" data-p_name="[' . $row->br_name . '] ' . $row->article_id . ' ' . $row->p_name . ' ' . $row->p_color . ' [' . $row->sz_name . '] [' . $row->pl_code . ']" id="add_to_item_list">
                    <span style="float-left;">
                    <span class="btn-lg btn-primary">[' . strtoupper($row->br_name) . ']' . strtoupper($row->article_id) . ' ' . strtoupper($row->p_name) . ' ' . strtoupper($row->p_color) . ' [' . strtoupper($row->sz_name) . ']</span> ' . $bin . ' ' . $status . ' </span></a></li>
                    ';
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    function fetchInvoice(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = PosTransaction::select('pos_invoice')
                ->where('pos_invoice', 'LIKE', "%{$query}%")
                ->limit(7)
                ->get();
            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach ($data as $row) {
                    $output .= '
                    <li><a class="btn btn-sm btn-inventory col-12" href="' . url('') . '/print_invoice/' . $row->pos_invoice . '" target="_blank"><span style="float-left;"><span class="btn-sm btn-primary">' . strtoupper($row->pos_invoice) . '</span></a></li>
                    ';
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    function fetchInvoiceOffline(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = PosTransaction::select('pos_invoice')
                ->where('pos_invoice', 'LIKE', "%{$query}%")
                ->limit(7)
                ->get();
            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach ($data as $row) {
                    $output .= '
                    <li><a class="btn btn-sm btn-inventory col-12" href="' . url('') . '/print_offline_invoice/' . $row->pos_invoice . '" target="_blank"><span style="float-left;"><span class="btn-sm btn-primary">' . strtoupper($row->pos_invoice) . '</span></a></li>
                    ';
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function getFreeSock(Request $request)
    {
        $access_code = $request->post('_access_code');
        $check = User::where('u_secret_code', '=', $access_code)->exists();
        if (!$check) {
            $r['status'] = '419';
        } else {
            UserRating::where([
                'st_id' => Auth::user()->st_id,
                'stt_id' => Auth::user()->stt_id,
                'ur_status' => 'WAITING FOR REVIEW',
            ])->delete();
            UserRating::where([
                'st_id' => Auth::user()->st_id,
                'stt_id' => Auth::user()->stt_id,
                'ur_status' => 'WAITING FOR CHECKOUT',
            ])->update([
                'ur_status' => 'DONE',
            ]);
            $u_id = User::select('id')->where('u_secret_code', '=', $access_code)->get()->first()->id;
            $user_rating = UserRating::insert([
                'user_id' => $u_id,
                'st_id' => Auth::user()->st_id,
                'stt_id' => Auth::user()->stt_id,
                'ur_status' => 'WAITING FOR REVIEW',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            if (!empty($user_rating)) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        }
        return json_encode($r);
    }

    public function checkWaitingForCheckout()
    {
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();


        $check = ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id', 'product_discounts.st_id as st_id', 'pd_date', 'pd_type', 'pd_value', 'pl_code', 'p_name', 'br_name', 'p_color', 'p_sell_price', 'p_price_tag', 'ps_price_tag', 'ps_sell_price', 'sz_name', 'ps_qty', 'pls_qty', 'product_stocks.id as pst_id', 'product_locations.id as pl_id')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_discount_details', 'product_discount_details.pst_id', '=', 'product_stocks.id')
            ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
            ->where('plst_status', '=', 'WAITING FOR CHECKOUT')
            ->where('product_locations.st_id', '=', Auth::user()->st_id)
            ->where('u_id', Auth::user()->id)
            ->where('pls_qty', '>=', '0')
            ->whereNotIn('pl_code', $exception)
            ->groupBy('product_stocks.id')
            ->get();
        if (!empty($check)) {
            $data = [
                'pos_data' => $check
            ];
        } else {
            $data = [
                'pos_data' => null
            ];
        }
        return view('app.offline_pos._reload_waiting_for_checkout', compact('data'));
    }

    function fetchRefundInvoice(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = PosTransaction::select('pos_transactions.id as pt_id', 'pos_invoice')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->where('st_id', '=', Auth::user()->st_id)
                ->where('pos_refund', '!=', '1')
                ->whereIn('pos_status', ['DONE', 'SHIPPING NUMBER', 'IN DELIVERY'])
                ->whereIn('plst_status', ['DONE', 'COMPLAINT', 'WAITING FOR PACKING'])
                ->whereRaw('CONCAT(pos_invoice) LIKE ?', "%$query%")
                ->groupBy('pos_transactions.id')
                ->orderBy('pos_transactions.created_at')
                ->limit(10)->get();

            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach ($data as $row) {
                    $output .= '
                  <li><a class="btn btn-sm btn-inventory col-12" data-id="' . $row->pt_id . '" id="add_to_item_list_refund">' . $row->pos_invoice . '</a></li>';
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function verifyVoucher(Request $request)
    {
        $code = $request->post('code');
        $item = $request->post('item');

        $check = DB::table('vouchers')->where('vc_code', '=', $code)
            ->where('vc_due_date', '>=', date('Y-m-d'))
            ->where('vc_status', '=', '1')->get()->first();
        if (!empty($check)) {
            $check_vtrx = DB::table('voucher_transactions')->where('vc_id', '=', $check->id)->get()->first();
            if ($check->vc_reuse == '0') {
                if (!empty($check_vtrx)) {
                    $r['status'] = '202';
                    return json_encode($r);
                }
            } else if ($check->vc_reuse == '2') {
                $start_new = date("Y-m-d H:i:s", strtotime("+1 month", strtotime($check_vtrx->created_at)));
                if (date('Y-m-d H:i:s') < $start_new) {
                    $r['status'] = '203';
                    return json_encode($r);
                }
            }
            if ($check->vc_platform == 'all') {
                if (count($item) > 0) {
                    $sell_price = array();
                    $price = array();
                    $item_id = array();
                    for ($i = 0; $i < count($item); $i++) {
                        $exp = explode('-', $item[$i]);
                        $sell_price[] = [$exp[2]];
                        $price[] = [$exp[1]];
                        $item_id[] = [$exp[0]];
                    }
                    rsort($price);
                    $disc_item = '';
                    for ($i = 0; $i < count($item_id); $i++) {
                        $key1 = $item_id[$i][0];
                        for ($x = 0; $x < count($sell_price); $x++) {
                            $key2 = $price[0][0];
                            $key3 = $sell_price[$x][0];
                            $key = $key1 . '-' . $key2 . '-' . $key3;
                            $search = array_search($key, $item);
                            if ($search != false || $search >= 0) {
                                $disc_item = $search;
                            }
                        }
                    }
                    $result = explode('-', $item[$disc_item]);
                    $id = $result[0];
                    $bandrol = $result[1];
                    $sell = $result[2];
                    $disc = null;
                    $disc_type = null;
                    $disc_value = null;
                    $value = null;
                    $article = DB::table('products')->selectRaw("CONCAT(br_name, ' ', p_name, ' ', p_color, ' ', sz_name) as article")
                        ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->where('product_stocks.id', '=', $id)->get()->first()->article;
                    if ($check->vc_type == 'percent') {
                        $disc_type = "%";
                        $disc = $check->vc_discount;
                        $disc_value = ($bandrol / 100) * $check->vc_discount;
                        $value = $bandrol - $disc_value;
                    } else if ($check->vc_type == 'amount') {
                        $disc_type = "Rp";
                        $disc = ($check->vc_discount / 1000) . 'K';
                        $disc_value = $check->vc_discount;
                        $value = $bandrol - $disc_value;
                    }
                    $r['voc_id'] = $check->id;
                    $r['pst_id'] = $id;
                    $r['article'] = $article;
                    $r['bandrol'] = $bandrol;
                    $r['sell'] = $sell;
                    $r['disc_type'] = $disc_type;
                    $r['disc_value'] = $disc_value;
                    $r['disc'] = $disc;
                    $r['value'] = $value;
                    $r['status'] = '200';
                } else {
                    $r['status'] = '204';
                }
            } else {
                $r['status'] = '201';
            }
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function verifyVouchers(Request $request)
    {
        $codes = $request->post('formData');
        $item = $request->post('item');

        $total_discount_voucher_nomimal = 0;
        $total_discount_voucher_percent = 0;

        foreach ($codes as $code) {
            //            return $code['value'];
            $check = DB::table('vouchers')->where('vc_code', '=', $code['value'])
                ->where('vc_due_date', '>=', date('Y-m-d'))
                ->where('vc_status', '=', '1')->get()->first();

            //            return $check;
            if (!empty($check)) {
                $check_vtrx = DB::table('voucher_transactions')->where('vc_id', '=', $check->id)->get()->first();
                if ($check->vc_reuse == '0') {
                    if (!empty($check_vtrx)) {
                        $r['status'] = '202';
                        return json_encode($r);
                    }
                } else if ($check->vc_reuse == '2') {
                    $start_new = date("Y-m-d H:i:s", strtotime("+1 month", strtotime($check_vtrx->created_at)));
                    if (date('Y-m-d H:i:s') < $start_new) {
                        $r['status'] = '203';
                        return json_encode($r);
                    }
                }
                if ($check->vc_platform == 'all') {
                    if (count($item) > 0) {
                        $sell_price = array();
                        $price = array();
                        $item_id = array();
                        for ($i = 0; $i < count($item); $i++) {
                            $exp = explode('-', $item[$i]);
                            $sell_price[] = [$exp[2]];
                            $price[] = [$exp[1]];
                            $item_id[] = [$exp[0]];
                        }
                        rsort($price);
                        $disc_item = '';
                        for ($i = 0; $i < count($item_id); $i++) {
                            $key1 = $item_id[$i][0];
                            for ($x = 0; $x < count($sell_price); $x++) {
                                $key2 = $price[0][0];
                                $key3 = $sell_price[$x][0];
                                $key = $key1 . '-' . $key2 . '-' . $key3;
                                $search = array_search($key, $item);
                                if ($search != false || $search >= 0) {
                                    $disc_item = $search;
                                }
                            }
                        }
                        $result = explode('-', $item[$disc_item]);
                        $id = $result[0];
                        $bandrol = $result[1];
                        $sell = $result[2];
                        $disc = null;
                        $disc_type = null;
                        $disc_value = null;
                        $value = null;
                        $article = DB::table('products')->selectRaw("CONCAT(br_name, ' ', p_name, ' ', p_color, ' ', sz_name) as article")
                            ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                            ->where('product_stocks.id', '=', $id)->get()->first()->article;
                        if ($check->vc_type == 'percent') {
                            $total_discount_voucher_percent += $check->vc_discount;
                            $disc_type = "%";
                            $disc = $check->vc_discount;
                            $disc_value = ($bandrol / 100) * $total_discount_voucher_percent;
                            $value = $bandrol - $disc_value;
                        } else if ($check->vc_type == 'amount') {
                            $total_discount_voucher_nomimal += $check->vc_discount;
                            $disc_type = "Rp";
                            $disc = ($total_discount_voucher_nomimal / 1000) . 'K';
                            $disc_value = $total_discount_voucher_nomimal;
                            $value = $bandrol - $disc_value;
                        }
                        $r['voc_id'] = $check->id;
                        $r['pst_id'] = $id;
                        $r['article'] = $article;
                        $r['bandrol'] = $bandrol;
                        $r['sell'] = $sell;
                        $r['disc_type'] = $disc_type;
                        $r['disc_value'] = $disc_value;
                        $r['disc'] = $disc;
                        $r['value'] = $value;
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '204';
                    }
                } else {
                    $r['status'] = '201';
                }
            } else {
                $r['status'] = '400';
            }
        }
        //        return $r;
        return json_encode($r);
    }

    public function addCustomAmount(Request $request)
    {
        $b1g1_setup = BuyOneGetOne::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'buy_one_get_ones.pl_id')->get()->toArray();

        $pt_id = $request->p_name;
        $pst_id = $request->_pst_id;
        $pl_id = $request->_pl_id;
        $sell_price = $request->_sell_price;
        $mode = $request->_mode;
        $item_type = $request->_item_type;
        //        $plst_id = $request->_plst_id;
        $item_type = $request->_item_type;
        //        $plst_status = '';
        $free_delete = $request->_free_delete;

        if (!empty($free_delete)) {
            UserRating::where([
                'st_id' => Auth::user()->st_id,
                'stt_id' => Auth::user()->stt_id,
                'ur_status' => 'WAITING FOR REVIEW',
            ])->delete();
            UserRating::where([
                'st_id' => Auth::user()->st_id,
                'stt_id' => Auth::user()->stt_id,
                'ur_status' => 'WAITING FOR CHECKOUT',
            ])->update([
                'ur_status' => 'DONE',
            ]);
        }
        $pl_code = ProductLocation::select('pl_code')->where('id', $pl_id)->get()->first()->pl_code;
        $pls_id = ProductLocationSetup::select('id')->where('pst_id', $pst_id)->get()->first()->id;
        $plst_status_new = [
            'WAITING OFFLINE',
            'INSTOCK APPROVAL'
        ];
        if ($item_type == 'waiting') {
            if ($mode == 'add') {
                //                $plst_status = 'WAITING FOR CHECKOUT';
                //                $update = ProductLocationSetupTransaction::where('id', $plst_id)
                //                    ->whereIn('plst_status', $plst_status_new)
                //                    ->update([
                //                        'plst_status' => $plst_status,
                //                        'u_id' => Auth::user()->id,
                //                    ]);
                if (!empty($update)) {
                    $create = PosTransactionDetail::create([
                        'pt_id' => $pt_id,
                        'pst_id' => $pst_id,
                        'pl_id' => $pl_id,
                        'pos_td_qty' => '1',
                        'pos_td_sell_price' => $sell_price,
                        'pos_td_discount_price' => $sell_price,
                        'pos_td_total_price' => $sell_price,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    if (!empty($create)) {
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                } else {
                    $r['status'] = '400';
                }
            } else {
                if ($pl_code == 'TOKO') {
                    $plst_status = 'INSTOCK';
                    $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('plst_status', 'WAITING FOR CHECKOUT')
                        ->update([
                            'plst_status' => $plst_status,
                            'plst_type' => 'IN',
                        ]);
                    if (!empty($update)) {
                        $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();
                        $update_pls = DB::table('product_location_setups')->where('id', $pls_id)->update([
                            'pls_qty' => ($pls->pls_qty + 1),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        if (!empty($update_pls)) {
                            $r['status'] = '200';
                        } else {
                            $r['status'] = '400';
                        }
                    } else {
                        $r['status'] = '400';
                    }
                } else if (in_array(['pl_code' => $pl_code], $b1g1_setup)) {

                    if (!empty($update)) {
                        $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();
                        $update_pls = DB::table('product_location_setups')->where('id', $pls_id)->update([
                            'pls_qty' => ($pls->pls_qty + 1),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        if (!empty($update_pls)) {
                            $r['status'] = '200';
                        } else {
                            $r['status'] = '400';
                        }
                    } else {
                        $r['status'] = '400';
                    }
                } else {
                    //                    $plst_status = 'WAITING OFFLINE';
                    //                    $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('plst_status', 'WAITING FOR CHECKOUT')
                    //                        ->update([
                    //                            'plst_status' => $plst_status
                    //                        ]);
                    if (!empty($update)) {
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                }
            }
        } else {
            if ($mode == 'add') {
                $insert = DB::table('product_location_setup_transactions')->insertGetId([
                    'pls_id' => $pls_id,
                    'u_id' => Auth::user()->id,
                    'plst_qty' => '1',
                    'plst_type' => 'OUT',
                    'plst_status' => 'WAITING FOR CHECKOUT',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                if (!empty($insert)) {
                    $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();
                    $update = DB::table('product_location_setups')->where('id', $pls_id)->update([
                        'pls_qty' => ($pls->pls_qty - 1),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    if (!empty($update)) {
                        $r['plst_id'] = $insert;
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                } else {
                    $r['status'] = '400';
                }
            } else {
                if ($pl_code == 'TOKO') {
                    $plst_status = 'INSTOCK';
                    $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('plst_status', 'WAITING FOR CHECKOUT')
                        ->update([
                            'plst_status' => $plst_status,
                            'plst_type' => 'IN',
                        ]);
                    if (!empty($update)) {
                        $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();
                        $update_pls = DB::table('product_location_setups')->where('id', $pls_id)->update([
                            'pls_qty' => ($pls->pls_qty + 1),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        if (!empty($update_pls)) {
                            $r['status'] = '200';
                        } else {
                            $r['status'] = '400';
                        }
                    } else {
                        $r['status'] = '400';
                    }
                } else if (in_array(['pl_code' => $pl_code], $b1g1_setup)) {
                    $plst_status = 'INSTOCK';
                    $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('plst_status', 'WAITING FOR CHECKOUT')
                        ->update([
                            'plst_status' => $plst_status,
                            'plst_type' => 'IN',
                        ]);
                    if (!empty($update)) {
                        $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();
                        $update_pls = DB::table('product_location_setups')->where('id', $pls_id)->update([
                            'pls_qty' => ($pls->pls_qty + 1),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        if (!empty($update_pls)) {
                            $r['status'] = '200';
                        } else {
                            $r['status'] = '400';
                        }
                    } else {
                        $r['status'] = '400';
                    }
                } else {
                    $plst_status = 'WAITING OFFLINE';
                    $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('plst_status', 'WAITING FOR CHECKOUT')
                        ->update([
                            'plst_status' => $plst_status
                        ]);
                    if (!empty($update)) {
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                }
            }
        }


        return json_encode($r);
    }

    function changeWaitingStatus(Request $request)
    {

        $b1g1_setup = BuyOneGetOne::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'buy_one_get_ones.pl_id')->get()->toArray();

        $pt_id = $request->_pt_id;
        $pst_id = $request->_pst_id;
        $pl_id = $request->_pl_id;
        $sell_price = $request->_sell_price;
        $mode = $request->_mode;
        $item_type = $request->_item_type;
        $plst_id = $request->_plst_id;
        $plst_status = '';
        $free_delete = $request->_free_delete;
        if (!empty($free_delete)) {
            UserRating::where([
                'st_id' => Auth::user()->st_id,
                'stt_id' => Auth::user()->stt_id,
                'ur_status' => 'WAITING FOR REVIEW',
            ])->delete();
            UserRating::where([
                'st_id' => Auth::user()->st_id,
                'stt_id' => Auth::user()->stt_id,
                'ur_status' => 'WAITING FOR CHECKOUT',
            ])->update([
                'ur_status' => 'DONE',
            ]);
        }
        $pl_code = ProductLocation::select('pl_code')->where('id', $pl_id)->get()->first()->pl_code;
        $pls_id = ProductLocationSetup::select('id')->where('pst_id', $pst_id)->where('pl_id', $pl_id)->get()->first()->id;
        $plst_status_new = [
            'WAITING OFFLINE',
            'INSTOCK APPROVAL'
        ];
        if ($item_type == 'waiting') {
            if ($mode == 'add') {
                $plst_status = 'WAITING FOR CHECKOUT';
                $update = ProductLocationSetupTransaction::where('id', $plst_id)
                    ->whereIn('plst_status', $plst_status_new)
                    ->update([
                        'plst_status' => $plst_status,
                        'u_id' => Auth::user()->id,
                    ]);
                if (!empty($update)) {
                    $create = PosTransactionDetail::create([
                        'pt_id' => $pt_id,
                        'pst_id' => $pst_id,
                        'pl_id' => $pl_id,
                        'pos_td_qty' => '1',
                        'pos_td_sell_price' => $sell_price,
                        'pos_td_discount_price' => $sell_price,
                        'pos_td_total_price' => $sell_price,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    if (!empty($create)) {
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                } else {
                    $r['status'] = '400';
                }
            } else {
                if ($pl_code == 'TOKO') {
                    $plst_status = 'INSTOCK';
                    $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('plst_status', 'WAITING FOR CHECKOUT')
                        ->update([
                            'plst_status' => $plst_status,
                            'plst_type' => 'IN',
                        ]);
                    if (!empty($update)) {
                        $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();
                        $update_pls = DB::table('product_location_setups')->where('id', $pls_id)->update([
                            'pls_qty' => ($pls->pls_qty + 1),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        if (!empty($update_pls)) {
                            $r['status'] = '200';
                        } else {
                            $r['status'] = '400';
                        }
                    } else {
                        $r['status'] = '400';
                    }
                } else if (in_array(['pl_code' => $pl_code], $b1g1_setup)) {
                    $plst_status = 'INSTOCK';
                    $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('plst_status', 'WAITING FOR CHECKOUT')
                        ->update([
                            'plst_status' => $plst_status,
                            'plst_type' => 'IN',
                        ]);
                    if (!empty($update)) {
                        $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();
                        $update_pls = DB::table('product_location_setups')->where('id', $pls_id)->update([
                            'pls_qty' => ($pls->pls_qty + 1),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        if (!empty($update_pls)) {
                            $r['status'] = '200';
                        } else {
                            $r['status'] = '400';
                        }
                    } else {
                        $r['status'] = '400';
                    }
                } else {
                    $plst_status = 'WAITING OFFLINE';
                    $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('plst_status', 'WAITING FOR CHECKOUT')
                        ->update([
                            'plst_status' => $plst_status
                        ]);
                    if (!empty($update)) {
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                }
            }
        } else {
            if ($mode == 'add') {
                $insert = DB::table('product_location_setup_transactions')->insertGetId([
                    'pls_id' => $pls_id,
                    'u_id' => Auth::user()->id,
                    'plst_qty' => '1',
                    'plst_type' => 'OUT',
                    'plst_status' => 'WAITING FOR CHECKOUT',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                if (!empty($insert)) {
                    $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();
                    $update = DB::table('product_location_setups')->where('id', $pls_id)->update([
                        'pls_qty' => ($pls->pls_qty - 1),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    //Tambahan mengurangi QTY stock ketika dimasukkan ke keranjang
                    $pst = ProductStock::select('id', 'ps_barcode', 'ps_qty')->where('id', $pls->pst_id)->get()->first();
                    $update_pst = DB::table('product_stocks')->where('id', $pst->id)->update([
                        'ps_qty' => ($pst->ps_qty - 1),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    if (!empty($update) || !empty($update_pst)) {
                        $r['plst_id'] = $insert;
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                } else {
                    $r['status'] = '400';
                }
            } else {
                if ($pl_code == 'TOKO') {
                    $plst_status = 'INSTOCK';
                    $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('plst_status', 'WAITING FOR CHECKOUT')
                        ->update([
                            'plst_status' => $plst_status,
                            'plst_type' => 'IN',
                        ]);
                    if (!empty($update)) {
                        $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();
                        $update_pls = DB::table('product_location_setups')->where('id', $pls_id)->update([
                            'pls_qty' => ($pls->pls_qty + 1),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        if (!empty($update_pls)) {
                            $r['status'] = '200';
                        } else {
                            $r['status'] = '400';
                        }
                    } else {
                        $r['status'] = '400';
                    }
                } else if (in_array(['pl_code' => $pl_code], $b1g1_setup)) {
                    $plst_status = 'INSTOCK';
                    $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('plst_status', 'WAITING FOR CHECKOUT')
                        ->update([
                            'plst_status' => $plst_status,
                            'plst_type' => 'IN',
                        ]);
                    if (!empty($update)) {
                        $pls = ProductLocationSetup::select('pst_id', 'pls_qty')->where('id', $pls_id)->get()->first();
                        $update_pls = DB::table('product_location_setups')->where('id', $pls_id)->update([
                            'pls_qty' => ($pls->pls_qty + 1),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                        if (!empty($update_pls)) {
                            $r['status'] = '200';
                        } else {
                            $r['status'] = '400';
                        }
                    } else {
                        $r['status'] = '400';
                    }
                } else {
                    $plst_status = 'WAITING OFFLINE';
                    $update = ProductLocationSetupTransaction::where('id', $plst_id)->where('plst_status', 'WAITING FOR CHECKOUT')
                        ->update([
                            'plst_status' => $plst_status
                        ]);
                    if (!empty($update)) {
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                }
            }
        }


        return json_encode($r);
    }

    public function scanBarcode(Request $request)
    {
        $barcode = $request->post('barcode');
        $type = $request->post('type');
        $std_id = $request->post('_std_id');
        $item_type = $request->post('_item_type');

        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

        $b1g1_setup = BuyOneGetOne::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'buy_one_get_ones.pl_id')->get()->toArray();

        $fs = '';
        $b1g1_id = '';
        $b1g1_price = '';
        $free_sock = ProductLocationSetup::select('pls_qty')->where([
            'pst_id' => '15118',
            'pl_id' => '306'
        ])->get()->first();

        if (!empty($free_sock)) {
            $fs = $free_sock->pls_qty;
        }

        $plst_status_new = [
            'WAITING OFFLINE',
            'INSTOCK APPROVAL'
        ];

        if ($item_type == 'waiting') {
            $data = ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id', 'products.psc_id', 'p_name', 'p_color', 'p_sell_price', 'p_price_tag', 'ps_price_tag', 'ps_sell_price', 'sz_name', 'ps_qty', 'pls_qty', 'br_name', 'plst_status', 'product_stocks.id as pst_id', 'product_locations.id as pl_id')
                ->join('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->join('brands', 'brands.id', '=', 'products.br_id')
                //                ->where('pls_qty', '>=', '0')
                ->whereNotIn('pl_code', $exception)
                ->where('product_locations.st_id', '=', Auth::user()->st_id)
                ->whereIn('plst_status', $plst_status_new)
                ->where('product_stocks.ps_barcode', '=', $barcode)
                ->first();
        } else if ($item_type == 'b1g1') {
            $data = ProductStock::select('product_locations.id as pl_id', 'products.psc_id', 'p_name', 'p_color', 'p_sell_price', 'p_price_tag', 'ps_price_tag', 'ps_sell_price', 'sz_name', 'ps_qty', 'pls_qty', 'br_name', 'product_stocks.id as pst_id')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->join('brands', 'brands.id', '=', 'products.br_id')
                ->join('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_locations.st_id', '=', Auth::user()->st_id)
                //                ->where('pls_qty', '>', '0')
                ->whereNotIn('pl_code', $exception)
                ->whereIn('pl_code', $b1g1_setup)
                ->where('product_stocks.ps_barcode', '=', $barcode)
                ->groupBy('product_stocks.id')
                ->first();
        } else {
            //            $data = ProductStock::select('product_locations.id as pl_id', 'products.psc_id', 'p_name', 'p_color', 'p_sell_price', 'p_price_tag', 'ps_price_tag', 'ps_sell_price', 'sz_name', 'ps_qty', 'pls_qty', 'br_name', 'product_stocks.id as pst_id')
            //                ->join('products', 'products.id', '=', 'product_stocks.p_id')
            //                ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            //                ->join('brands', 'brands.id', '=', 'products.br_id')
            //                ->join('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
            //                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            //                ->where('product_locations.st_id', '=', Auth::user()->st_id)
            //                ->where('pls_qty', '>', '0')
            //                ->whereNotIn('pl_code', $exception)
            //                ->whereIn('pl_code', ['TOKO'])
            //                ->where('product_stocks.ps_barcode', '=', $barcode)
            //                ->groupBy('product_stocks.id')
            //                ->first();

            $data = ProductStock::select('product_locations.id as pl_id', 'products.psc_id', 'p_name', 'pl_code', 'p_color', 'p_sell_price', 'p_price_tag', 'ps_price_tag', 'ps_sell_price', 'sz_name', 'ps_qty', 'pls_qty', 'br_name', 'product_stocks.id as pst_id', 'products.article_id as article_id')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->join('brands', 'brands.id', '=', 'products.br_id')
                ->join('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_locations.st_id', '=', Auth::user()->st_id)
                //                ->where('pls_qty', '>', '0')
                ->whereNotIn('pl_code', $exception)
                ->whereIn('pl_code', ['TOKO'])
                ->where('product_stocks.ps_barcode', '=', $barcode)
                ->groupBy('product_stocks.id')
                ->first();
        }
        if (!empty($data)) {
            $row = $data;
            $check_setup = ProductLocationSetup::select('product_locations.id as pl_id', 'product_location_setups.id as pls_id', 'pl_code', 'pl_name', 'pls_qty')
                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_locations.st_id', '=', Auth::user()->st_id)
                ->where('pst_id', $row->pst_id)
                ->where('pls_qty', '>=', '0')
                ->whereNotIn('pl_code', $exception)->get();
            $bin = '';
            $sell_price = 0;
            $bandrol = 0;
            if (!empty($row->ps_price_tag)) {
                $bandrol = $row->ps_price_tag;
            } else {
                $bandrol = $row->p_price_tag;
            }
            if ($type == 'RESELLER') {
                if (!empty($row->ps_price_tag)) {
                    $sell_price = $row->ps_price_tag;
                } else {
                    $sell_price = $row->p_price_tag;
                }
            } else {
                if (!empty($row->ps_sell_price)) {
                    $sell_price = $row->ps_sell_price;
                } else {
                    $sell_price = $row->p_sell_price;
                }
            }
            $set_discount_check = ProductDiscountDetail::select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                ->where('pst_id', '=', $row->pst_id)
                ->where('std_id', '=', $std_id)
                ->where('product_discounts.st_id', '=', Auth::user()->st_id)
                ->where('pd_type', '=', 'b1g1')
                ->where('pd_date', '>=', date('Y-m-d'))
                ->exists();
            if ($set_discount_check) {
                $set_discount = ProductDiscountDetail::select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                    ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                    ->where('pst_id', '=', $row->pst_id)
                    ->where('std_id', '=', $std_id)
                    ->where('product_discounts.st_id', '=', Auth::user()->st_id)->where('pd_type', '=', 'b1g1')
                    ->get()->first();
            } else {
                $set_discount = ProductDiscountDetail::select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                    ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                    ->where('pst_id', '=', $row->pst_id)
                    ->where('pd_date', '>=', date('Y-m-d'))
                    ->where('product_discounts.st_id', '=', Auth::user()->st_id)
                    ->where('std_id', '=', $std_id)->where('pd_type', '!=', 'b1g1')->get()->first();
                if (empty($set_discount)) {
                    $set_discount = ProductDiscountDetail::select('pd_type', 'pd_value', 'st_id', 'std_id', 'pd_date')
                        ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
                        ->where('pst_id', '=', $row->pst_id)
                        ->whereNull('product_discounts.st_id')
                        ->where('pd_date', '>=', date('Y-m-d'))
                        ->where('std_id', '=', $std_id)->where('pd_type', '!=', 'b1g1')->get()->first();
                }
            }
            if (!empty($set_discount)) {
                if (date('Y-m-d') <= $set_discount->pd_date) {
                    if (!empty($row->ps_price_tag)) {
                        $price_tag = $row->ps_price_tag;
                    } else {
                        $price_tag = $row->p_price_tag;
                    }
                    if ($set_discount->pd_type == 'percent') {
                        $sell_price = $price_tag - ($price_tag / 100 * $set_discount->pd_value);
                    } else if ($set_discount->pd_type == 'amount') {
                        $sell_price = $price_tag - $set_discount->pd_value;
                    } else {
                        $sell_price = $price_tag;
                        $b1g1_id = $row->pst_id;
                        $b1g1_price = $sell_price;
                    }
                }
            }
            if (!empty($check_setup)) {
                if ($item_type == 'waiting') {
                    $status = '<span class="btn-lg btn-warning">' . $row->plst_status . '</span>';
                    $bin = '<span class="btn-lg btn-info">' . strtoupper($check_setup->first()->pl_code) . '</span>';
                } else {
                    $status = '';
                    $bin = '<span class="btn-lg btn-info">' . $row->pls_qty . '</span>';
                }
            }
            $r['status'] = '200';
            $r['p_name'] = strtoupper('[' . $row->br_name . '] ' . $row->p_name . ' ' . $row->p_color . ' ' . $row->sz_name);
            $r['pst_id'] = $row->pst_id;
            $r['fs'] = $fs;
            $r['pl_id'] = $row->pl_id;
            $r['pls_qty'] = $row->pls_qty;
            $r['psc_id'] = $row->psc_id;
            $r['plst_id'] = $row->plst_id;
            $r['sell_price'] = $sell_price;
            $r['bandrol'] = $bandrol;
            $r['b1g1_id'] = $b1g1_id;
            $r['b1g1_price'] = $b1g1_price;
        } else {
            $r['status'] = '400';
            $r['barang'] = $item_type;
        }
        return json_encode($r);
    }

    public function totalDiscount(Request $request)
    {
        $datas = $request->post('formData');
        // get first iteration
        $discountType = $datas[0]['value'];
        $total = 0;

        if ($discountType == 'percentage') {
            foreach ($datas as $key => $data) {
                if ($key == 0) {
                    continue;
                }
                $total += $data['value'];
            }
        } else {
            // foreach but skip first iteration
            foreach ($datas as $key => $data) {
                if ($key == 0) {
                    continue;
                }
                $total += $data['value'];
            }
        }

        $response = [
            'status' => '200',
            'total' => $total,
            'discountType' => $discountType
        ];

        return json_encode($response);
    }
}
