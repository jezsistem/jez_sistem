<?php

namespace App\Http\Controllers;

use App\Exports\ArticleReportExport;
use App\Exports\OnlineReportExport;
use App\Imports\PurchaseOrderExcelImport;
use App\Imports\StockLocationImport;
use App\Imports\TransactionOnlineImport;
use App\Models\OnlineTransactionChat;
use App\Models\OnlineTransactionDetails;
use App\Models\OnlineTransactions;
use App\Models\PaymentMethod;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\Product;
use App\Models\ProductLocation;
use App\Models\ProductLocationSetup;
use App\Models\ProductLocationSetupTransaction;
use App\Models\ProductStock;
use App\Models\Size;
use App\Models\Store;
use App\Models\StoreTypeDivision;
use App\Models\TempMutasi;
use App\Models\TransaksiOnline;
use App\Models\TransaksiOnlineDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\WarehouseIndex;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TransaksiOnlineController extends Controller
{
    protected function validateAccess()
    {
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => request()->segment(1),
                'status' => TransaksiOnline::select('order_status')
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
        $user = new User();
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => 'Transaksi Online V2',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'st_id' => Store::where('st_delete', '!=', '1')->where('st_name', 'like', '%ONLINE%')->orderByDesc('id')->pluck('st_name', 'id'),
            'std_id' => StoreTypeDivision::where('dv_delete', '!=', '1')->orderByDesc('id')->pluck('dv_name', 'id'),
            'couriers' => OnlineTransactions::select('courier')->distinct()->where('courier', '!=', '')->orderBy('courier')->get(),
            'warehouses' => WarehouseIndex::all(),
        ];
        return view('app.online_transaction.online_transaction_v2', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        //echo
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }
        if (request()->ajax()) {
            return DataTables::of(
                OnlineTransactions::select([
                    'online_transactions.id as to_id',
                    'online_transactions.order_number as to_order_number',
                    'no_resi',
                    'platform_name',
                    'order_date_created',
                    // 'sku',
                    'shipping_fee',
                    'total_payment',
                    'order_status',
                    'online_print',
                    'print_resi',
                    'internal_order_status',
                    DB::raw('COUNT(DISTINCT ts_online_transaction_chat_history.id) as unread_count'),
                    DB::raw('MAX(ts_online_transaction_chat_history.created_at) as last_chat_time'),
                    'courier',
                    DB::raw('CASE WHEN shipping_method LIKE "%Instant%" THEN 1 ELSE 0 END as is_instant'),
                    'shipping_method'
                ])
                    ->leftJoin('online_transaction_details', 'online_transactions.id', '=', 'online_transaction_details.to_id')
                    ->leftJoin('online_transaction_chat_history', function ($join) {
                        $join->on('online_transaction_chat_history.ot_id', '=', 'online_transactions.id')
                            ->where('online_transaction_chat_history.is_readed', '=', 0)
                            ->where('online_transaction_chat_history.is_amp', '=', 0);
                    })
                    // ->where('no_resi', '!=', '')
                    ->where('st_id', '=', $st_id)
                    ->where('order_status', 'not like', '%batal%')
                    ->where('order_status', 'not like', '%cancel%')
                    ->where('order_status', '!=', 'Belum dibayar')
                    ->where('order_status', '!=', 'Belum Bayar')
                    ->when($request->has('warehouse') && !empty($request->get('warehouse')), function ($query) use ($request) {
                        $query->where('online_transaction_details.warehouse', $request->get('warehouse'));
                    })
                    ->when($request->has('courier') && !empty($request->get('courier')), function ($query) use ($request) {
                        $query->where('online_transactions.courier', 'LIKE', '%' . $request->get('courier') . '%');
                    })
                    ->when($request->has('platform') && !empty($request->get('platform')), function ($query) use ($request) {
                        $query->where('online_transactions.platform_name', 'LIKE', '%' . $request->get('platform') . '%');
                    })
                    ->orderByRaw('CASE WHEN is_instant = 1 AND online_print = 0 AND order_status not in ("selesai","Telah dikirim","dikirim","completed") AND order_status not like "%Pesanan diterima%" THEN 0 ELSE 1 END')
                    ->orderByDesc('last_chat_time')
                    ->orderBy('online_transactions.order_date_created', 'DESC')
                    ->groupBy('to_id')
            )
                ->editColumn('order_number', function ($data) {
                    return '<a class="text-white" href="#" data-to_id="' . $data->to_id . '" data-status="' . $data->order_status . '" data-num_order="' . $data->to_order_number . '" id="detail_btn"><span class="btn btn-sm btn-primary" >' . $data->to_order_number . '</span></a><br>';
                })
                ->editColumn('no_resi', function ($data) {
                    $printStatus = '';
                    if ($data->online_print && $data->print_resi) {
                        $printStatus = '<span style="color: red;" class="text-center">DONE PRINT NOTA & RESI</span>';
                    } elseif ($data->online_print) {
                        $printStatus = '<span style="color: red;" class="text-center">DONE PRINT NOTA</span>';
                    } elseif ($data->print_resi) {
                        $printStatus = '<span style="color: red;" class="text-center">DONE PRINT RESI</span>';
                    }
                    return $data->no_resi . '<br>' . $printStatus;
                })
                ->editColumn('total_item', function ($data) {
                    $total_item = OnlineTransactionDetails::where('to_id', $data->to_id)->count();
                    return $total_item ? $total_item : '-';
                })
                ->editColumn('order_status', function ($data) {
                    return '<a class="text-white" href="#" data-pt_id="' . $data->order_status . '" id="detail_btn"><span class="btn btn-sm btn-primary" title="wsad">' . $data->order_status . '</span></a>';
                })
                ->addColumn('action', function ($data) {
                    $unreadCount = $data->unread_count;

                    $badge = $unreadCount > 0 ? '<span class="badge badge-danger position-absolute top-0 start-100 translate-middle">' . $unreadCount . '</span>' : '';

                    return '<div class="d-flex">
                                <button class="btn btn-sm btn-danger ms-1 mr-2" onclick="cancelTransaction(' . $data->to_id . ')" title="Cancel">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-sm btn-warning ms-1 mr-2" onclick="clearPrintStatus(' . $data->to_id . ')" title="Clear Print Status">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <div class="position-relative d-inline-block mr-2">
                                    <button class="btn btn-sm btn-info ms-1" onclick="openChat(' . $data->to_id . ')" data-trx_number="' . $data->to_order_number . '" title="Chat">
                                        <i class="fas fa-comment"></i>
                                    </button>' . $badge . '
                                </div>
                            </div>';
                })
                ->editColumn('internal_order_status', function ($data) {
                    $statusClasses = [
                        'NEW TRX' => 'badge badge-info',
                        'WAITING ONLINE' => 'badge badge-warning',
                        'UNDER REVIEW' => 'badge badge-secondary',
                        'WAITING RECEIPT' => 'badge badge-primary',
                        'WAITING PACKING' => 'badge badge-light',
                        'DONE' => 'badge badge-success',
                    ];

                    $status = $data->internal_order_status; // Assuming this is the field name
                    $class = $statusClasses[$status] ?? 'badge badge-default';

                    return '<span class="' . $class . '">' . $status . '</span>';
                })
                ->rawColumns(['order_number', 'no_resi', 'total_item', 'order_status', 'action', 'internal_order_status'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('no_resi', 'LIKE', "%$search%")
                                ->orWhere('online_transactions.order_number', 'LIKE', "%$search%");
                        });
                    }
                    if (!empty($request->get('tab_status')) && $request->get('tab_status') != 'INSTANT') {
                        $instance->where(function ($w) use ($request) {
                            $tab_status = $request->get('tab_status');
                            if ($tab_status != '') {
                                $w->orWhere('internal_order_status', 'LIKE', "%$tab_status%");
                            }
                        });
                    } else {
                        if ($request->get('tab_status') == 'INSTANT') {
                            $instance->having('is_instant', '=', 1);
                        }
                    }

                    if (!empty($request->get('status'))) {
                        $instance->where(function ($w) use ($request) {
                            $status = $request->get('status');

                            if ($status == 0) {
                                $w->orWhere('online_print', '=', "0");
                            } else if ($status == 1) {
                                $w->orWhere('online_print', '=', "1");
                            }
                        });
                    }

                    if (!empty($request->get('chat_status'))) {
                        $instance->where(function ($w) use ($request) {
                            $chat_status = $request->get('chat_status');

                            if ($chat_status == 'unreaded') {
                                $w->whereExists(function ($query) {
                                    $query->select(DB::raw(1))
                                        ->from('online_transaction_chat_history')
                                        ->whereRaw('ts_online_transaction_chat_history.ot_id = ts_online_transactions.id')
                                        ->where('online_transaction_chat_history.is_readed', 0)
                                        ->where('online_transaction_chat_history.is_amp', 0);
                                });
                            } else if ($chat_status == 'readed') {
                                $w->whereNotExists(function ($query) {
                                    $query->select(DB::raw(1))
                                        ->from('online_transaction_chat_history')
                                        ->whereRaw('ts_online_transaction_chat_history.ot_id = ts_online_transactions.id')
                                        ->where('online_transaction_chat_history.is_readed', 1)
                                        ->where('online_transaction_chat_history.is_amp', 0);
                                });
                            }
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function exportDataOnline(Request $request)
    {
        try {
            $branch = $request->get('branch');
            $status = $request->get('status');
            $date = $request->get('date');
            $changeplatform = $request->get('changeplatform');
            $exp = explode('|', $date);
            $start = null;
            $end = null;
            if (!empty($exp[1])) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $request->get('date');
            }
            // Mendapatkan tanggal dan waktu saat ini
            $now = new \DateTime();
            $timestamp = $now->format('d-m-Y_H.i.s');
            $fileName = 'item_online_details' . $timestamp . '.xlsx';

            return Excel::download(new OnlineReportExport($branch, $start, $end, $status, $changeplatform), $fileName);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function detailDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(OnlineTransactionDetails::select(
                'online_transaction_details.id as otd_id',
                'to_id',
                'products.p_name',
                'ps_barcode',
                'online_transaction_details.sku',
                'brands.br_name',
                'p_color',
                'sz_name',
                'online_transaction_details.qty as to_qty',
                'original_price as shopee_price',
                'products.p_price_tag as jez_price',
                'total_discount',
                'price_after_discount as final_price',
                'discount_seller',
                'platform_name',
                'online_transactions.st_id as trx_store_id',
                'online_transactions.internal_order_status as jezpro_status',
                'warehouse',
                'online_transactions.online_print as is_printed',
                DB::raw('CONCAT_WS(", ", 
                IF(SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "WAITING ONLINE" THEN 1 ELSE 0 END) > 0, 
                    CONCAT("WAITING ONLINE => ", SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "WAITING ONLINE" THEN 1 ELSE 0 END)), 
                    NULL),
                IF(SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "UNDER REVIEW" THEN 1 ELSE 0 END) > 0, 
                    CONCAT("UNDER REVIEW => ", SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "UNDER REVIEW" THEN 1 ELSE 0 END)), 
                    NULL),
                IF(SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "WAITING RECEIPT" THEN 1 ELSE 0 END) > 0, 
                    CONCAT("WAITING RECEIPT => ", SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "WAITING RECEIPT" THEN 1 ELSE 0 END)), 
                    NULL),
                IF(SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "WAITING PACKING" THEN 1 ELSE 0 END) > 0, 
                    CONCAT("WAITING PACKING => ", SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "WAITING PACKING" THEN 1 ELSE 0 END)), 
                    NULL),
                IF(SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "INSTOCK" THEN 1 ELSE 0 END) > 0, 
                    CONCAT("INSTOCK => ", SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "INSTOCK" THEN 1 ELSE 0 END)), 
                    NULL),
                IF(SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "DONE" THEN 1 ELSE 0 END) > 0, 
                    CONCAT("DONE => ", SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = "DONE" THEN 1 ELSE 0 END)), 
                    NULL)
            ) as pick_status')
            )
                ->Join('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
                ->Join('online_transactions', 'online_transactions.id', '=', 'online_transaction_details.to_id')
                ->Join('products', 'products.id', '=', 'product_stocks.p_id')
                ->Join('brands', 'brands.id', '=', 'products.br_id')
                ->Join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.otd_id', '=', 'online_transaction_details.id')
                ->where('online_transactions.id', '=', $request->to_id)
                ->groupBy('online_transaction_details.id'))
                ->editColumn('article', function ($data) {
                    return '<span class="btn btn-primary">[' . $data->br_name . '] ' . $data->p_name . ' ' . $data->p_color . ' [' . $data->sz_name . ']</span>';
                })
                ->editColumn('gap_price', function ($data) {
                    return $data->jez_price - $data->shopee_price;
                })
                ->editColumn('ns_before_admin', function ($data) {
                    return $data->shopee_price - $data->discount_seller;
                })
                ->editColumn('status_pick', function ($data) {
                    $st_id = Auth::user()->st_id;
                    $requiredQty = $data->to_qty;

                    $cek_pick = ProductLocationSetupTransaction::query()->where('otd_id', $data->otd_id)->sum('plst_qty');

                    // Determine status and class based on cek_pick
                    $status_pick = ($cek_pick > $requiredQty) ? 'Done Pick' : 'Not taken';
                    $show_status = ($cek_pick == $requiredQty) ? 'Done Pick' : $cek_pick . ' / ' . $requiredQty;
                    $btnClass = ($show_status == 'Done Pick') ? 'btn-light-success' : 'btn-primary';


                    return '<span class="btn ' . $btnClass . '">' . $show_status . '</span>';
                })
                ->editColumn('pick_status', function ($data) {
                    $statuses = explode(', ', $data->pick_status);
                    $badges = [];

                    $statusClasses = [
                        'WAITING ONLINE' => 'warning',
                        'UNDER REVIEW' => 'secondary',
                        'WAITING RECEIPT' => 'primary',
                        'WAITING PACKING' => 'danger',
                        'INSTOCK' => 'info',
                        'DONE' => 'success'
                    ];

                    foreach ($statuses as $status) {
                        if (empty(trim($status))) continue;

                        $badgeClass = 'secondary'; // default
                        foreach ($statusClasses as $statusKey => $className) {
                            if (strpos($status, $statusKey) !== false) {
                                $badgeClass = $className;
                                break;
                            }
                        }

                        $badges[] = '<span class="mb-2 badge badge-' . $badgeClass . '">' . str_replace(' => ', ' : ', $status) . '</span>';
                    }

                    return implode('<br>', $badges);
                })
                ->addColumn('action', function ($data) {
                    $user_st_id = Auth::user()->st_id;
                    $user_st_code = Store::query()->where('id', $user_st_id)->first()->st_code;

                    $store_id = Store::query()->where('st_code', $user_st_code)->where('st_name', 'like', 'JEZ%')->first()->id;
                    $warehouse_st_id = WarehouseIndex::query()->where('w_code', $data->warehouse)->first()->st_id ?? $store_id;

                    $ps_barcode = $data->ps_barcode;

                    $total_stock = ProductLocationSetup::join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                        ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->join('stores', 'stores.id', '=', 'product_locations.st_id')
                        ->where('stores.id', '=', $warehouse_st_id)
                        ->where('product_stocks.ps_barcode', '=', $data->ps_barcode)
                        ->sum('product_location_setups.pls_qty');

                    $total_waiting = ProductLocationSetupTransaction::leftjoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                        ->leftjoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                        ->leftjoin('product_stocks as ps2', 'ps2.id', '=', 'product_location_setup_transactions.pst_id')
                        ->leftjoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where(function ($query) use ($warehouse_st_id) {
                            $query->where('product_location_setup_transactions.st_id', '=', $warehouse_st_id)
                                ->orWhere('product_location_setup_transactions.warehouse_st_id', '=', $warehouse_st_id);
                        })
                        ->where(function ($query) use ($ps_barcode) {
                            $query->where('product_stocks.ps_barcode', '=', $ps_barcode)
                                ->orWhere('ps2.ps_barcode', '=', $ps_barcode);
                        })
                        ->whereIn('product_location_setup_transactions.plst_status', ['WAITING ONLINE', 'WAITING TO TAKE'])
                        ->whereNull('product_location_setup_transactions.pls_id')
                        ->count();


                    $cek_pick = ProductLocationSetupTransaction::query()->where('otd_id', $data->otd_id)->whereNotIn('plst_status', ['INSTOCK', 'REFUND'])->sum('plst_qty');
                    $can_pick = true;

                    if ($data->warehouse == null && $data->is_printed == 1) {
                        $can_pick = false;
                    }

                    if ($data->internal_order_status == 'WAITING RECEIPT' || $data->internal_order_status == 'WAITING PACKING' || $data->internal_order_status == 'DONE ONLINE' || $data->internal_order_status == 'DONE') {
                        $can_pick = false;
                    }

                    return '<div class="d-flex">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge badge-warning mb-1">Stock: ' . $total_stock - $total_waiting . '</span>
                                    <div>
                                        <button class="btn btn-sm btn-secondary me-1" onclick="pickItems(\'' . $warehouse_st_id . '\',\'' . $data->to_id . '\', \'' . $data->ps_barcode . '\', \'' . $data->otd_id . '\', \'' . $data->to_qty . '\')" ' . ($cek_pick >= $data->to_qty || $can_pick == false ? 'disabled' : '') . '>
                                            <i class="fas fa-hand-paper"></i> Pick
                                        </button>
                                        
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-warning ml-4" id="edit_item_btn" data-otd_id= \'' . $data->otd_id . '\' data-qty= \'' . $data->to_qty . '\' data-to_id= \'' . $data->to_id . '\' title="Edit Qty">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <!--<button class="btn btn-sm btn-info ml-4" id="edit_item_warehouse_btn" data-otd_id= \'' . $data->otd_id . '\'' . '\' data-to_id= \'' . $data->to_id . '\' data-warehouse= \'' . $data->warehouse . '\' title="Edit Warehouse">
                                    <i class="fas fa-warehouse"></i>
                                </button>-->
                                <button class="btn btn-sm btn-danger ml-4" onclick="deleteItem(\'' . $data->otd_id . '\')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>';
                })
                ->rawColumns(['article', 'status_pick', 'action', 'pick_status'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function deleteItem(Request $request)
    {
        $otd_id = $request->otd_id;

        try {
            DB::beginTransaction();

            $plst_list = ProductLocationSetupTransaction::query()->where('otd_id', $otd_id);

            // get data item that already picked by helper
            $already_picked = ProductLocationSetupTransaction::where('otd_id', $otd_id)
                ->where('plst_status', 'WAITING ONLINE')
                ->whereNotNull('pls_id')
                ->exists();

            if ($already_picked) {
                DB::rollBack();
                return response()->json([
                    'status' => '400',
                    'message' => 'Item sudah dipick oleh helper, tidak dapat dihapus.'
                ]);
            }

            // cancel taking item
            $plst_list->update([
                'plst_type' => 'IN',
                'plst_status' => 'INSTOCK',
                'cancel_pickup_time' => now(),
                'updated_at' => now(),
            ]);

            // delete online transaction detail
            $update = OnlineTransactionDetails::query()->where('id', $otd_id)->update([
                'deleted_at' => now(),
                'deleted_by' => Auth::user()->id,
            ]);

            if ($update) {
                DB::commit();
                return response()->json([
                    'status' => '200',
                    'message' => 'Item berhasil dihapus.'
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => '400',
                    'message' => 'Gagal menghapus item.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in deleteItem: ' . $e->getMessage());
            return response()->json([
                'status' => '400',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function editItem(Request $request)
    {
        $otd_id = $request->edit_item_otd_id;
        $qty = $request->qty;
        $to_id = $request->edit_item_to_id;

        try {
            DB::beginTransaction();

            // // get data item that already picked by helper
            $transaction = OnlineTransactions::where('id', $to_id)->get()->first();

            if ($transaction->internal_order_status == 'WAITING RECEIPT' || $transaction->internal_order_status == 'WAITING PACKING' || $transaction->internal_order_status == 'DONE ONLINE' || $transaction->internal_order_status == 'DONE') {
                DB::rollBack();
                return response()->json([
                    'status' => '400',
                    'message' => 'Transaksi sudah dalam status ' . $transaction->internal_order_status . ', tidak dapat diubah.'
                ]);
            }

            $count_picked = ProductLocationSetupTransaction::query()->whereNotIn('plst_status', ['DONE', 'INSTOCK', 'REFUND'])->where('otd_id', $otd_id)->count();

            if ($qty < $count_picked) {
                DB::rollBack();
                return response()->json([
                    'status' => '400',
                    'message' => 'Jumlah qty tidak boleh kurang dari jumlah item yang sudah dipick. Minta Helper untuk cancel pick'
                ]);
            }

            $update = OnlineTransactionDetails::query()->where('id', $otd_id)->update([
                'qty' => $qty,
                'updated_at' => now(),
                'updated_by' => Auth::user()->id,
            ]);

            $update_status = $transaction->update([
                'internal_order_status' => 'WAITING ONLINE',
                'updated_at' => now(),
            ]);

            if ($update && $update_status) {
                DB::commit();
                return response()->json([
                    'status' => '200',
                    'message' => 'Item berhasil diupdate.'
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => '400',
                    'message' => 'Gagal mengupdate item.'
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in editItem: ' . $e->getMessage());
            return response()->json([
                'status' => '400',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function cetak_invoice2(Request $request)
    {
        try {
            DB::beginTransaction();

            $invoice = $request->orderNumber;
            // Check if the latest pos_status for this invoice is 'DONE'
            $check = PosTransaction::where(['pos_invoice' => $invoice])
                ->orderByDesc('id')
                ->value('pos_status') === 'DONE' ? true : false;

            $get_invoice = array();
            $dropshipper = null;
            $st_id = Auth::user()->st_id;

            if ($check) {
                $trx = PosTransaction::select(
                    'pos_transactions.id as pt_id',
                    'cust_id',
                    'pos_cc_charge',
                    'cust_province',
                    'cust_city',
                    'cust_subdistrict',
                    'sub_cust_id',
                    'u_name',
                    'pm_name',
                    'pm_id_partial',
                    'dv_name',
                    'cr_name',
                    'pos_another_cost',
                    'pos_payment',
                    'pos_payment_partial',
                    'pos_ref_number',
                    'pos_card_number',
                    'cust_name',
                    'cust_phone',
                    'cust_address',
                    'pos_invoice',
                    'st_name',
                    'st_phone',
                    'st_address',
                    'pos_shipping',
                    'cr_id',
                    'pos_transactions.created_at as pos_created',
                    'pos_transactions.pos_total_vouchers',
                    'pos_total_discount',
                    'cust_name'
                )
                    ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
                    ->leftJoin('couriers', 'couriers.id', '=', 'pos_transactions.cr_id')
                    ->leftJoin('payment_methods', 'payment_methods.id', '=', 'pos_transactions.pm_id')
                    ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
                    ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
                    ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
                    ->where(['pos_invoice' => $invoice])
                    ->first();

                $check_transaction_detail = PosTransactionDetail::leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
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

            $response = [];

            $check_status_print = OnlineTransactions::where('order_number', $invoice)->whereNotNull('time_print');

            if ($check_status_print->count() > 0) {
                // If already printed, return a 200 OK status
                $response['status'] = 200;
                DB::commit();
                return $response;
            }

            $online_transactions = [];
            $cur_trx = OnlineTransactions::where('order_number', $invoice)->get()->first();
            $sku_current_print = OnlineTransactionDetails::where('to_id', $cur_trx->id);

            if (!$cur_trx) {
                throw new \Exception('Online transaction not found');
            }

            if ($cur_trx->platform_name == 'Shopee') {
                $platform = StoreTypeDivision::where('dv_name', 'SHOPEE')->get()->first()->id;
            } else {
                $platform = StoreTypeDivision::where('dv_name', 'TIKTOK')->get()->first()->id;
            }

            $trx_id_new = null;


            foreach ($sku_current_print->get() as $ind => $data) {
                $chk_pos_offline = PosTransaction::where('pos_invoice', $invoice)->count();

                // Get product stock ID based on barcode
                if ($ind <= $sku_current_print->count()) {
                    $ps_barcode_record = ProductStock::where('ps_barcode', $data->sku)->first();

                    if ($ps_barcode_record) {
                        $ps_barcode_id = $ps_barcode_record->id;

                        // Check for any waiting online transactions
                        $cek_keep_online = ProductLocationSetupTransaction::join('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                            ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                            ->where('product_stocks.ps_barcode', '=', $data->sku)
                            ->where('st_id', '=', $st_id)
                            ->whereNull('pt_id')
                            ->where('plst_status', '=', 'WAITING RECEIPT')
                            ->count();

                        // Fetch the first record of WAITING RECEIPT transactions
                        $data_keep_online = ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id')
                            ->join('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                            ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                            ->where('product_stocks.ps_barcode', '=', $data->sku)
                            ->where('st_id', '=', $st_id)
                            ->whereNull('pt_id')
                            ->where('plst_status', '=', 'WAITING RECEIPT')
                            ->first();

                        // If there are any waiting transactions, store them for further processing
                        if ($cek_keep_online > 0 && $data_keep_online) {
                            $online_transactions[] = [
                                'ps_barcode' => $data->ps_barcode,
                                'qty' => $data->qty,
                                'id' => $data_keep_online->plst_id,
                                'online_id' => $data->to_id,
                            ];
                        }
                    }

                    $sku_count = OnlineTransactionDetails::where('to_id', $cur_trx->id)->where('sku', $data->sku)->count();
                    if (count($online_transactions) >= $sku_count) {
                        $pos_transaction_check = PosTransaction::where(['pos_invoice' => $invoice])
                            ->orderByDesc('id')
                            ->first();

                        $is_trx_done = $pos_transaction_check && $pos_transaction_check->pos_status === 'DONE';

                        if ($is_trx_done) {
                            // If a POS transaction already exists, use its ID
                            $trx_id_new = $pos_transaction_check->id;
                        } else {
                            // If no POS transaction exists, create a new one
                            $trx_id_new = DB::table('pos_transactions')->insertGetId([
                                'u_id' => Auth::user()->id,
                                'kasir_id' => Auth::user()->id,
                                'st_id' => Auth::user()->st_id,
                                'stt_id' => Auth::user()->stt_id,
                                'pos_online_payment' => $cur_trx->payment_method,
                                'std_id' => $platform,
                                'cust_id' => 1,
                                'pos_admin_cost' => 0,
                                'pos_another_cost' => 0,
                                'pos_real_price' => $cur_trx->total_payment,
                                'pos_order_number' => $cur_trx->order_number,
                                'pos_invoice' => $cur_trx->order_number,
                                'pos_unique_code' => 0,
                                'pos_shipping' => $cur_trx->shipping_fee,
                                'pos_total_discount' => 0,
                                'pos_discount_seller' => 0,
                                'created_at' => date('Y-m-d H:i:s'),
                                'pos_status' => 'DONE',
                                'pos_payment' => $cur_trx->total_payment
                            ]);
                        }

                        if (!$trx_id_new) {
                            throw new \Exception('Failed to create POS transaction');
                        }

                        $params = [
                            'online_print' => true,
                            'u_print' => Auth::user()->id,
                            'time_print' => now(),
                            'updated_at' => now(),
                        ];
                        OnlineTransactions::where('order_number', $invoice)->update($params);

                        // Update DONE status
                        $keep_online_details = ProductLocationSetupTransaction::join('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                            ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                            ->where('product_stocks.ps_barcode', '=', $data->sku)
                            ->where('st_id', '=', $st_id)
                            ->where('plst_status', '=', 'WAITING ONLINE')
                            ->select([
                                'product_location_setup_transactions.id as plst_id',
                                'product_location_setup_transactions.pls_id',
                                'product_location_setup_transactions.u_id',
                                'product_location_setup_transactions.u_id_helper',
                                'product_location_setup_transactions.u_id_packer',
                                'product_location_setup_transactions.pt_id',
                                'product_location_setup_transactions.st_id',
                                'product_location_setup_transactions.u_id_refund',
                                'product_location_setup_transactions.plst_qty',
                                'product_location_setup_transactions.plst_type',
                                'product_location_setup_transactions.plst_status',
                                'product_location_setup_transactions.created_at',
                                'product_location_setup_transactions.updated_at',
                                'product_location_setup_transactions.rt_id',
                                'product_location_setup_transactions.is_approval',
                                'product_location_setups.pl_id',
                                'product_stocks.id as pst_id',
                                'product_location_setups.id as pl_id',
                                'product_location_setups.pls_qty',
                                'product_location_setups.created_by',
                                'product_location_setups.updated_by',
                                'product_stocks.p_id',
                                'product_stocks.sz_id',
                                'product_stocks.ps_qty',
                                'product_stocks.ps_barcode',
                                'product_stocks.ps_running_code',
                                'product_stocks.ps_price_tag',
                                'product_stocks.ps_sell_price',
                                'product_stocks.ps_purchase_price',
                                'product_stocks.ps_delete',
                            ])
                            ->limit($data->qty)
                            ->get();

                        $paramsPlst = [
                            'plst_status' => 'DONE',
                            'updated_at' => now(),
                            'u_id_packer' => Auth::user()->id,
                            'pt_id' => $trx_id_new,
                        ];

                        foreach ($keep_online_details as $key => $cko) {
                            $product_stock = ProductStock::where('ps_barcode', $data->sku)->first();

                            if (!$product_stock->id) {
                                throw new \Exception('Product stock not found for barcode: ' . $data->sku);
                            }

                            $item_detail_checks = PosTransactionDetail::where('pst_id', $product_stock->id)->where('pt_id', $trx_id_new)->exists();

                            $price_before_discount = $data->original_price * $data->qty;
                            $price_after_discount = $data->price_after_discount * $data->qty;

                            if (!$item_detail_checks) {
                                $insert_details = PosTransactionDetail::create([
                                    'pt_id' => $trx_id_new,
                                    'pst_id' => $product_stock->id,
                                    'pl_id' => $data->pl_id,
                                    'pos_td_qty' => $data->qty,
                                    'pos_td_sell_price' => $price_after_discount,
                                    'pos_td_discount_number' => $price_before_discount - $price_after_discount,
                                    'pos_td_discount' => NULL,
                                    'pos_td_discount_price' => $data->price_after_discount * $data->qty,
                                    'pos_td_marketplace_price' => 0,
                                    'pos_td_nameset_price' => 0,
                                    'pos_td_nameset' => 0,
                                    'pos_td_description' => '',
                                    'pos_td_price_item_discount' => 0,
                                    'pos_td_total_price' => $price_before_discount,
                                    'pos_td_item_cogs' => $product_stock->ps_purchase_price,
                                    'pos_td_item_price_tag' => $product_stock->ps_price_tag,
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);

                                if (!$insert_details) {
                                    throw new \Exception('Failed to create POS transaction detail');
                                }
                            }

                            // $updateResult = ProductLocationSetupTransaction::where('id', $cko->plst_id)->update($paramsPlst);
                            if (!$updateResult) {
                                throw new \Exception('Failed to update product location setup transaction');
                            }
                        }

                        // Return a 200 OK status
                    } else {
                        // If not all transactions match, return a 400 Bad Request status
                        $response['status'] = 400;
                        $response['message'] = 'Not all transactions match';
                        DB::rollback();
                        return $response;
                    }
                }
            }

            if ($trx_id_new) {
                $response['status'] = 200;
                DB::commit();
            } else {
                $response['status'] = 400;
                $response['message'] = 'Failed to create transaction';
                DB::rollback();
            }

            return $response;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in cetak_invoice: ' . $e->getMessage());
            return [
                'status' => 500,
                'message' => 'An error occurred while processing the invoice: ' . $e->getMessage()
            ];
        }
    }

    public function cetak_invoice(Request $request)
    {

        $order_number = $request->orderNumber;
        $to_id = $request->to_id;

        $check = PosTransaction::where(['pos_invoice' => $order_number])
            ->orderByDesc('id')
            ->value('pos_status') === 'DONE' ? true : false;

        if ($check) {
            return response()->json([
                'status' => '200',
                'message' => 'Invoice sudah pernah dicetak.'
            ]);
        }

        try {
            DB::beginTransaction();


            $is_trx_online_exists = OnlineTransactions::where('id', $to_id)->exists();

            if (!$is_trx_online_exists) {
                DB::rollBack();
                return response()->json([
                    'status' => '404',
                    'message' => 'Transaksi online tidak ditemukan.'
                ]);
            }

            // ambil semua item transaksi online yang masih aktif (belum dihapus)
            $active_transaction_items = OnlineTransactionDetails::where('to_id', $to_id)
                ->whereNull('deleted_at')
                ->get();

            if ($active_transaction_items->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'status' => '400',
                    'message' => 'Tidak ada item aktif pada transaksi online ini.'
                ]);
            }

            // ambil barang yang sudah dipick oleh helper dan berada di status 'WAITING RECEIPT'
            $waiting_receipt_items = ProductLocationSetupTransaction::join('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->whereIn('product_location_setup_transactions.otd_id', $active_transaction_items->pluck('id')->toArray())
                ->where('product_location_setup_transactions.plst_status', 'WAITING RECEIPT')
                ->select('product_stocks.ps_barcode', DB::raw('SUM(ts_product_location_setup_transactions.plst_qty) as total_picked'))
                ->groupBy('product_stocks.ps_barcode')
                ->get()
                ->keyBy('ps_barcode');

            if ($waiting_receipt_items->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'status' => '400',
                    'message' => 'Belum ada item yang dipick oleh helper.'
                ]);
            }

            // bandingkan qty yang diorder dengan qty yang sudah dipick
            foreach ($active_transaction_items as $item) {
                $picked_item = $waiting_receipt_items->get($item->sku);
                $picked_qty = $picked_item ? $picked_item->total_picked : 0;

                if ($picked_qty < $item->qty) {
                    DB::rollBack();
                    return response()->json([
                        'status' => '400',
                        'message' => 'Item dengan SKU ' . $item->sku . ' belum lengkap dipick. Qty diorder: ' . $item->qty . ', Qty dipick: ' . $picked_qty
                    ]);
                }

                if ($picked_qty > $item->qty) {
                    DB::rollBack();
                    return response()->json([
                        'status' => '400',
                        'message' => 'Item dengan SKU ' . $item->sku . ' melebihi qty yang diorder. Qty diorder: ' . $item->qty . ', Qty dipick: ' . $picked_qty
                    ]);
                }
            }

            $trx_data = OnlineTransactions::where('id', $to_id)->get()->first();

            if (!$trx_data) {
                DB::rollBack();
                return response()->json([
                    'status' => '404',
                    'message' => 'Data transaksi online tidak ditemukan.'
                ]);
            }

            $std_id = StoreTypeDivision::where('dv_name', strtoupper($trx_data->platform_name))->value('id');

            if (!$std_id) {
                DB::rollBack();
                return response()->json([
                    'status' => '400',
                    'message' => 'Platform toko tidak dikenali.'
                ]);
            }

            //buat pos_transaction jika semua item sudah lengkap dipick
            $pos_transaction_id = DB::table('pos_transactions')->insertGetId([
                'u_id' => Auth::user()->id,
                'kasir_id' => Auth::user()->id,
                'stt_id' => Auth::user()->stt_id,
                'std_id' => $std_id,
                'st_id' => $trx_data->st_id,
                'pos_online_payment' => '',
                'cust_id' => 1,
                'pos_admin_cost' => 0,
                'pos_another_cost' => 0,
                'pos_real_price' => $trx_data->total_payment,
                'pos_order_number' => $trx_data->order_number,
                'pos_invoice' => $trx_data->order_number,
                'pos_unique_code' => 0,
                'pos_shipping' => $trx_data->shipping_fee,
                'pos_total_discount' => 0,
                'pos_discount_seller' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'pos_status' => 'DONE',
                'pos_payment' => $trx_data->total_payment
            ]);

            if (!$pos_transaction_id) {
                DB::rollBack();
                return response()->json([
                    'status' => '500',
                    'message' => 'Gagal membuat transaksi POS.'
                ]);
            }

            // Insert pos_transaction_details
            foreach ($active_transaction_items as $item) {
                $price_before_discount = $item->original_price * $item->qty;
                $price_after_discount = $item->price_after_discount * $item->qty;

                $product_stock = ProductStock::where('ps_barcode', $item->sku)->get()->first();

                $insert_details = PosTransactionDetail::create([
                    'pt_id' =>  $pos_transaction_id,
                    'pst_id' => $product_stock->id,
                    'pl_id' => $item->pl_id,
                    'pos_td_qty' => $item->qty,
                    'pos_td_sell_price' => $price_after_discount,
                    'pos_td_discount_number' => $price_before_discount - $price_after_discount,
                    'pos_td_discount' => NULL,
                    'pos_td_discount_price' => $item->price_after_discount * $item->qty,
                    'pos_td_marketplace_price' => 0,
                    'pos_td_nameset_price' => 0,
                    'pos_td_nameset' => 0,
                    'pos_td_description' => '',
                    'pos_td_price_item_discount' => 0,
                    'pos_td_total_price' => $price_before_discount,
                    'pos_td_item_cogs' => $product_stock->ps_purchase_price,
                    'pos_td_item_price_tag' => $product_stock->ps_price_tag,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                if (!$insert_details) {
                    DB::rollBack();
                    return response()->json([
                        'status' => '500',
                        'message' => 'Gagal menambahkan detail transaksi untuk SKU: ' . $item->sku
                    ]);
                }
            }

            // tambah pt_id di product_location_setup_transactions
            $plst_list = ProductLocationSetupTransaction::whereIn('product_location_setup_transactions.otd_id', $active_transaction_items->pluck('id')->toArray())
                ->where('product_location_setup_transactions.plst_status', 'WAITING RECEIPT')
                ->get();


            foreach ($plst_list as $plst_item) {
                $update_plst = DB::table('product_location_setup_transactions')
                    ->where('id', $plst_item->id)
                    ->update([
                        'updated_at' => now(),
                        'u_id_packer' => Auth::user()->id,
                        'pt_id' => $pos_transaction_id,
                    ]);

                if (!$update_plst) {
                    DB::rollBack();
                    return response()->json([
                        'status' => '500',
                        'message' => 'Gagal memperbarui status pick untuk item dengan PLST ID: ' . $plst_item->id
                    ]);
                }
            }

            // jika semua item sudah lengkap dipick, lanjutkan proses cetak invoice
            $params = [
                'online_print' => true,
                'u_print' => Auth::user()->id,
                'time_print' => now(),
                'updated_at' => now(),
            ];
            $update_print_status = OnlineTransactions::where('order_number', $order_number)->update($params);
            if ($update_print_status === false) {
                DB::rollBack();
                return response()->json([
                    'status' => '500',
                    'message' => 'Gagal memperbarui status cetak invoice.'
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => '200',
                'message' => 'Invoice berhasil dicetak.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in cetak_invoice: ' . $e->getMessage());
            return response()->json([
                'status' => '500',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function cetak_nota($orderNumber)
    {
        $get_invoice = array();
        $check = OnlineTransactions::where(['order_number' => $orderNumber])->exists();

        if ($check) {
            $trx = OnlineTransactions::where(['order_number' => $orderNumber])->first();

            $check_transaction_detail = OnlineTransactionDetails::leftJoin('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                //                ->leftjoin('online_transactions', 'online_transactions.id', '=', 'online_transaction_details.to_id')
                //                ->leftjoin('stores', 'stores.id', '=', 'online_transactions.st_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(['to_id' => $trx->id])
                ->where('online_transaction_details.deleted_at', null)->get();


            if (!empty($check_transaction_detail)) {
                $trx->subitem = $check_transaction_detail;
                array_push($get_invoice, $trx);
            }
        }

        $stores = $trx->st_id;

        $st_name = Store::where('id', $stores)->first()->st_name;

        $data_stores = Store::where('id', $stores)->get()->first();

        $stores_code = $data_stores->st_code;

        $cashier = User::query()->select('u_name')->where('id', $trx->u_print)->value('u_name');


        $data = [
            'title' => 'Invoice ' . $orderNumber,
            'invoice' => $orderNumber,
            'st_name' => $st_name,
            'invoice_data' => $get_invoice,
            'store_code' => $stores_code,
            'segment' => request()->segment(1),
            'cashier' => $cashier
        ];
        return view('app.invoice.print_invoice_online', compact('data'));
    }

    public function importData(Request $request)
    {
        $user_store_id = Auth::user()->st_id;
        $online_store_ids = Store::where('st_name', 'like', '%ONLINE%')->pluck('id')->toArray();

        if (!in_array($user_store_id, $online_store_ids)) {
            return response()->json([
                'status' => '403',
                'message' => 'Anda tidak memiliki izin untuk import data.'
            ]);
        }

        try {
            if ($request->hasFile('importFile')) {
                $file = $request->file('importFile');

                $nama_file = rand() . $file->getClientOriginalName();
                $original_name = $file->getClientOriginalName();

                $file->move('online', $nama_file);

                // Check if the user is authenticated
                if (Auth::check()) {
                    $st_id_form = $request->input('st_id_form');
                } else {
                    throw new \Exception('User not authenticated');
                }

                Log::info('st_id_form: ' . $st_id_form);

                $import = new TransactionOnlineImport();
                $data = Excel::toArray($import, public_path('online/' . $nama_file));

                // Validate header structure
                $expectedHeaders = [
                    "Order ID",
                    "Order Status",
                    "Cancel Reason",
                    "Tracking ID",
                    "Delivery Option",
                    "Created Time",
                    "Paid Time",
                    "Payment Method",
                    "SKU Unit Original Price",
                    "SKU Subtotal After Discount",
                    "Quantity",
                    "Seller SKU",
                    "Sku Quantity of return",
                    "SKU Seller Discount",
                    "SKU Platform Discount",
                    "Shipping Fee After Discount",
                    "Order Amount",
                    "Regency and City",
                    "Province",
                    "Warehouse",
                    'Ekspedisi'
                ];

                if (!isset($data[0][0]) || $data[0][0] !== $expectedHeaders) {
                    $r['status'] = "422";
                    $r['message'] = 'Invalid file format.';
                    return json_encode($r);
                }

                if (count($data) >= 0) {
                    $processData = $this->processImportData($data[0], $original_name, $st_id_form);
                    $r['data'] = $file->getClientOriginalName();
                    $r['status'] = '200';
                } else {
                    $r['status'] = '419';
                }
            } else {
                $r['status'] = '400';
            }
            return json_encode($r);
        } catch (\Exception $e) {
            if (isset($nama_file)) {
                unlink(public_path('online/' . $nama_file));
            }
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
            return json_encode($r);
        }
    }

    public function sendChatHistoryOnlineTransaction(Request $request)
    {
        $ot_id = $request->ot_id;
        $message = $request->message;
        $user = Auth::user();
        $is_amp = $request->is_amp;

        try {
            $ot = OnlineTransactions::where('id', $ot_id)->first();

            if (!$ot) {
                return response()->json(['status' => '404', 'message' => 'Transaction not found']);
            }

            $send = OnlineTransactionChat::create([
                'ot_id' => $ot_id,
                'user_id' => $user->id,
                'messages' => $message,
                'is_amp' => $is_amp ? 1 : 0,
                'created_at' => now(),
            ]);

            return response()->json(['status' => '200', 'message' => 'Message sent successfully']);
        } catch (\Exception $e) {
            \Log::error('Error sending chat message: ' . $e->getMessage());
            return response()->json(['status' => '500', 'message' => 'An error occurred while sending the message']);
        }
    }

    public function getChatHistoryOnlineTransaction($id, Request $request)
    {
        $is_amp = $request->is_amp;
        try {
            $chatHistory = OnlineTransactionChat::where('ot_id', $id)
                ->leftJoin('users', 'users.id', '=', 'online_transaction_chat_history.user_id')
                ->select('online_transaction_chat_history.*', 'users.u_name')
                ->orderBy('online_transaction_chat_history.created_at', 'ASC')
                ->get();

            if ($is_amp == 1) {
                // Mark messages as read if is_amp is true
                OnlineTransactionChat::where('ot_id', $id)
                    ->where('is_readed', 0)
                    ->where('is_amp', 0)
                    ->update(['is_readed' => 1]);
            } else {
                // Mark AMP messages as read if is_amp is false
                OnlineTransactionChat::where('ot_id', $id)
                    ->where('is_amp', 1)
                    ->where('is_readed', 0)
                    ->update(['is_readed' => 1]);
            }

            return response()->json(['status' => '200', 'data' => $chatHistory]);
        } catch (\Exception $e) {
            \Log::error('Error fetching chat history: ' . $e->getMessage());
            return response()->json(['status' => '500', 'message' => 'An error occurred while fetching chat history']);
        }
    }

    public function pickItems(Request $request)
    {
        $warehouse_st_id = $request->warehouse_st_id;
        $to_id = $request->to_id;
        $ps_barcode = $request->sku;
        $otd_id = $request->to_detail_id;
        $qty = $request->qty;
        $st_id = Auth::user()->st_id;
        $user_id = Auth::user()->id;

        $st_code = Store::where('id', $st_id)->first()->st_code;

        try {
            DB::beginTransaction();

            $to = OnlineTransactions::where('id', $to_id)->first();
            if (!$to) {
                DB::rollback();
                return response()->json(['status' => '404', 'message' => 'Online transaction not found']);
            }

            $otd = OnlineTransactionDetails::where('id', $otd_id)->first();
            if (!$otd) {
                DB::rollback();
                return response()->json(['status' => '404', 'message' => 'Online transaction detail not found']);
            }

            $ps = ProductStock::where('ps_barcode', $ps_barcode)->first();
            if (!$ps) {
                DB::rollback();
                return response()->json(['status' => '404', 'message' => 'Product stock not found']);
            }


            $availableQty = ProductLocationSetup::join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->join('stores', 'stores.id', '=', 'product_locations.st_id')
                ->where('stores.id', '=', $warehouse_st_id)
                ->where('product_stocks.ps_barcode', '=', $ps_barcode)
                ->sum('product_location_setups.pls_qty');

            $waitingQty = ProductLocationSetupTransaction::leftjoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->leftjoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftjoin('product_stocks as ps2', 'ps2.id', '=', 'product_location_setup_transactions.pst_id')
                ->leftjoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where(function ($query) use ($warehouse_st_id) {
                    $query->where('product_location_setup_transactions.st_id', '=', $warehouse_st_id)
                        ->orWhere('product_location_setup_transactions.warehouse_st_id', '=', $warehouse_st_id);
                })
                ->where(function ($query) use ($ps_barcode) {
                    $query->where('product_stocks.ps_barcode', '=', $ps_barcode)
                        ->orWhere('ps2.ps_barcode', '=', $ps_barcode);
                })
                ->whereIn('product_location_setup_transactions.plst_status', ['WAITING ONLINE', 'WAITING TO TAKE'])
                ->whereNull('product_location_setup_transactions.pls_id')
                ->count();

            $readyQty = $availableQty - $waitingQty;

            if ($readyQty < $qty) {
                DB::rollback();
                return response()->json(['status' => '400', 'message' => 'Jumlah stok tidak mencukupi']);
            }

            //count picked item with the same otd_id
            $count_picked = ProductLocationSetupTransaction::query()->whereNotIn('plst_status', ['DONE', 'INSTOCK', 'REFUND'])->where('otd_id', $otd_id)->count();

            for ($i = $count_picked; $i < $qty; $i++) {
                $create_plst = DB::table('product_location_setup_transactions')->insert([
                    'pst_id' => $ps->id,
                    'u_id' => $user_id,
                    'otd_id' => $otd_id,
                    'st_id' => $st_id,
                    'warehouse_st_id' => $warehouse_st_id,
                    'plst_qty' => 1,
                    'plst_type' => 'OUT',
                    'plst_status' => 'WAITING ONLINE',
                    'created_at' => now(),
                ]);

                if (!$create_plst) {
                    DB::rollback();
                    return response()->json(['status' => '500', 'message' => 'Failed to create pick item record']);
                }
            }

            $items = OnlineTransactionDetails::query()->where('to_id', $to_id)->get();
            $item_ids = $items->pluck('id')->toArray();
            $total_qty = $items->sum('qty');

            $all_picked = ProductLocationSetupTransaction::whereIn('otd_id', $item_ids)
                ->where('plst_status', 'WAITING ONLINE')
                ->count();

            if ($total_qty == $all_picked) {
                OnlineTransactions::where('id', $to->id)
                    ->update(['internal_order_status' => 'WAITING ONLINE']);
            }

            DB::commit();
            return response()->json(['status' => '200', 'message' => 'Items picked successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in pickItems: ' . $e->getMessage());
            return response()->json(['status' => '500', 'message' => 'An error occurred while processing the request']);
        }
    }

    public function editItemWarehouse(Request $request)
    {
        $otd_id = $request->edit_item_warehouse_otd_id;
        $to_id = $request->edit_item_warehouse_to_id;
        $new_warehouse = $request->warehouse;

        $transaction = OnlineTransactions::where('id', $to_id)->get()->first();

        if (!$transaction) {
            return response()->json(['status' => '404', 'message' => 'Online transaction not found']);
        }

        $is_picked = ProductLocationSetupTransaction::query()
            ->join('online_transaction_details', 'online_transaction_details.id', '=', 'product_location_setup_transactions.otd_id')
            ->whereNotIn('plst_status', ['DONE', 'INSTOCK', 'REFUND'])->where('to_id', $to_id)->exists();

        if ($is_picked) {
            return response()->json(['status' => '400', 'message' => 'Item sudah dipick, tidak dapat diubah']);
        }

        $update_warehouse = OnlineTransactionDetails::where('to_id', $to_id)->where('deleted_at', null)->update([
            'warehouse' => $new_warehouse,
        ]);

        if ($update_warehouse) {
            return response()->json(['status' => '200', 'message' => 'Warehouse updated successfully']);
        } else {
            return response()->json(['status' => '500', 'message' => 'Failed to update warehouse']);
        }
    }

    private function processImportData($data, $original_name, $st_id_form)
    {
        $processedData = [];
        $type = strpos($original_name, 'Shopee') !== false ? 'Shopee' : 'TikTok';
        $platform = $type;

        $st_id = $st_id_form;

        $store_id = Auth::user()->st_id;
        $st_code = Store::where('id', $store_id)->first()->st_code;

        if ($type === 'Shopee') {
            foreach ($data as $item) {
                $order_number = $item[0];
                $order_status = $item[1];
                $reason_cancellation = $item[2];
                $no_resi = $item[3];
                $shipping_method = $item[4];
                $order_date_created = $item[5];
                $payment_date = $item[6];
                $payment_method = $item[7];

                $shipping_fee = str_replace(['IDR ', '.'], '', $item[15]);
                $total_payment = str_replace(['IDR ', '.'], '', $item[16]);
                $city = $item[17];
                $province = $item[18];

                //get courier from ekspedisi column
                $courier = OnlineTransactions::getCourierAttribute($item[20]);

                $rowData = [
                    'st_id' => 20,
                    'order_number' => $order_number,
                    'order_status' => $order_status,
                    'reason_cancellation' => $reason_cancellation,
                    'no_resi' => $no_resi,
                    'platform_name' => 'Shopee',
                    'shipping_method' => $shipping_method,
                    'shipping_fee' => $shipping_fee,
                    'order_date_created' => $order_date_created,
                    'payment_date' => $payment_date,
                    'payment_method' => $payment_method,
                    'total_payment' => $total_payment,
                    'city' => $city,
                    'province' => $province,
                    'internal_order_status' => 'NEW TRX',
                    'courier' => $courier,
                ];

                try {
                    $get_order_number = OnlineTransactions::where('order_number', $order_number)->count();

                    if ($get_order_number == 0) {
                        $processedData[] = $rowData;
                        if ($rowData['order_status'] != 'Cancel' && $rowData['order_status'] != 'Batal') {
                            $transaction = OnlineTransactions::create($rowData);

                            OnlineTransactions::where('id', $transaction->id)->update(['st_id' => Auth::user()->st_id]);
                        }
                    } else {
                        $rowUpdate = [
                            'order_number' => $order_number,
                            'order_status' => $order_status,
                            'reason_cancellation' => $reason_cancellation,
                            'no_resi' => $no_resi,
                            'shipping_method' => $shipping_method,
                            'shipping_fee' => $shipping_fee,
                            'payment_method' => $payment_method,
                            'total_payment' => $total_payment,
                            'city' => $city,
                            'province' => $province,
                            'courier' => $courier,
                        ];

                        $id_trx = OnlineTransactions::select('id', 'order_number', 'time_print')
                            ->where('order_number', $order_number)
                            ->first();

                        if ($id_trx->time_print == NULL) {
                            OnlineTransactions::where('id', $id_trx->id)->update($rowUpdate);
                            $insert_id = $id_trx->id;
                        }
                    }
                } catch (\Exception $e) {
                    // Log the exception message
                    \Log::error('Error processing TikTok data: ' . $e->getMessage());
                }
            }

            foreach ($data as $item) {
                $order_number = $item[0];
                $order_status = $item[1];
                $original_price = str_replace('.', '', $item[8]);
                $price_after_discount = str_replace('.', '', $item[9]);
                $qty = $item[10];
                $sku = ltrim($item[11], 'X');
                $return_qty = $item[12];
                $total_discount = str_replace('.', '', $item[13]);
                $discount_seller = str_replace('.', '', $item[13]);
                $discount_platform = str_replace('.', '', $item[14]);
                $warehouse = $item[19] ?? $st_code;


                try {
                    $to_id = OnlineTransactions::where('order_number', $order_number)->get()->first();

                    //cek current status
                    if ($order_status != 'Batal' || $order_status != 'Cancel') {
                        if ($to_id != null) {
                            $sku_exists = OnlineTransactionDetails::where('order_number', '=', $order_number)->where('sku', '=', $sku)->where('to_id', '=', $to_id->id)->exists();

                            $otd_warehouse = OnlineTransactionDetails::where('order_number', '=', $order_number)->where('sku', '=', $sku)->value('warehouse');

                            if ($to_id->internal_order_status != 'NEW TRX') {
                                $warehouse = $otd_warehouse;
                            } else {
                                $warehouse = $item[19] ?? $st_code;
                            }
                            $rowSku = [
                                'order_number' => $order_number,
                                'to_id' => $to_id->id,
                                'sku' => $sku,
                                'original_price' => $original_price,
                                'price_after_discount' => $price_after_discount,
                                'qty' => $qty,
                                'return_qty' => $return_qty,
                                'total_discount' => $total_discount,
                                'discount_seller' => $discount_seller,
                                //                                'ns_before_admin' => $ns_before_admin,
                                'discount_platform' => $discount_platform,
                                'warehouse' => $warehouse,
                            ];

                            if (!$sku_exists) {
                                OnlineTransactionDetails::create($rowSku);
                            } else {
                                OnlineTransactionDetails::where('to_id', '=', $to_id->id)
                                    ->where('sku', $sku)
                                    ->update($rowSku);
                            }
                        }
                        // Delete duplicates using Eloquent
                        $duplicateRecords = OnlineTransactionDetails::where('order_number', $order_number)
                            ->where('sku', $sku)
                            ->where('qty', $qty)
                            ->where('to_id', $to_id->id)
                            ->orderBy('id', 'asc') // Order by ID to keep the first one
                            ->get();

                        if ($duplicateRecords->count() > 1) {
                            // Keep the first record and delete the rest
                            $idsToDelete = $duplicateRecords->pluck('id')->slice(1); // Get all except the first
                            OnlineTransactionDetails::whereIn('id', $idsToDelete)->delete();
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing TikTok SKU data: ' . $e->getMessage());
                }
            }
        } else { // TikTok
            foreach ($data as $item) {
                $order_number = $item[0];
                $order_status = $item[1];
                $reason_cancellation = $item[2];
                $no_resi = $item[3];
                $shipping_method = $item[4];
                $order_date_created = $item[5];
                $payment_date = $item[6];
                $payment_method = $item[7];

                $shipping_fee = str_replace(['IDR ', '.'], '', $item[15]);
                $total_payment = str_replace(['IDR ', '.'], '', $item[16]);
                $city = $item[17];
                $province = $item[18];

                //get courier from ekspedisi column
                $courier = OnlineTransactions::getCourierAttribute($item[20]);

                $rowData = [
                    'st_id' => '20',
                    'order_number' => $order_number,
                    'order_status' => $order_status,
                    'reason_cancellation' => $reason_cancellation,
                    'no_resi' => $no_resi,
                    'platform_name' => 'TikTok',
                    'shipping_method' => $shipping_method,
                    'shipping_fee' => $shipping_fee,
                    'order_date_created' => $order_date_created,
                    'payment_date' => $payment_date,
                    'payment_method' => $payment_method,
                    'total_payment' => $total_payment,
                    'city' => $city,
                    'province' => $province,
                    'internal_order_status' => 'NEW TRX',
                    'courier' => $courier,
                ];

                try {
                    $get_order_number = OnlineTransactions::where('order_number', $order_number)->count();

                    if ($get_order_number == 0) {
                        $processedData[] = $rowData;
                        if ($rowData['order_status'] != 'Canceled' && $rowData['order_status'] != 'Batal') {
                            $transaction = OnlineTransactions::create($rowData);

                            OnlineTransactions::where('id', $transaction->id)->update(['st_id' => Auth::user()->st_id]);
                        }
                    } else {
                        $rowUpdate = [
                            'order_number' => $order_number,
                            'order_status' => $order_status,
                            'reason_cancellation' => $reason_cancellation,
                            'no_resi' => $no_resi,
                            'shipping_method' => $shipping_method,
                            'shipping_fee' => $shipping_fee,
                            'payment_method' => $payment_method,
                            'total_payment' => $total_payment,
                            'city' => $city,
                            'province' => $province,
                            'courier' => $courier,
                        ];

                        $id_trx = OnlineTransactions::select('id', 'order_number', 'time_print')
                            ->where('order_number', $order_number)
                            ->first();

                        if ($id_trx->time_print == NULL) {
                            OnlineTransactions::where('id', $id_trx->id)->update($rowUpdate);
                            $insert_id = $id_trx->id;
                        }
                    }
                } catch (\Exception $e) {
                    // Log the exception message
                    \Log::error('Error processing TikTok data: ' . $e->getMessage());
                }
            }

            foreach ($data as $item) {
                $order_number = $item[0];
                $order_status = $item[1];
                $original_price = str_replace(['IDR ', '.'], '', $item[8]);
                $price_after_discount = str_replace(['IDR ', '.'], '', $item[9]);
                $qty = $item[10];
                $sku = $sku = ltrim($item[11], 'X');
                $return_qty = $item[12];
                $total_discount = str_replace(['IDR ', '.'], '', $item[13]);
                $discount_seller = str_replace('.', '', $item[13]);
                $discount_platform = str_replace('.', '', $item[14]);
                $warehouse = $item[19] ?? $st_code;


                try {
                    $to_id = OnlineTransactions::where('order_number', $order_number)->get()->first();



                    if ($order_status != 'Batal' || $order_status != 'Canceled') {
                        if ($to_id != null) {
                            $sku_exists = OnlineTransactionDetails::where('order_number', '=', $order_number)->where('sku', '=', $sku)->exists();
                            $otd_warehouse = OnlineTransactionDetails::where('order_number', '=', $order_number)->where('sku', '=', $sku)->value('warehouse');

                            if ($to_id->internal_order_status != 'NEW TRX') {
                                $warehouse = $otd_warehouse;
                            } else {
                                $warehouse = $item[19] ?? $st_code;
                            }

                            $rowSku = [
                                'order_number' => $order_number,
                                'to_id' => $to_id->id,
                                'sku' => $sku,
                                'original_price' => $original_price,
                                'price_after_discount' => $price_after_discount,
                                'qty' => $qty,
                                'return_qty' => $return_qty,
                                'total_discount' => $total_discount,
                                'discount_seller' => $discount_seller,
                                //                                'ns_before_admin' => $ns_before_admin,
                                'discount_platform' => $discount_platform,
                                'warehouse' => $warehouse,
                            ];

                            if (!$sku_exists) {
                                OnlineTransactionDetails::create($rowSku);
                            } else {
                                OnlineTransactionDetails::where('to_id', $to_id->id)
                                    ->where('sku', $sku)
                                    ->update($rowSku);
                            }
                        }

                        $duplicateRecords = OnlineTransactionDetails::where('order_number', $order_number)
                            ->where('sku', $sku)
                            ->where('qty', $qty)
                            ->where('to_id', $to_id->id)
                            ->orderBy('id', 'asc') // Order by ID to keep the first one
                            ->get();

                        if ($duplicateRecords->count() > 1) {
                            // Keep the first record and delete the rest
                            $idsToDelete = $duplicateRecords->pluck('id')->slice(1); // Get all except the first
                            OnlineTransactionDetails::whereIn('id', $idsToDelete)->delete();
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing TikTok SKU data: ' . $e->getMessage());
                }
            }
        }

        return [
            'processedData' => $processedData
        ];
    }

    public function getOnlineTransactionItems(Request $request)
    {
        $to_id = $request->to_id;

        try {
            $items = OnlineTransactionDetails::where('to_id', $to_id)->get();

            return response()->json(['status' => '200', 'data' => $items]);
        } catch (\Exception $e) {
            \Log::error('Error fetching online transaction items: ' . $e->getMessage());
            return response()->json(['status' => '500', 'message' => 'An error occurred while fetching the items']);
        }
    }

    public function addNewItem(Request $request)
    {
        $sku = $request->sku;
        $otd_id = $request->item_sejenis;
        $qty = $request->qty;

        $similar_item = OnlineTransactionDetails::where('id', $otd_id)->first();

        $sku_exist = ProductStock::where('ps_barcode', $sku)->exists();

        if (!$sku_exist) {
            return response()->json(['status' => '404', 'message' => 'SKU not found in product stock']);
        }

        if (!$similar_item) {
            return response()->json(['status' => '404', 'message' => 'Similar item not found']);
        }

        try {
            OnlineTransactionDetails::create([
                'to_id' => $similar_item->to_id,
                'order_number' => $similar_item->order_number,
                'warehouse' => $similar_item->warehouse,
                'sku' => $sku,
                'qty' => $qty,
                'return_qty' => 0,
                'original_price' => $similar_item->original_price,
                'discount_seller' => $similar_item->discount_seller,
                'discount_platform' => $similar_item->discount_platform,
                'total_discount' => $similar_item->total_discount,
                'price_after_discount' => $similar_item->price_after_discount,
                'created_at' => now(),
                'created_by' => Auth::user()->id,
            ]);
            return response()->json(['status' => '200', 'message' => 'Items added successfully']);
        } catch (\Exception $e) {
            \Log::error('Error adding new items: ' . $e->getMessage());
            return response()->json(['status' => '500', 'message' => 'An error occurred while adding the items']);
        }
    }

    public function clearPrintStatus($to_id)
    {
        $online_transactions = OnlineTransactions::where('id', $to_id)->first();
        $check = PosTransaction::where(['pos_invoice' => $online_transactions->order_number])
            ->orderByDesc('id')
            ->value('pos_status') === 'REFUND' ? true : false;

        if (!$check) {
            return response()->json([
                'status' => '400',
                'message' => 'Bukan transaksi refund, tidak dapat menghapus status cetak.'
            ]);
        }

        try {
            $update_print_status = $online_transactions->update(['online_print' => 0, 'time_print' => null, 'print_resi' => 0, 'time_print_resi' => null]);
            if ($update_print_status === false) {
                return response()->json(['status' => '500', 'message' => 'Failed to clear print status']);
            }

            return response()->json(['status' => '200', 'message' => 'Print status cleared successfully']);
        } catch (\Exception $e) {
            \Log::error('Error clearing print status: ' . $e->getMessage());
            return response()->json(['status' => '500', 'message' => 'An error occurred while clearing the print status']);
        }
    }

    public function cancelTran($to_id)
    {
        try {
            DB::beginTransaction();

            $online_transactions = OnlineTransactions::where('id', $to_id)->first();

            if (!$online_transactions) {
                DB::rollBack();
                return response()->json([
                    'status' => '404',
                    'message' => 'Transaksi tidak ditemukan.'
                ]);
            }

            //check status must be under review and not have active pick items
            if ($online_transactions->internal_order_status != 'UNDER REVIEW') {
                DB::rollBack();
                return response()->json([
                    'status' => '400',
                    'message' => 'Status transaksi harus UNDER REVIEW untuk dibatalkan.'
                ]);
            }

            //get all item that already picked and pass qc then return it to staging failed qc
            $waiting_receipt_items = ProductLocationSetupTransaction::query()
                ->select('product_location_setup_transactions.*')
                ->join('online_transaction_details', 'online_transaction_details.id', '=', 'product_location_setup_transactions.otd_id')
                ->where('plst_status', 'WAITING RECEIPT')->where('to_id', $to_id)->get();

            foreach ($waiting_receipt_items as $item) {
                $plst = DB::table('product_location_setup_transactions')
                    ->where('id', $item->id)
                    ->first();

                if (!$plst) {
                    DB::rollBack();
                    return response()->json(['status' => '400', 'message' => 'Product location setup transaction not found.']);
                }

                $update_plst = DB::table('product_location_setup_transactions')
                    ->where('id', $item->id)
                    ->update([
                        'plst_type' => 'IN',
                        'plst_status' => 'INSTOCK',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                if (!$update_plst) {
                    DB::rollBack();
                    return response()->json(['status' => '400', 'message' => 'Failed to update product location setup transaction.']);
                }

                $pl_default_failed_qc = ProductLocation::where('st_id', $plst->warehouse_st_id)
                    ->where('pl_default_failed_qc', 1)
                    ->get();

                if ($pl_default_failed_qc->count() == 0) {
                    DB::rollBack();
                    return response()->json(['status' => '400', 'message' => 'Default bin for failed QC not found.']);
                }

                if ($pl_default_failed_qc->count() > 1) {
                    DB::rollBack();
                    return response()->json(['status' => '400', 'message' => 'Multiple default bins for failed QC found.']);
                }

                $pls_default_failed_qc = DB::table('product_location_setups')
                    ->where('pl_id', $pl_default_failed_qc->first()->id)
                    ->where('pst_id', $plst->pst_id)
                    ->first();

                if (!$pls_default_failed_qc) {
                    $new_pls_id = DB::table('product_location_setups')->insertGetId([
                        'pl_id' => $pl_default_failed_qc->first()->id,
                        'pst_id' => $plst->pst_id,
                        'pls_qty' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    if (!$new_pls_id) {
                        DB::rollBack();
                        return response()->json(['status' => '400', 'message' => 'Failed to add new pls.']);
                    }

                    $pls_default_failed_qc = DB::table('product_location_setups')
                        ->where('id', $new_pls_id)
                        ->first();
                }

                $add_to_default_failed_qc = DB::table('product_location_setups')
                    ->where('id', $pls_default_failed_qc->id)
                    ->update([
                        'pls_qty' => DB::raw("pls_qty + " . $plst->plst_qty),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                if (!$add_to_default_failed_qc) {
                    DB::rollBack();
                    return response()->json(['status' => '400', 'message' => 'Failed add qty to bin default failed qc.']);
                }
            }

            $is_picked = ProductLocationSetupTransaction::query()
                ->join('online_transaction_details', 'online_transaction_details.id', '=', 'product_location_setup_transactions.otd_id')
                ->whereNotIn('plst_status', ['DONE', 'INSTOCK', 'REFUND'])->where('to_id', $to_id)->exists();

            if ($is_picked) {
                DB::rollBack();
                return response()->json([
                    'status' => '400',
                    'message' => 'Transaksi memiliki item yang sudah dipick, tidak dapat dibatalkan.'
                ]);
            }

            $update_status = $online_transactions->update(['internal_order_status' => 'NEW TRX']);
            if ($update_status === false) {
                DB::rollBack();
                return response()->json(['status' => '500', 'message' => 'Failed to cancel transaction']);
            }

            DB::commit();
            return response()->json(['status' => '200', 'message' => 'Transaction canceled successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error canceling transaction: ' . $e->getMessage());
            return response()->json(['status' => '500', 'message' => 'An error occurred while canceling the transaction']);
        }
    }
}
