<?php

namespace App\Http\Controllers;

use App\Exports\SettlementDetailTransactionExport;
use App\Exports\SettlementTransactionExport;
use App\Models\PaymentMethod;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Expr\PostDec;

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
            ->editColumn('netsales', function ($collection) {
                return 'Rp ' . number_format($collection->netsales, 0, ',', '.');
            })
            ->rawColumns(['pos_status', 'is_settle'])
            ->make(true);
    }

    public function getDetailSettlement($id)
    {
        DB::beginTransaction();

        try {
            $transaction_items = PosTransactionDetail::where('pt_id', $id)->where('pos_td_item_cogs', 0)->get();
            foreach ($transaction_items as $transaction_item) {
                if ($transaction_item->pos_td_item_cogs == 0) {
                    $item_cogs = ProductStock::query()->where('id', $transaction_item->pst_id)->pluck('ps_purchase_price')->first();

                    $update_data = [
                        'pos_td_item_cogs' => $item_cogs ?? 0,
                    ];

                    $item_update = PosTransactionDetail::where('id', $transaction_item->id)->update($update_data);
                    if (!$item_update) {
                        throw new \Exception('Failed to update item COGS for transaction item ID: ' . $transaction_item->id);
                    }
                }
            }

            $transaction = PosTransaction::query()
                ->select('pos_transactions.created_at as transaction_date', 'pos_invoice as receipt_number', 'pos_order_number as order_number', 'stores.st_name as store_name', 'pos_status as trx_status', 'pos_real_price', 'pos_payment', 'pm_main.pm_name as payment_method_main', 'pm_partial.pm_name as payment_method_partial', 'pos_payment_partial', 'pos_transactions.sub_payment', DB::raw('SUM(pos_td_qty * ps_price_tag) as gross_sales'), 'pos_transactions.pos_total_discount as total_discount',DB::raw('SUM(pos_td_qty * pos_td_item_cogs) as total_cogs'),DB::raw('(Select SUM(discount_seller) from ts_online_transactions join ts_online_transaction_details on ts_online_transactions.id = to_id where ts_online_transactions.order_number=ts_pos_transactions.pos_order_number) AS total_seller_discount'),'pos_transactions.pos_note as note')
                ->leftJoin('stores', 'stores.id', '=', 'st_id')
                ->leftJoin('payment_methods as pm_main', 'pm_main.id', '=', 'pm_id')
                ->leftJoin('payment_methods as pm_partial', 'pm_partial.id', '=', 'pm_id_partial')
                ->leftJoin('pos_transaction_details', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                ->leftJoin('online_transactions', 'online_transactions.order_number', '=', 'pos_invoice')
                ->where('pos_transactions.id', $id)
                ->groupBy('pos_transactions.id','online_transactions.id')
                ->first();
            
            $items = PosTransactionDetail::query()
                ->select('products.article_id','products.p_name', 'ps_barcode', 'pos_td_qty','ps_price_tag',DB::raw('CASE WHEN ts_pos_transaction_details.pos_td_nameset_price>0 THEN \'YES\' ELSE \'NO\' END as is_nameset'), 'pos_td_discount_number as discount', 'pos_td_discount_price as price_after_discount')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->where('pt_id', $id)
                ->get()
                ->toArray();

            $transaction_date = $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d F Y, H:i') : '-';
            $receipt_number = $transaction->receipt_number ?? '-';
            $order_number = $transaction->order_number ?? '-';
            $store_name = $transaction->store_name ?? '-';
            $trx_status = $transaction->trx_status ?? '-';
            $outstanding_balance = 0;
            if ($transaction->trx_status == 'DP') {
                $payment_status = 'Partial Payment';
                $outstanding_balance = $transaction->pos_real_price - $transaction->pos_payment;
            } elseif ($transaction->trx_status == 'REFUND') {
                $payment_status = 'Refunded';
            } elseif ($transaction->trx_status == 'DONE') {
                $payment_status = 'Full Payment';
            } else {
                $payment_status = 'Unknown';
            }

            $payment_method_1 = $transaction->payment_method_main ?? 'UNKNOWN';

            if ($transaction->sub_payment == 1) {
                $sub_payment_method_1 = 'CASH';
            } elseif ($transaction->sub_payment == 2) {
                $sub_payment_method_1 = 'COD';
            } elseif ($transaction->sub_payment == 3) {
                $sub_payment_method_1 = 'ON US';
            } elseif ($transaction->sub_payment == 4) {
                $sub_payment_method_1 = 'OFF US';
            } else {
                $sub_payment_method_1 = '-';
            }

            $payment_amount_1 = $transaction->pos_payment ?? 0;
            $payment_method_2 = $transaction->payment_method_partial ?? '-';
            $sub_payment_method_2 = '-';
            $payment_amount_2 = $transaction->pos_payment_partial ?? 0;

            // Financial Summary
            if ($transaction->trx_status == 'DP') {
                $down_payment = $transaction->pos_payment ?? 0;
            } else {
                $down_payment = 0;
            }
            $gross_sales = $transaction->gross_sales ?? 0;
            $total_discount = $transaction->total_discount ?? 0;

            $net_sales = $transaction->pos_real_price;
            $total_payment = null;
            $cogs = $transaction->total_cogs ?? 0;
            $seller_voucher = $transaction->total_seller_discount ?? 0;
            $total_admin_fee = null;
            $total_dana_cair = null;
            $gross_margin = $gross_sales - $cogs;
            $margin_percentage = $gross_sales != 0 ? round(($gross_margin / $gross_sales) * 100, 2) : 0;

            if ($store_name && str_contains(strtoupper($store_name), 'ONLINE') && substr(trim((string) $receipt_number), 0, 3) !== 'INV') {
                $print_receipt_url = url('/') . '/print_online_nota/' . $receipt_number;
            } else {
                $print_receipt_url = url('/') . '/print_offline_invoice/' . $receipt_number;
            }

            $result = [
                'transaction_date' => $transaction_date,
                'receipt_number' => $receipt_number,
                'order_number' => $order_number,
                'store_name' => $store_name,
                'trx_status' => $trx_status,
                'payment_status' => $payment_status,
                'outstanding_balance' => $outstanding_balance,
                'payment_method_1' => $payment_method_1,
                'sub_payment_method_1' => $sub_payment_method_1,
                'payment_amount_1' => $payment_amount_1,
                'payment_method_2' => $payment_method_2,
                'sub_payment_method_2' => $sub_payment_method_2,
                'payment_amount_2' => $payment_amount_2,
                'down_payment' => $down_payment,
                'gross_sales' => $gross_sales,
                'total_discount' => $total_discount,
                'net_sales' => $net_sales,
                'total_payment' => $total_payment,
                'cogs' => $cogs,
                'seller_voucher' => $seller_voucher,
                'total_admin_fee' => $total_admin_fee,
                'total_dana_cair' => $total_dana_cair,
                'gross_margin' => $gross_margin,
                'margin_percentage' => $margin_percentage . '%',
                'print_receipt_url' => $print_receipt_url,
                'items'=>$items,
                'note' => $transaction->note,
            ];

            DB::commit();
            return response()->json($result);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getNetsalesPerPaymentMethod(Request $request)
    {
        $start_date = $request->input('start_date') . ' 00:00:00';
        $end_date = $request->input('end_date') . ' 23:59:59';
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
            ->where(function ($query) {
                $query->where('payment_methods.pm_name', '!=', 'CASH')
                    ->orWhere(function ($q) {
                        $q->where('payment_methods.pm_name', 'CASH')
                            ->whereNotNull('pos_payment_partial');
                    });
            })
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
            ->where(function ($query) {
                $query->where('payment_methods.pm_name', '!=', 'CASH')
                    ->orWhere(function ($q) {
                        $q->where('payment_methods.pm_name', 'CASH')
                            ->whereNotNull('pos_payment_partial');
                    });
            })
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
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pm_id')
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
            ->when($pm_id != 0 && !in_array($pm_id, ['DEPOSIT SHOPEE', 'DEPOSIT TIKTOK']), function ($query) use ($pm_id) {
                return $query->where('pm_name',$pm_id); // Return no results when pm_id is not DEPOSIT SHOPEE or DEPOSIT TIKTOK
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

    public function bulkUpdateStatus(Request $request)
    {
        $settled_transaction_id = $request->checked_ids;

        foreach ($settled_transaction_id as $key => $value) {
            PosTransaction::query()->where('id', $value)->update(['is_settle' => 1]);
        }
    }

    private function getAllTransactions($start_date, $end_date, $st_id, $pm_id, $status_trx)
    {
        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 23:59:59';
        
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
            ->where(function ($query) {
                $query->where('payment_methods.pm_name', '!=', 'CASH')
                    ->orWhere(function ($q) {
                        $q->where('payment_methods.pm_name', 'CASH')
                            ->whereNotNull('pos_payment_partial');
                    });
            })
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
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pm_id')
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
            ->when($pm_id != 0 && !in_array($pm_id, ['DEPOSIT SHOPEE', 'DEPOSIT TIKTOK']), function ($query) use ($pm_id) {
                return $query->where('pm_name',$pm_id); // Return no results when pm_id is not DEPOSIT SHOPEE or DEPOSIT TIKTOK
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
            ->where(function ($query) {
                $query->where('payment_methods.pm_name', '!=', 'CASH')
                    ->orWhere(function ($q) {
                        $q->where('payment_methods.pm_name', 'CASH')
                            ->whereNotNull('pos_payment_partial');
                    });
            })
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

    public function exportTransaction(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $st_id = $request->input('st_id') ?? 0;
        $pm_id = $request->input('pm_id') ?? 0;
        $status_trx = $request->input('status_trx') ?? '';

        $export = new SettlementTransactionExport(
            $start_date,
            $end_date,
            $st_id,
            $pm_id,
            $status_trx
        );

        // Get current date and time (format: YYYYMMDD_HHmm)

        $fileName = 'settlement_transactions_' . date('Ymd_His') . '.xlsx';
        return Excel::download($export, $fileName);
    }
    public function exportTransactionDetail(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $st_id = $request->input('st_id') ?? 0;
        $pm_id = $request->input('pm_id') ?? 0;
        $status_trx = $request->input('status_trx') ?? '';

        $export = new SettlementDetailTransactionExport(
            $start_date,
            $end_date,
            $st_id,
            $pm_id,
            $status_trx
        );

        // Get current date and time (format: YYYYMMDD_HHmm)

        $fileName = 'settlement_detail_transactions_' . date('Ymd_His') . '.xlsx';
        return Excel::download($export, $fileName);
    }


}
