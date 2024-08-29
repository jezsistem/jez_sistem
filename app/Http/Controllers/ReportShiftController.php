<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\Store;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserShift;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportShiftController extends Controller
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
            'st_id' => Store::selectRaw('ts_stores.id as sid, CONCAT(st_name) as store')
                ->where('st_delete', '!=', '1')
                ->orderByDesc('sid')->pluck('store', 'sid'),
            'segment' => request()->segment(1),
        ];

        return view('app.report.shift.shift', compact('data'));
    }

    public function current_shift()
    {
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
            'st_id' => Store::selectRaw('ts_stores.id as sid, CONCAT(st_name) as store')
                ->where('st_delete', '!=', '1')
                ->orderByDesc('sid')->pluck('store', 'sid'),
            'segment' => request()->segment(1),
        ];

        return view('app.report.current_shift.current_shift', compact('data'));
    }

    public function getDatatablesCurrentShift(Request $request)
    {
        $st_id = $request->st_id;

        $u_id = Auth::user()->id;
        $date_now = date('Y-m-d');

//        if ($request->ajax()) {
//            return datatables()->of(
//                DB::table('users')
//                    ->select(
//                        'users.id',
//                        'stores.id as st_id',
//                        'users.u_name',
//                        'user_shifts.start_time',
//                        'user_shifts.date',
//                        'stores.st_name',
////                        DB::raw('SUM(CASE WHEN ts_pos_transactions.u_id = 247 AND DATE(ts_pos_transactions.created_at) = 2024-08-28 THEN ts_pos_transactions.pos_real_price ELSE 0 END) AS total_pos_real_price')
//                        DB::raw('SUM(CASE WHEN ts_pos_transactions.u_id = '.$u_id.' AND ts_pos_transactions.pos_refund = "0" THEN ts_pos_transactions.pos_real_price ELSE 0 END) as total_pos_real_price')
//                    )
//                    ->join('stores', 'users.st_id', '=', 'stores.id')
//                    ->join('user_shifts', 'users.id', '=', 'user_shifts.user_id')
//                    ->leftJoin('pos_transactions', 'pos_transactions.u_id', '=', 'users.id')
//                    ->where('users.id', '=', $u_id)
//                    ->where(DB::raw('DATE(ts_user_shifts.date)'), '=', $date_now)
//                    ->groupBy(
//                        'users.id',
//                        'stores.id',
//                        'users.u_name',
//                        'user_shifts.start_time',
//                        'user_shifts.date',
//                        'stores.st_name'
//                    )
//            )
//                ->editColumn('start_time', function ($row) {
//                    return date('H:i:s', strtotime($row->start_time));
//                })
//                ->editColumn('total_pos_real_price', function ($row) {
//                    return 'Rp. ' . number_format($row->total_pos_real_price);
//                })
//                ->rawColumns(['total_pos_real_price'])
//                ->addIndexColumn()
//                ->make(true);
//        }

        if ($request->ajax()) {
            return datatables()->of(
                User::select(
                    'users.id',
                    'stores.id as st_id',
                    'users.u_name',
                    'user_shifts.start_time',
                    'user_shifts.date',
                    'stores.st_name',
                    // Uncomment the following line if you need the SUM aggregation
                    DB::raw('SUM(CASE WHEN ts_pos_transactions.u_id = 242 AND DATE(ts_pos_transactions.created_at) = 2024-08-28 THEN ts_pos_transactions.pos_real_price ELSE 0 END) as total_pos_real_price')
//                    DB::raw('SUM(CASE WHEN ts_pos_transactions.u_id = 242 AND DATE(ts_pos_transactions.created_at) = CURDATE() THEN ts_pos_transactions.pos_real_price ELSE 0 END) AS total_pos_real_price')
                )
                    ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
                    ->join('user_shifts', 'users.id', '=', 'user_shifts.user_id')
                    ->where(DB::raw('DATE(ts_user_shifts.date)'), '=', $date_now)
                    ->where('user_id', '=', $u_id)
                    // Uncomment the following lines if you need the INNER JOIN and additional conditions
                    ->leftJoin('pos_transactions', function ($join) {
                        $join->on('users.id', '=', 'pos_transactions.u_id')
                            ->where(DB::raw('DATE(ts_user_shifts.date)'), '=', '2024-07-23');
                    })
                    ->groupBy(
                        'users.id',
                        'users.u_name',
                        'user_shifts.start_time',
                        'user_shifts.date',
                        'stores.st_name'
                    // Uncomment the following line if you need to include the column in the GROUP BY clause
                    // 'user_shifts.laba_shift'
                    )
            )
                ->editColumn('start_time', function ($row) {
                    return date('H:i:s', strtotime($row->start_time));
                })
                ->editColumn('total_pos_payment_price', function ($row) {
                    return 'Rp. ' . number_format($row->total_pos_payment_price);
                })
                ->editColumn('start_time_original', function ($row) {
                    return $row->start_time;
                })
                ->rawColumns(['total_pos_payment_price', 'total_pos_real_price', 'laba_shift', 'difference'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getDatatables(Request $request)
    {
        $st_id = $request->st_id;

        if ($request->ajax()) {
            return datatables()->of(User::select(
                'users.id',
                'stores.id as st_id',
                'users.u_name',
                'user_shifts.start_time',
                'user_shifts.end_time',
                'user_shifts.date',
                'stores.st_name',
                'user_shifts.laba_shift',
                DB::raw('SUM(CASE WHEN ts_pos_transactions.st_id = ts_users.st_id AND ts_pos_transactions.pos_refund = "0" THEN ts_pos_transactions.pos_real_price ELSE 0 END) as total_pos_real_price'),
                DB::raw('SUM(CASE WHEN ts_pos_transactions.st_id = ts_users.st_id AND ts_pos_transactions.pos_refund = "0" THEN ts_pos_transactions.pos_payment ELSE 0 END) as total_pos_payment_price'),
            )
                ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
                ->leftjoin('user_shifts', 'users.id', '=', 'user_shifts.user_id')
                ->leftjoin('pos_transactions', function ($join) {
                    $join->on('users.id', '=', 'pos_transactions.u_id')
                        ->where('pos_transactions.pos_refund', '=', '0')
                        ->whereBetween('pos_transactions.created_at', [DB::raw('ts_user_shifts.start_time'), DB::raw('ts_user_shifts.end_time')]);
                })
                ->groupBy(
                    'users.id',
                    'user_shifts.id',
                    'users.u_name',
                    'user_shifts.start_time',
                    'user_shifts.date',
                    'user_shifts.end_time',
                    'stores.st_name',
                    'user_shifts.laba_shift'
                )->orderBy('user_shifts.id', 'DESC'))
                ->editColumn('start_time', function ($row) {
                    return date('H:i:s', strtotime($row->start_time));
                })
                ->editColumn('end_time', function ($row) {
                    return date('H:i:s', strtotime($row->end_time));
                })
                ->editColumn('total_pos_payment_price', function ($row) {
                    return 'Rp. ' . $row->total_pos_payment_price;
                })
                ->editColumn('total_pos_real_price', function ($row) {
                    return 'Rp. ' . $row->total_pos_real_price;
                })
                ->editColumn('laba_shift', function ($row) {
                    return 'Rp. ' . $row->laba_shift;
                })
                ->editColumn('difference', function ($row) {
                    return 'Rp. ' . $row->total_pos_real_price - $row->total_pos_payment_price;
                })
                ->editColumn('start_time_original', function ($row) {
                    return $row->start_time;
                })
                ->editColumn('end_time_original', function ($row) {
                    return $row->end_time;
                })
                ->rawColumns(['total_pos_payment_price', 'total_pos_real_price', 'laba_shift', 'difference'])
                ->filter(function ($instance) use ($request) {
                    if ($request->get('st_id') != null) {
                        $instance->where('users.st_id', $request->get('st_id'));
                    }

                    if ($request->get('search')) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('users.u_name', 'LIKE', "%$search%")
                                ->orWhere('stores.st_name', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function current_shift_detail(Request $request)
    {
        try {
            $data['name'] = $request->u_name;
            $data['start_time'] = $request->start_time_original;
//            $data['end_time'] = $request->end_time_original;
            $data['store'] = $request->st_name;
            $data['total_pos_real_price'] = $request->total_pos_real_price;
//            $data['total_pos_payment_price'] = $request->total_pos_payment_price;
//            $data['laba_shift'] = $request->laba_shift;
//            $data['difference'] = $request->difference;
            $data['user_id'] = $request->id;
            $data['st_id'] = $request->st_id;
            $data['st_name'] = $request->st_name;

            $currencyValue = $request->laba_shift;
//            $laba_shift = (int)preg_replace('/\D/', '', $currencyValue);
//            $data['laba_shift'] = $laba_shift;

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
                ->whereBetween('pos_transactions.created_at', [$data['start_time'], $data['end_time']])
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
                $total_payment_two  += $paymentMethod->total_pos_partials;
            }

            return view('app.report.shift._shift_detail',
                compact('data', 'cashMethods', 'bcaMethods', 'bniMethods', 'briMethods', 'transferBca', 'transferBni', 'transferBri',
                    'total_sold_items', 'total_refund_items', 'total_payment_two','total_expected_payment', 'total_actual_payment', 'methodsPartials'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    public function detail(Request $request)
    {
        try {
            $data['name'] = $request->u_name;
            $data['start_time'] = $request->start_time_original;
            $data['end_time'] = $request->end_time_original;
            $data['store'] = $request->st_name;
            $data['total_pos_real_price'] = $request->total_pos_real_price;
            $data['total_pos_payment_price'] = $request->total_pos_payment_price;
//            $data['laba_shift'] = $request->laba_shift;
            $data['difference'] = $request->difference;
            $data['user_id'] = $request->id;
            $data['st_id'] = $request->st_id;
            $data['st_name'] = $request->st_name;

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
                ->whereBetween('pos_transactions.created_at', [$data['start_time'], $data['end_time']])
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
                $total_payment_two  += $paymentMethod->total_pos_partials;
            }

            return view('app.report.shift._shift_detail',
                compact('data', 'cashMethods', 'bcaMethods', 'bniMethods', 'briMethods', 'transferBca', 'transferBni', 'transferBri',
                    'total_sold_items', 'total_refund_items', 'total_payment_two','total_expected_payment', 'total_actual_payment', 'methodsPartials'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function productSold(Request $request)
    {
        try {
            $data = User::select(
                'stores.st_name', 'stores.id as st_id',
            )
                ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
                ->join('user_shifts', 'users.id', '=', 'user_shifts.user_id')
//                ->join('pos_transactions', function($join) {
//                    $join->on('users.id', '=', 'pos_transactions.u_id')
//                        ->whereBetween('pos_transactions.created_at', [DB::raw('ts_user_shifts.start_time'), DB::raw('ts_user_shifts.end_time')]);
//                    $join->join('pos_transaction_details', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id');
//                })
                ->where('users.id', '=', $request->id)
                ->groupBy(
                    'users.id',
                    'users.u_name',
                    'user_shifts.start_time',
                    'user_shifts.end_time',
                    'user_shifts.date',
                    'stores.st_name',
                )
                ->first();

            $items = $this->getDataItems($data, "0", $request);

            return view('app.report.shift._shift_product_sold', compact('items'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function totalProductSold($data, $user_id, $start_time, $end_time)
    {
        // get pos_transaction, then get pos_transaction_details with related pos_transaction, then sum the pos_td_qty
        $totalProductSold = PosTransaction::select(
            DB::raw('SUM(ts_pos_transaction_details.pos_td_qty) as total_product_sold')
        )
            ->join('pos_transaction_details', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
            ->where('pos_transactions.u_id', '=', $user_id)
            ->where('pos_transactions.st_id', "=", $data['st_id'])
            ->whereBetween('pos_transactions.created_at', [$start_time, $end_time])
            ->first();

        return $totalProductSold->total_product_sold;
    }

    public function productRefund(Request $request)
    {
        try {
            $data = User::select(
                'stores.st_name', 'stores.id as st_id',
            )
                ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
                ->join('user_shifts', 'users.id', '=', 'user_shifts.user_id')
                ->join('pos_transactions', function ($join) {
                    $join->on('users.id', '=', 'pos_transactions.u_id')
                        ->whereBetween('pos_transactions.created_at', [DB::raw('ts_user_shifts.start_time'), DB::raw('ts_user_shifts.end_time')]);
                    $join->join('pos_transaction_details', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id');
                })
                ->where('users.id', '=', $request->id)
                ->groupBy(
                    'users.id',
                    'users.u_name',
                    'user_shifts.start_time',
                    'user_shifts.end_time',
                    'user_shifts.date',
                    'stores.st_name',
                )
                ->first();

            $items = $this->getDataItems($data, "1", $request);

            return view('app.report.shift._shift_product_sold', compact('items'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function totalProductRefund($data, $user_id, $start_time, $end_time)
    {
        $totalProductRefund = PosTransaction::select(
            DB::raw('SUM(ts_pos_transaction_details.pos_td_qty) as total_product_refund')
        )
            ->join('pos_transaction_details', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
            ->where('pos_transactions.u_id', '=', $user_id)
            ->where('pos_transactions.st_id', "=", $data['st_id'])
            ->where('pos_transactions.pos_refund', "=", "1")
            ->whereBetween('pos_transactions.created_at', [$start_time, $end_time])
            ->first();

        return $totalProductRefund->total_product_refund;
    }

    private function getDataItems($data, $pos_status, $request)
    {
        return PosTransactionDetail::select(
            DB::raw('CONCAT(ts_products.article_id, " ",ts_products.p_name, " ",ts_products.p_color) as product_name'),
            'sizes.sz_name as size_name',
            'pos_transaction_details.pos_td_qty as qty',
        )
            ->join('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
            ->whereIn('pt_id', function ($query) use ($request) {
                $query->select('pos_transactions.id')
                    ->from('pos_transactions')
                    ->where('pos_transactions.u_id', $request->id)
                    ->whereBetween('pos_transactions.created_at', [$request->start_time_original, $request->end_time_original]);
            })
            ->join('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->join('products', 'products.id', '=', 'product_stocks.p_id')
            ->where('pos_transactions.st_id', $data['st_id'])
            ->where('pos_transactions.pos_refund', $pos_status)
            ->get();
    }
}
