<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\PosTransaction;
use App\Models\Store;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettlementController extends Controller
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

    protected function UserActivity($activity)
    {
        UserActivity::create([
            'user_id' => Auth::user()->id,
            'ua_description' => $activity,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function index()
    {

        
        $user = new User();
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        //        $store_onl = Store::where('st_name', 'like', '%ONLINE%')->get();
        $data = [
            'title' => $title,
            'subtitle' => 'Settlement',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'statusses' => PosTransaction::statusList(),
            'st_id' => Store::where('st_delete', '!=', '1')->orderBy('st_name')->pluck('st_name', 'id'),
            'pm_id' => PaymentMethod::where('pm_delete', '!=', '1')->groupBy('pm_name')->orderBy('pm_name')->pluck('pm_name'),
        ];
        return view('app.settlement.settlement', compact('data'));
    }

    public function reloadPaymentMethod(Request $request)
    {
        $st_id = $request->input('st_id') ?? 0;
        $is_online = Store::where('id', $st_id)->where('st_name', 'like', '%ONLINE%')->exists();
        $paymentMethods = PaymentMethod::where('pm_delete', '!=', '1')
            ->where('st_id', $st_id)
            ->orderBy('pm_name')
            ->pluck('pm_name', 'id');

        // Add DEPOSIT SHOPEE and DEPOSIT TIKTOK to payment methods
        if (!$paymentMethods->isEmpty() && $is_online) {
            if (!$paymentMethods->contains('DEPOSIT SHOPEE')) {
                $paymentMethods['deposit_shopee'] = 'DEPOSIT SHOPEE';
            }
            if (!$paymentMethods->contains('DEPOSIT TIKTOK')) {
                $paymentMethods['deposit_tiktok'] = 'DEPOSIT TIKTOK';
            }
        }

        return view('app.settlement._payment_method', compact('paymentMethods'));
    }

    public function getDatatables(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $st_id = $request->input('st_id') ?? 0;
        $pm_id = $request->input('pm_id') ?? 0;
        $status_trx = $request->input('status_trx') ?? '';

        $data = $this->getAllTransactions($start_date, $end_date, $st_id, $pm_id, $status_trx);

        $combinedData = $data->sortBy('pos_invoice');

        return datatables($combinedData)
            ->addIndexColumn()
            ->editColumn('sub_payment', function ($row) {
                if ($row->sub_payment == 1) {
                    return 'CASH';
                }

                if ($row->sub_payment == 2) {
                    return 'COD';
                }

                if ($row->sub_payment == 3) {
                    return 'ON US';
                }

                if ($row->sub_payment == 4) {
                    return 'OFF US';
                }

                return '-';
            })
            ->editColumn('pos_status', function ($collection) {
                if ($collection->pos_status === 'REFUND') {
                    return '<button class="btn btn-sm btn-dark">Refund</button>';
                } elseif ($collection->pos_status === 'DONE') {
                    return '<button class="btn btn-sm btn-success">DONE</button>';
                } elseif ($collection->pos_status === 'DP') {
                    return '<button class="btn btn-sm btn-info">DP</button>';
                } else {
                    return '<button class="btn btn-sm btn-warning">' . $collection->pos_status . '</button>';
                }
            })
            ->editColumn('is_settle', function ($collection) {
                if ($collection->is_settle) {
                    return '<button class="btn btn-sm btn-success">SETTLED</button>';
                } else {
                    return '<button class="btn btn-sm btn-secondary">UNSETTLED</button>';
                }
            })
            ->rawColumns(['pos_status', 'is_settle'])
            ->make(true);
    }

    public function getDetailSettlement(){
        $trasaction_date = null;
        $receipt_number = null;
        $order_number = null;
        $store_name = null;
        $trx_status = null;
        $payment_status = null;
        $outstanding_balance = null;
        $payment_method_1 = null;
        $sub_payment_method_1 = null;
        $payment_amount_1 = null;
        $payment_method_2 = null;
        $sub_payment_method_2 = null;
        $payment_amount_2 = null;


        // Financial Summary
        $down_payment = null;
        $gross_sales = null;
        $total_discount = null;
        $net_sales = null;
        $total_payment = null;
        $cogs = null;
        $seller_voucher = null;
        $total_admin_fee = null;
        $outstanding_balance = null;
        $total_dana_cair = null;
        $gross_margin = null;
        $margin_percentage = null;

        $result = [

        ];
    }

    public function getNetsalesPerPaymentMethod(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $st_id = $request->input('st_id') ?? 0;
        $pm_id = $request->input('pm_id') ?? 0;
        $status_trx = $request->input('status_trx') ?? '';

        $main = DB::table('payment_methods')
            ->select(
                'payment_methods.pm_name',
                DB::raw('SUM(COALESCE(ts_pos_transactions.pos_payment, 0)) as total_payment'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.is_settle = TRUE THEN COALESCE(ts_pos_transactions.pos_payment, 0) ELSE 0 END)  AS settled_payment'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.is_settle = FALSE THEN COALESCE(ts_pos_transactions.pos_payment, 0) ELSE 0 END)  AS unsettled_payment')
            )
            ->leftJoin('pos_transactions', function ($join) use ($start_date, $end_date, $st_id, $pm_id, $status_trx) {
                $join->on('pos_transactions.pm_id', '=', 'payment_methods.id')
                    ->whereBetween('pos_transactions.created_at', [$start_date, $end_date])
                    ->when($st_id != 0, function ($query) use ($st_id) {
                        return $query->where('pos_transactions.st_id', $st_id);
                    })
                    ->when($pm_id != 0 && !in_array($pm_id, ['DEPOSIT SHOPEE', 'DEPOSIT TIKTOK']), function ($query) use ($pm_id) {
                        return $query->where('payment_methods.pm_name', $pm_id);
                    })
                    ->when($status_trx != '', function ($query) use ($status_trx) {
                        return $query->where('pos_transactions.pos_status', $status_trx);
                    });
            })
            ->where('payment_methods.st_id', '=', $st_id)
            ->where('payment_methods.pm_name', '!=', 'CASH')
            ->when($pm_id != 0 && !in_array($pm_id, ['DEPOSIT SHOPEE', 'DEPOSIT TIKTOK']), function ($query) use ($pm_id) {
                return $query->where('payment_methods.pm_name', $pm_id);
            })
            ->groupBy('payment_methods.pm_name')
            ->get();

        $partial = DB::table('payment_methods')
            ->select(
                'payment_methods.pm_name',
                DB::raw('SUM(COALESCE(ts_pos_transactions.pos_payment_partial, 0)) as total_payment'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.is_settle = TRUE THEN COALESCE(ts_pos_transactions.pos_payment_partial, 0) ELSE 0 END)  AS settled_payment'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.is_settle = FALSE THEN COALESCE(ts_pos_transactions.pos_payment_partial, 0) ELSE 0 END)  AS unsettled_payment')
            )
            ->leftJoin('pos_transactions', function ($join) use ($start_date, $end_date, $st_id, $pm_id, $status_trx) {
                $join->on('pos_transactions.pm_id_partial', '=', 'payment_methods.id')
                    ->whereBetween('pos_transactions.created_at', [$start_date, $end_date])
                    ->when($st_id != 0, function ($query) use ($st_id) {
                        return $query->where('pos_transactions.st_id', $st_id);
                    })
                    ->when($pm_id != 0 && !in_array($pm_id, ['DEPOSIT SHOPEE', 'DEPOSIT TIKTOK']), function ($query) use ($pm_id) {
                        return $query->where('payment_methods.pm_name', $pm_id);
                    })
                    ->when($status_trx != '', function ($query) use ($status_trx) {
                        return $query->where('pos_transactions.pos_status', $status_trx);
                    });
            })
            ->where('payment_methods.st_id', '=', $st_id)
            ->where('payment_methods.pm_name', '!=', 'CASH')
            ->when($pm_id != 0 && !in_array($pm_id, ['DEPOSIT SHOPEE', 'DEPOSIT TIKTOK']), function ($query) use ($pm_id) {
                return $query->where('payment_methods.pm_name', $pm_id);
            })
            ->groupBy('payment_methods.pm_name')
            ->get();

        $cash_only = DB::table('pos_transactions')
            ->leftJoin('stores', 'stores.id', '=', 'st_id')
            ->leftJoin('payment_methods as pm_main', 'pm_main.id', '=', 'pm_id')
            ->leftJoin('payment_methods as pm_partial', 'pm_partial.id', '=', 'pm_id_partial')
            ->select([
                DB::raw('\'CASH\' as pm_name'),
                DB::raw('SUM(COALESCE(pos_real_price, 0)) as total_payment'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.is_settle = TRUE THEN COALESCE(pos_real_price, 0) ELSE 0 END) AS settled_payment'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.is_settle = FALSE THEN COALESCE(pos_real_price, 0) ELSE 0 END) AS unsettled_payment')
            ])
            ->whereBetween('pos_transactions.created_at', [$start_date, $end_date])
            ->when($st_id != 0, function ($query) use ($st_id) {
                return $query->where('pos_transactions.st_id', $st_id);
            })
            ->when($pm_id != 0, function ($query) use ($pm_id) {
                if ($pm_id == 'CASH') {
                    return $query->where('pm_main.pm_name', 'CASH');
                } else {
                    return $query->whereRaw('1 = 0'); // Return no results when pm_id is not CASH
                }
            })
            ->when($status_trx != '', function ($query) use ($status_trx) {
                return $query->where('pos_transactions.pos_status', $status_trx);
            })
            ->where(function ($query) {
                $query->where('pm_main.pm_name', 'CASH')
                    ->orWhere(function ($q) {
                        $q->where('pm_partial.pm_name', 'CASH')
                            ->whereNull('pos_payment_partial');
                    });
            })
            ->get();

        $online = DB::table('pos_transactions')
            ->select([
                DB::raw('CONCAT(\'DEPOSIT \', UPPER(platform_name)) as pm_name'),
                DB::raw('SUM(COALESCE(pos_real_price, 0)) as total_payment'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.is_settle = TRUE THEN COALESCE(pos_real_price, 0) ELSE 0 END) AS settled_payment'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.is_settle = FALSE THEN COALESCE(pos_real_price, 0) ELSE 0 END) AS unsettled_payment')
            ])
            ->join('online_transactions', 'pos_invoice', '=', 'order_number')
            ->whereBetween('pos_transactions.created_at', [$start_date, $end_date])
            ->when($st_id != 0, function ($query) use ($st_id) {
                return $query->where('pos_transactions.st_id', $st_id);
            })
            ->when($pm_id == 'DEPOSIT SHOPEE', function ($query) {
                return $query->where('online_transactions.platform_name', 'shopee');
            })
            ->when($pm_id == 'DEPOSIT TIKTOK', function ($query) {
                return $query->where('online_transactions.platform_name', 'tiktok');
            })
            ->when($status_trx != '', function ($query) use ($status_trx) {
                return $query->where('pos_transactions.pos_status', $status_trx);
            })
            ->groupBy('platform_name')
            ->get();

        $merged = $main->merge($partial)->merge($cash_only)->merge($online)
            ->groupBy('pm_name')
            ->map(function ($group) {
                return $group->reduce(function ($carry, $item) {
                    $carry->pm_name = $item->pm_name;
                    $carry->total_payment += $item->total_payment;
                    $carry->settled_payment += $item->settled_payment;
                    $carry->unsettled_payment += $item->unsettled_payment;
                    return $carry;
                }, (object) ['pm_name' => '', 'total_payment' => 0, 'settled_payment' => 0, 'unsettled_payment' => 0]);
            })
            ->filter(function ($item) {
                return $item->total_payment != 0;
            })
            ->values();
        return view('app.settlement._payment_calc_cards', compact('merged'));
    }

    public function getTotalNetsales(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $st_id = $request->input('st_id') ?? 0;
        $pm_id = $request->input('pm_id') ?? 0;
        $status_trx = $request->input('status_trx') ?? '';

        $transactions = $this->getAllTransactions(
            $start_date,
            $end_date,
            $st_id,
            0,
            $status_trx
        );

        $totalNetsales = $transactions->sum('netsales');

        return response()->json(['total_netsales' => $totalNetsales]);
    }

    public function bulkUpdateStatus(Request $request) {
        $settled_transaction_id = $request->checked_ids;

        foreach ($settled_transaction_id as $key => $value) {
            PosTransaction::query()->where('id',$value)->update(['is_settle' => 1]);
        }
    }

    private function getAllTransactions($start_date, $end_date, $st_id, $pm_id, $status_trx)
    {
        $main = DB::table('pos_transactions')
            ->leftJoin('stores', 'stores.id', '=', 'st_id')
            ->leftJoin('pos_transaction_details', 'pos_transactions.id', '=', 'pt_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pm_id')
            ->select([
                DB::raw('DATE(ts_pos_transactions.created_at) as date'),
                'pos_invoice',
                'st_name',
                DB::raw('SUM(pos_td_qty) as qty'),
                DB::raw('MAX(pos_payment) as netsales'),
                'pm_name',
                'sub_payment',
                'pos_status',
                'is_settle',
                'pos_transactions.id'
            ])
            ->whereBetween('pos_transactions.created_at', [$start_date, $end_date])
            ->when($st_id != 0, function ($query) use ($st_id) {
                return $query->where('pos_transactions.st_id', $st_id);
            })
            ->when($pm_id != 0 && !in_array($pm_id, ['DEPOSIT SHOPEE', 'DEPOSIT TIKTOK']), function ($query) use ($pm_id) {
                return $query->where('payment_methods.pm_name', $pm_id);
            })
            ->when($status_trx != '', function ($query) use ($status_trx) {
                return $query->where('pos_transactions.pos_status', $status_trx);
            })
            ->where('payment_methods.pm_name', '!=', 'CASH')
            ->whereNull('pos_payment_partial')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('online_transactions')
                    ->whereRaw('ts_online_transactions.order_number = ts_pos_transactions.pos_invoice');
            })
            ->groupBy([
                'pos_transactions.id',
            ])
            ->get();

        $online = DB::table('pos_transactions')
            ->leftJoin('stores', 'stores.id', '=', 'st_id')
            ->leftJoin('pos_transaction_details', 'pos_transactions.id', '=', 'pt_id')
            ->join('online_transactions', 'online_transactions.order_number', '=', 'pos_invoice')
            ->select([
                DB::raw('DATE(ts_pos_transactions.created_at) as date'),
                'pos_invoice',
                'st_name',
                DB::raw('SUM(pos_td_qty) as qty'),
                DB::raw('MAX(pos_real_price) as netsales'),
                DB::raw('CONCAT(\'DEPOSIT \', UPPER(platform_name)) as pm_name'),
                'sub_payment',
                'pos_status',
                'is_settle',
                'pos_transactions.id'
            ])
            ->whereBetween('pos_transactions.created_at', [$start_date, $end_date])
            ->when($st_id != 0, function ($query) use ($st_id) {
                return $query->where('pos_transactions.st_id', $st_id);
            })
            ->when($pm_id == 'DEPOSIT SHOPEE', function ($query) {
                return $query->where('online_transactions.platform_name', 'shopee');
            })
            ->when($pm_id == 'DEPOSIT TIKTOK', function ($query) {
                return $query->where('online_transactions.platform_name', 'tiktok');
            })
            ->when($status_trx != '', function ($query) use ($status_trx) {
                return $query->where('pos_transactions.pos_status', $status_trx);
            })
            ->groupBy([
                'pos_transactions.id',
            ])
            ->get();

        $partial = DB::table('pos_transactions')
            ->leftJoin('stores', 'stores.id', '=', 'st_id')
            ->leftJoin('pos_transaction_details', 'pos_transactions.id', '=', 'pt_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pm_id_partial')
            ->select([
                DB::raw('DATE(ts_pos_transactions.created_at) as date'),
                'pos_invoice',
                'st_name',
                DB::raw('SUM(pos_td_qty) as qty'),
                DB::raw('MAX(CASE WHEN st_name like \'ONLINE%\' THEN pos_real_price ELSE pos_payment_partial END) as netsales'),
                'pm_name',
                'sub_payment',
                'pos_status',
                'is_settle',
                'pos_transactions.id'
            ])
            ->whereBetween('pos_transactions.created_at', [$start_date, $end_date])
            ->when($st_id != 0, function ($query) use ($st_id) {
                return $query->where('pos_transactions.st_id', $st_id);
            })
            ->when($pm_id != 0 && !in_array($pm_id, ['DEPOSIT SHOPEE', 'DEPOSIT TIKTOK']), function ($query) use ($pm_id) {
                return $query->where('payment_methods.pm_name', $pm_id);
            })
            ->when($status_trx != '', function ($query) use ($status_trx) {
                return $query->where('pos_transactions.pos_status', $status_trx);
            })
            ->where('payment_methods.pm_name', '!=', 'CASH')
            ->groupBy([
                'pos_transactions.id',
            ])
            ->get();

        $cash_only = DB::table('pos_transactions')
            ->leftJoin('stores', 'stores.id', '=', 'st_id')
            ->leftJoin('pos_transaction_details', 'pos_transactions.id', '=', 'pt_id')
            ->join('payment_methods as pm_main', 'pm_main.id', '=', 'pm_id')
            ->select([
                DB::raw('DATE(ts_pos_transactions.created_at) as date'),
                'pos_invoice',
                'st_name',
                DB::raw('SUM(pos_td_qty) as qty'),
                DB::raw('MAX(pos_real_price) as netsales'),
                DB::raw('\'CASH\' as pm_name'),
                'sub_payment',
                'pos_status',
                'is_settle',
                'pos_transactions.id'
            ])
            ->whereBetween('pos_transactions.created_at', [$start_date, $end_date])
            ->where(function ($query) {
                $query->where('pm_main.pm_name', 'CASH')
                    ->orWhereNull('pm_id');
            })
            ->when($st_id != 0, function ($query) use ($st_id) {
                return $query->where('pos_transactions.st_id', $st_id);
            })
            ->when($pm_id != 0, function ($query) use ($pm_id) {
                if ($pm_id == 'CASH') {
                    return $query;
                } else {
                    return $query->whereRaw('1 = 0'); // Return no results when pm_id is not CASH
                }
            })
            ->when($status_trx != '', function ($query) use ($status_trx) {
                return $query->where('pos_transactions.pos_status', $status_trx);
            })
            ->groupBy([
                'pos_transactions.id',
            ])
            ->get();

        // dd($cash_only);

        return $main->merge($partial)->merge($cash_only)->merge($online);
    }
}
