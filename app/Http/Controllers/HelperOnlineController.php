<?php

namespace App\Http\Controllers;

use App\Models\ModalLockAllowedModel;
use App\Models\OnlineTransactionDetails;
use App\Models\OnlineTransactions;
use App\Models\PaymentMethod;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\ProductLocation;
use App\Models\ProductLocationSetupTransaction;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\StoreTypeDivision;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\WarehouseIndex;
use App\Models\WebConfig;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Driver\FpdiDriver;
use Illuminate\Support\Facades\File;
use setasign\Fpdi\Fpdi;

class HelperOnlineController extends Controller
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
            'st_id' => Auth::user()->st_id,
            // 'warehouse' => WarehouseIndex::query()->where('st_id', Auth::user()->st_id)->first()->w_code,
            'platforms' => DB::table('online_transactions')
                ->select('platform_name')
                ->distinct()
                ->orderBy('platform_name', 'ASC')
                ->get(),
            'modal_lock_active' => ModalLockAllowedModel::where('is_active', true)->where('identifier', 'helper_online')->exists(),
            'expeditions' => DB::table('couriers')->orderBy('cr_name', 'ASC')->get()
        ];

        //        $dataResi = [
        //            [
        //                'no_resi' => 'JX123456789',
        //                'nama_barang' => 'Sepatu Sneakers Hitam',
        //                'jumlah' => 2,
        //                'jumlah_barang' => 12,
        //                'gambar' => 'https://via.placeholder.com/150',
        //            ],
        //            [
        //                'no_resi' => 'SPX998877665',
        //                'nama_barang' => 'Kaos Oversize Putih, Sepatu Sneakers Hitam',
        //                'jumlah' => 1,
        //                'jumlah_barang' => 1,
        //                'gambar' => 'https://via.placeholder.com/150',
        //            ],
        //            [
        //                'no_resi' => 'SPX9988776625',
        //                'nama_barang' => 'Kaos Oversize Putih',
        //                'jumlah' => 1,
        //                'jumlah_barang' => 3,
        //                'gambar' => 'https://via.placeholder.com/150',
        //            ],
        //        ];

        $dataResi = DB::table('online_transactions')
            ->join('online_transaction_details', 'online_transaction_details.to_id', '=', 'online_transactions.id')
            ->join('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
            ->join('products', 'products.id', '=', 'product_stocks.p_id')
            ->select(
                'online_transactions.no_resi',
                DB::raw('GROUP_CONCAT(DISTINCT ts_products.p_name SEPARATOR ", ") as nama_barang'),
                DB::raw('SUM(ts_online_transaction_details.qty) as jumlah_barang'),
                DB::raw('COUNT(DISTINCT ts_products.id) as jumlah'),
                DB::raw('"https://via.placeholder.com/150" as gambar')
            )
            ->groupBy('online_transactions.no_resi')
            ->where('internal_order_status', '=', 'WAITING RECEIPT')
            ->whereNotNull('online_transactions.no_resi')
            ->get();

        return view('app.helper_online.helper_online', compact('data', 'dataResi'));
    }

    public function mergeResi(Request $request)
    {
        $resiList = $request->resi ?? [];

        if (empty($resiList)) {
            return response()->json(['error' => 'Tidak ada resi yang dipilih'], 400);
        }

        // Pastikan direktori split_resi ada
        $splitResiPath = storage_path('app/public/split_resi');
        if (!File::exists($splitResiPath)) {
            File::makeDirectory($splitResiPath, 0755, true);
        }

        // Buat objek merger
        $merger = new Merger(new Fpdi());

        foreach ($resiList as $resi) {
            $path = $splitResiPath . '/' . $resi . '.pdf';

            if (File::exists($path)) {
                $merger->addFile($path);
            }
        }

        // Gabungkan PDF
        $mergedPdf = $merger->merge();

        // Simpan hasil gabungan ke file baru
        $mergedName = 'merged_resi_' . time() . '.pdf';
        $outputPath = $splitResiPath . '/' . $mergedName;
        File::put($outputPath, $mergedPdf);

        // Buat URL publik untuk dibuka
        $url = asset('storage/split_resi/' . $mergedName);

        return response()->json(['url' => $url]);
    }

    public function getDatatables(Request $request)
    {
        $st_id = $request->get('st_id');
        $query = DB::table('product_location_setup_transactions')
            ->join('online_transaction_details', 'product_location_setup_transactions.otd_id', '=', 'online_transaction_details.id')
            ->join('online_transactions', 'online_transaction_details.to_id', '=', 'online_transactions.id')
            ->join('stores', 'online_transactions.st_id', '=', 'stores.id')
            ->select(
                'online_transactions.order_number',
                'platform_name AS platform',
                'st_name AS store',
                DB::raw("GROUP_CONCAT(DISTINCT CONCAT(ts_online_transaction_details.sku, ' (', ts_online_transaction_details.qty, ')') ORDER BY ts_online_transaction_details.sku ASC SEPARATOR ', ') AS sku"),
                'online_transactions.order_date_created AS created_at',
                'online_transactions.internal_order_status AS internal_order_status'
            )
            ->where('product_location_setup_transactions.warehouse_st_id', $st_id)
            ->groupBy('online_transactions.order_number', 'platform_name', 'st_name', 'online_transactions.order_date_created')
            ->orderBy('online_transactions.order_date_created', 'DESC');
    }

    public function getListPickedOnline(Request $request)
    {
        $st_id = $request->get('st_id');
        $status_filter = $request->get('status_filter');
        $order_number = $request->get('order_number');
        $status_pick = $request->get('status_pick');
        $platform = $request->get('platform');
        $filter_date = $request->get('filter_date');
        $filter_date_type = $request->get('filter_date_type');

        $start = null;
        $end = null;
        $exp = explode('|', $filter_date);
        if (count($exp) > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            $start = $filter_date;
        }

        $stores_code = Store::where('id', $st_id)->value('st_code');

        $baseQuery = DB::table('product_location_setup_transactions')
            ->join('online_transaction_details', 'product_location_setup_transactions.otd_id', '=', 'online_transaction_details.id')
            ->join('online_transactions', 'online_transaction_details.to_id', '=', 'online_transactions.id')
            ->join('stores', 'online_transactions.st_id', '=', 'stores.id')
            ->select(
                'product_location_setup_transactions.id as plst_id',
                'online_transactions.id as transaction_id',
                'online_transactions.order_number',
                'platform_name AS platform',
                'st_name AS store',
                DB::raw("CASE
                    WHEN SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = 'WAITING ONLINE' THEN 1 ELSE 0 END) > 0
                     AND SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = 'WAITING ONLINE' AND ts_product_location_setup_transactions.qc_status = '" . ProductLocationSetupTransaction::QC_STATUS_ON_GOING . "' THEN 1 ELSE 0 END)
                         = SUM(CASE WHEN ts_product_location_setup_transactions.plst_status = 'WAITING ONLINE' THEN 1 ELSE 0 END)
                    THEN TRUE
                    ELSE FALSE
                END AS all_picked"),
                'online_transactions.order_date_created AS created_at',
                'online_transactions.internal_order_status AS internal_order_status',
                DB::raw('MAX(ts_product_location_setup_transactions.created_at) AS picked_time'),
                'no_resi',
                DB::raw('SUM(ts_online_transaction_details.qty) AS total_picked'),
                DB::raw('COUNT(DISTINCT ts_online_transaction_chat_history.id) as unreaded_chat'),
                DB::raw("MAX(CASE WHEN ts_online_transaction_chat_history.is_readed = 0 AND ts_online_transaction_chat_history.is_amp = 1 THEN ts_online_transaction_chat_history.created_at END) as last_chat_time"),
                DB::raw('CASE WHEN shipping_method LIKE "%Instan%" THEN 1 ELSE 0 END as is_instant'),
                'online_print',
                'shipping_method',
                'print_resi',
            )
            ->leftJoin('online_transaction_chat_history', function ($join) {
                $join->on('online_transaction_chat_history.ot_id', '=', 'online_transactions.id')
                    ->where('online_transaction_chat_history.is_readed', '=', 0)
                    ->where('online_transaction_chat_history.is_amp', '=', 1);
            })
            ->where('product_location_setup_transactions.warehouse_st_id', $st_id)
            ->whereNotIn('product_location_setup_transactions.plst_status', ['REFUND'])
            ->where('online_transaction_details.deleted_at', null)
            ->where('warehouse', $stores_code)
            ->groupBy('online_transactions.id', 'online_transactions.order_number', 'platform_name', 'st_name', 'online_transactions.order_date_created', 'no_resi', 'online_print', 'shipping_method', 'online_transactions.internal_order_status')
            ->orderByRaw('CASE WHEN is_instant = 1 THEN 0 ELSE 1 END')
            ->orderByDesc('last_chat_time')
            ->orderBy('picked_time', 'asc');

        $allData = $baseQuery->get();

        $total_waiting_online = $allData->where('internal_order_status', 'WAITING ONLINE')->count();
        $total_under_review = $allData->where('internal_order_status', 'UNDER REVIEW')->count();
        $total_waiting_receipt = $allData->where('internal_order_status', 'WAITING RECEIPT')->count();
        $total_waiting_packing = $allData->where('internal_order_status', 'WAITING PACKING')->count();
        $total_done_online = $allData->where('internal_order_status', 'DONE ONLINE')->count();

        $transactions = $allData
            ->when($status_filter, function ($collection, $status_filter) {
                return $collection->where('internal_order_status', $status_filter);
            })
            ->when($order_number, function ($collection, $order_number) {
                $order_number = trim($order_number);
                if ($order_number === '') {
                    return $collection;
                }

                $matchedTransactionIds = OnlineTransactionDetails::query()
                    ->whereIn('to_id', $collection->pluck('transaction_id')->filter())
                    ->where('sku', 'like', '%' . $order_number . '%')
                    ->pluck('to_id')
                    ->unique()
                    ->all();

                return $collection->filter(function ($item) use ($order_number, $matchedTransactionIds) {
                    $orderNumberMatch = stripos($item->order_number, $order_number) !== false;
                    $resiMatch = stripos((string) ($item->no_resi ?? ''), $order_number) !== false;
                    $skuMatch = in_array($item->transaction_id, $matchedTransactionIds, true);

                    return $orderNumberMatch || $resiMatch || $skuMatch;
                });
            })
            ->when($status_pick, function ($collection, $status_pick) {
                if ($status_pick === 'PICKED') {
                    return $collection->filter(fn($item) => (bool) $item->all_picked);
                }
                if ($status_pick === 'NOT PICKED') {
                    return $collection->filter(fn($item) => ! (bool) $item->all_picked);
                }
                return $collection;
            })
            ->when($platform, function ($collection, $platform) {
                if ($platform === '') {
                    return $collection;
                }
                return $collection->where('platform', $platform);
            })
            ->when($filter_date_type && $start, function ($collection) use ($filter_date_type, $start, $end) {
                return $collection->filter(function ($item) use ($filter_date_type, $start, $end) {
                    if ($filter_date_type === 'pick_date') {
                        $pickDate = $item->picked_time;
                        if ($end) {
                            return $pickDate >= $start . ' 00:00:00' && $pickDate <= $end . ' 23:59:59';
                        } else {
                            return date('Y-m-d', strtotime($pickDate)) === $start;
                        }
                    } elseif ($filter_date_type === 'transaction_date') {
                        $transactionDate = $item->created_at;
                        if ($end) {
                            return $transactionDate >= $start . ' 00:00:00' && $transactionDate <= $end . ' 23:59:59';
                        } else {
                            return date('Y-m-d', strtotime($transactionDate)) === $start;
                        }
                    }
                    return true;
                });
            });

        $data = [
            'transactions' => $transactions,
            'total_waiting_online' => $total_waiting_online,
            'total_under_review' => $total_under_review,
            'total_waiting_receipt' => $total_waiting_receipt,
            'total_waiting_packing' => $total_waiting_packing,
            'total_done_online' => $total_done_online,
        ];

        return response()->json($data);
    }

    public function getOnlineItems(Request $request)
    {
        $transaction_id = $request->get('ot_id');
        $user_st_id = Auth::user()->st_id;

        $items = DB::table('online_transactions')
            ->join('online_transaction_details', 'online_transactions.id', '=', 'online_transaction_details.to_id')
            ->leftJoin('product_location_setup_transactions', 'online_transaction_details.id', '=', 'product_location_setup_transactions.otd_id')
            ->join('product_stocks', 'online_transaction_details.sku', '=', 'product_stocks.ps_barcode')
            ->leftJoin('sizes', 'product_stocks.sz_id', '=', 'sizes.id')
            ->join('products', 'product_stocks.p_id', '=', 'products.id')
            ->join('brands', 'products.br_id', '=', 'brands.id')
            ->select(
                'online_transactions.order_number',
                'online_transaction_details.id as otd_id',
                'online_transaction_details.sku as ps_barcode',
                'product_location_setup_transactions.id as plst_id',
                'product_location_setup_transactions.plst_status',
                'product_location_setup_transactions.plst_qty',
                'products.p_name',
                'products.p_color',
                'product_location_setup_transactions.created_at as plst_created',
                'sizes.sz_name',
                'brands.br_name',
                'product_location_setup_transactions.id as plst_id',
                DB::raw('(select SUM(pls_qty) from ts_product_location_setups join ts_product_locations on ts_product_locations.id = pl_id where ts_product_location_setups.pst_id = ts_product_location_setup_transactions.pst_id and ts_product_locations.st_id = warehouse_st_id) as current_qty'),
                'product_location_setup_transactions.warehouse_st_id',
                'product_location_setup_transactions.pst_id as pst_id',
                'product_location_setup_transactions.pls_id as pls_id',
                'product_location_setup_transactions.qc_status',
                'online_transactions.id as to_id',
            )
            ->where('online_transactions.id', $transaction_id)
            ->where('online_transaction_details.deleted_at', null)
            ->get();

        return datatables()->of($items)
            ->addColumn('item', function ($data,) use ($user_st_id) {
                $p_name = $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name;
                $dateTime = $data->plst_created; // '2024-08-07 14:13:46'
                $time = Carbon::parse($dateTime)->format('H:i:s'); // '14:13:46'
                $items = '
                ' . (function () use ($data) {
                    if ($data->plst_status === null) {
                        return '<span class="badge badge-dark" style="white-space: nowrap; font-weight:bold;">Not Picked</span>';
                    }
                    $statusClasses = [
                        'WAITING ONLINE' => 'warning',
                        'UNDER REVIEW' => 'secondary',
                        'WAITING RECEIPT' => 'primary',
                        'WAITING PACKING' => 'danger',
                        'INSTOCK' => 'info'
                    ];
                    $badgeClass = $statusClasses[$data->plst_status] ?? 'secondary';
                    return '<span class="badge badge-' . $badgeClass . '" style="white-space: nowrap; font-weight:bold;">' . $data->plst_status . '</span>';
                })() . '
                <span style="white-space: nowrap; font-weight:bold;">[' . $data->br_name . ']<br/>' . $data->ps_barcode . ' - ' . $data->p_name . '<br/>' . $data->p_color . ' (' . $data->sz_name . ')</span><br/><span style="white-space: nowrap; font-weight:bold; font-size: 10px;">' . $time . ' </span><br/>
                <div class="d-flex justify-content-between align-items-center">
                <div>
                <span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm btn-primary">Jml : ' . $data->plst_qty . '</span>
                <span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm btn-primary">Stok : ' . $data->current_qty . '</span>
                </div>';

                if ($data->plst_status == 'WAITING ONLINE' && $data->pls_id == null) {
                    $isDisabled = ($user_st_id != $data->warehouse_st_id);
                    $disabledClass = $isDisabled ? ' disabled' : '';
                    $disabledStyle = $isDisabled ? ' pointer-events: none; opacity: 0.6;' : '';
                    $items .= '<div><a class="btn btn-sm btn-info ml-1' . $disabledClass . '" data-plst_id="' . $data->plst_id . '" id="cancel_pick" style="font-weight:bold;' . $disabledStyle . '">Batal</a><a class="btn btn-sm btn-success ml-1' . $disabledClass . '" data-status="pickup" data-plst_id="' . $data->plst_id . '" data-p_name="' . $p_name . '" data-pst_id="' . $data->pst_id . '" data-qty="' . $data->plst_id . '" data-warehouse_st_id="' . $data->warehouse_st_id . '"data-sku="' . $data->ps_barcode . '" id="pick_get_bin_products" style="font-weight:bold;' . $disabledStyle . '">Ambil</a></div>';
                }

                if ($data->plst_status == 'WAITING ONLINE' && $data->pls_id && $data->qc_status == ProductLocationSetupTransaction::QC_STATUS_ON_GOING) {
                    $isDisabled = ($user_st_id != $data->warehouse_st_id);
                    $disabledClass = $isDisabled ? ' disabled' : '';
                    $disabledStyle = $isDisabled ? ' pointer-events: none; opacity: 0.6;' : '';
                    $items .= '<a class="btn btn-sm btn-dark ml-1' . $disabledClass . '" data-status="pickup" data-to_id="' . $data->to_id . '" data-plst_id="' . $data->plst_id . '" data-p_name="' . $p_name . '" data-pst_id="' . $data->pst_id . '" data-qty="' . $data->plst_id . '" data-warehouse_st_id="' . $data->warehouse_st_id . '"data-sku="' . $data->ps_barcode . '" id="submit_qc" style="font-weight:bold;' . $disabledStyle . '">Under QC</a>';
                }
                if ($data->plst_status == 'INSTOCK' && $data->pls_id && $data->qc_status == ProductLocationSetupTransaction::QC_STATUS_FAILED) {
                    $items .= '<a class="btn btn-sm btn-dark ml-1 disabled" data-status="pickup" data-plst_id="' . $data->plst_id . '" data-p_name="' . $p_name . '" data-pst_id="' . $data->pst_id . '" data-qty="' . $data->plst_id . '" data-warehouse_st_id="' . $data->warehouse_st_id . '"data-sku="' . $data->ps_barcode . '" style="font-weight:bold; pointer-events: none; opacity: 0.6;">Gagal QC</a>';
                }
                if ($data->plst_status == 'WAITING RECEIPT' && $data->pls_id && $data->qc_status == ProductLocationSetupTransaction::QC_STATUS_PASSED) {
                    $items .= '<a class="ml-1 disabled" data-status="pickup" data-plst_id="' . $data->plst_id . '" data-p_name="' . $p_name . '" data-pst_id="' . $data->pst_id . '" data-qty="' . $data->plst_id . '" data-warehouse_st_id="' . $data->warehouse_st_id . '"data-sku="' . $data->ps_barcode . '" style="font-weight:bold; pointer-events: none; opacity: 0.6; background-color: #28a745; color: white; border: 1px solid #28a745; padding: 0.25rem 0.5rem; border-radius: 0.2rem; display: inline-block; text-decoration: none;">LOLOS QC</a>';
                }

                $items .= '</div>';

                return $items;
            })
            ->rawColumns(['item'])
            ->make(true);
    }

    public function getPickHistory($transactionId)
    {
        $data = array();
        //get online transaction table log
        $online_transactions = DB::table('online_transactions')
            ->leftJoin('users as import', 'import.id', '=', 'online_transactions.imported_by')
            ->leftJoin('users as print_nota', 'print_nota.id', '=', 'online_transactions.u_print')
            ->leftJoin('users as print_resi', 'print_resi.id', '=', 'online_transactions.u_print_resi')
            ->select(
                'import.u_name as imported_by',
                'online_transactions.created_at as imported_at',
                'print_nota.u_name as print_nota',
                'online_transactions.time_print as nota_print_time',
                'print_resi.u_name as print_resi',
                'online_transactions.time_print_resi as resi_print_time'
            )
            ->where('online_transactions.id', $transactionId)
            ->first();

        $data[] = [
            'activity' => 'Import Pesanan',
            'by' => $online_transactions->imported_by,
            'at' => $online_transactions->imported_at,
        ];

        if ($online_transactions->print_nota) {
            $data[] = [
                'activity' => 'Print Nota',
                'by' => $online_transactions->print_nota,
                'at' => $online_transactions->nota_print_time,
            ];
        }

        if ($online_transactions->print_resi) {
            $data[] = [
                'activity' => 'Print Resi',
                'by' => $online_transactions->print_resi,
                'at' => $online_transactions->resi_print_time,
            ];
        }

        //get product location setup transaction log
        $plst = DB::table('online_transactions')
            ->leftJoin('online_transaction_details', 'online_transaction_details.to_id', '=', 'online_transactions.id')
            ->leftJoin('product_location_setup_transactions as plst', 'plst.otd_id', '=', 'online_transaction_details.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'plst.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
            ->leftJoin('users as amp_pick', 'amp_pick.id', '=', 'plst.u_id')
            ->leftJoin('users as helper_pick', 'helper_pick.id', '=', 'plst.u_id_helper')
            ->leftJoin('users as helper_qc', 'helper_qc.id', '=', 'plst.u_id_helper_qc')
            ->leftJoin('users as instock', 'instock.id', '=', 'plst.u_id_instock')
            ->leftJoin('users as helper_pack', 'helper_pack.id', '=', 'plst.u_id_packer')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'plst.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->select(
                'products.p_name',
                'sizes.sz_name',
                'main_colors.mc_name',
                'products.p_color',
                'product_stocks.ps_barcode',
                'amp_pick.u_name as pick_amp_by',
                'plst.created_at as pick_amp_at',
                'helper_pick.u_name as pick_helper_by',
                'plst.move_store_time as pick_helper_at',
                'product_locations.pl_code as pick_location',
                'helper_qc.u_name as qc_by',
                'plst.done_qc_time as qc_at',
                'plst.qc_status as qc_status',
                'instock.u_name as instock_by',
                'plst.in_stock_time as instock_at',
                'helper_pack.u_name as packed_by',
                'plst.packed_time as packed_at'
            )
            ->where('online_transactions.id', $transactionId)
            ->orderBy('plst.created_at')
            ->get();

        foreach ($plst as $item) {
            $item_details = [
                'ps_barcode' => $item->ps_barcode,
                'p_name' => $item->p_name,
                'p_color' => $item->p_color,
                'sz_name' => $item->sz_name,
            ];

            $data[] = [
                'activity' => 'Pick AMP',
                'by' => $item->pick_amp_by,
                'at' => $item->pick_amp_at,
                'item_details' => $item_details,
            ];
            if ($item->pick_helper_by) {
                $data[] = [
                    'activity' => 'Pick Helper',
                    'by' => $item->pick_helper_by,
                    'at' => $item->pick_helper_at,
                    'location' => $item->pick_location,
                    'item_details' => $item_details,
                ];
            }
            if ($item->qc_by) {
                $data[] = [
                    'activity' => 'Quality Check',
                    'by' => $item->qc_by,
                    'at' => $item->qc_at,
                    'status' => $item->qc_status,
                    'item_details' => $item_details,
                ];
            }
            if ($item->instock_by) {
                $data[] = [
                    'activity' => 'In Stock',
                    'by' => $item->instock_by,
                    'at' => $item->instock_at,
                    'item_details' => $item_details,
                ];
            }
            if ($item->packed_by) {
                $data[] = [
                    'activity' => 'Packed',
                    'by' => $item->packed_by,
                    'at' => $item->packed_at,
                    'item_details' => $item_details,
                ];
            }
        }

        //get manifest log
        $manifest =
            DB::table('online_transactions')
            ->leftJoin('delivery_receipts', 'online_transactions.no_resi', '=', 'delivery_receipts.resi')
            ->leftJoin('delivery_recaps', 'delivery_receipts.dr_id', '=', 'delivery_recaps.id')
            ->leftJoin('users as manifest_user', 'delivery_recaps.created_by', '=', 'manifest_user.id')
            ->select(
                'manifest_user.u_name as manifest_by',
                'delivery_recaps.created_at as manifest_at',
                'delivery_recaps.manifest_number as manifest_number'
            )
            ->where('online_transactions.id', $transactionId)
            ->first();

        if ($manifest && $manifest->manifest_by) {
            $data[] = [
                'activity' => 'Manifest',
                'by' => $manifest->manifest_by,
                'at' => $manifest->manifest_at,
                'manifest_number' => $manifest->manifest_number
            ];
        }

        usort($data, function ($a, $b) {
            if (!isset($a['at']) || $a['at'] === null) {
                if (!isset($b['at']) || $b['at'] === null) {
                    return 0;
                }
                return 1;
            }
            if (!isset($b['at']) || $b['at'] === null) {
                return -1;
            }
            return strtotime($a['at']) - strtotime($b['at']);
        });

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json($data);
    }

    public function getBin(Request $request)
    {
        $warehouse_st_id = $request->get('warehouse_id');
        $pst_id = $request->get('pst_id');

        $bins = DB::table('product_location_setups')
            ->select('pls_qty', 'pl_code', 'product_location_setups.id as pls_id')
            ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->where('product_location_setups.pst_id', $pst_id)
            ->where('product_locations.st_id', $warehouse_st_id)
            ->where('pls_qty', '!=', 0)
            ->get();

        return response()->json(['data' => $bins]);
    }

    public function pickItem(Request $request)
    {
        //        $pls_id = $request->_pls_id;
        $plst_id = $request->_plst_id;
        $sku = $request->_sku;
        $bin = $request->_bin;
        $bin_id = $request->_bin_id; // New variable to hold bin_id
        $u_id = Auth::user()->id;
        $plst_qty = $request->_plst_qty;

        // check is plst_id already picked
        $isAlreadyPicked = DB::table('product_location_setup_transactions')
            ->where('id', $plst_id)
            ->whereNotNull('pls_id')
            ->exists();

        if ($isAlreadyPicked) {
            return response()->json(['status' => '400', 'message' => 'Item sudah dipick sebelumnya.']);
        }

        //pst_id
        $pst_id = DB::table('product_stocks')->where('ps_barcode', $sku)->first()->id;

        //pl_id selected
        // $pl_id_selected = DB::table('product_locations')->where('pl_code', "=", "$bin")->first()->id;

        //get pls_id and check qty can't less than 1
        $pls_qty = DB::table('product_location_setups')->where('id', $bin_id)->where('pst_id', $pst_id)->first()->pls_qty;
        if ($pls_qty < 1) {
            return response()->json(['status' => '400', 'message' => 'Quantity Bin tidak boleh kurang dari 1.']);
        }

        $pls_id_selected = DB::table('product_location_setups')->where('id', $bin_id)->where('pst_id', $pst_id)->first()->id;

        $update_pls = DB::table('product_location_setups')->where('id', $pls_id_selected)
            ->update([
                'pls_qty' => DB::raw("pls_qty - $plst_qty"),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update_pls) {
            $update_plst = DB::table('product_location_setup_transactions')->where('id', $plst_id)
                ->whereIn('plst_status', ['WAITING ONLINE'])->update([
                    'u_id_helper' => $u_id,
                    'plst_type' => 'OUT',
                    'pls_id'        => $pls_id_selected,
                    'plst_status' => 'WAITING ONLINE',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'move_store_time' => date('Y-m-d H:i:s'),
                    'qc_status' => ProductLocationSetupTransaction::QC_STATUS_ON_GOING
                ]);


            if ($update_plst) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
            return json_encode($r);
        }
    }

    public function qualityCheckItem(Request $request)
    {
        $plst_id = $request->plst_id;
        $qc_status = $request->qc_status;
        $to_id = $request->to_id;

        DB::beginTransaction();
        try {
            $check = DB::table('product_location_setup_transactions')
                ->where('id', $plst_id)
                ->where('qc_status', ProductLocationSetupTransaction::QC_STATUS_ON_GOING)
                ->exists();

            if (!$check) {
                return response()->json(['status' => '400', 'message' => 'Transaksi tidak ditemukan atau sudah selesai QC.']);
            }

            if ($qc_status == 'passed') {
                $update = DB::table('product_location_setup_transactions')
                    ->where('id', $plst_id)
                    ->update([
                        'u_id_helper_qc' => Auth::user()->id,
                        'qc_status' => ProductLocationSetupTransaction::QC_STATUS_PASSED,
                        'plst_status' => 'WAITING RECEIPT',
                        'done_qc_time' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $items = OnlineTransactionDetails::query()->where('to_id', $to_id)->get();
                $item_ids = $items->pluck('id')->toArray();

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
                    ->where('product_location_setup_transactions.plst_status', 'WAITING RECEIPT')->where('product_location_setup_transactions.qc_status', ProductLocationSetupTransaction::QC_STATUS_PASSED)
                    ->select('product_stocks.ps_barcode', DB::raw('SUM(ts_product_location_setup_transactions.plst_qty) as total_picked'), 'otd_id')
                    ->groupBy('product_stocks.ps_barcode', 'otd_id')
                    ->get()
                    ->keyBy('otd_id');

                if ($waiting_receipt_items->isEmpty()) {
                    DB::rollBack();
                    return response()->json([
                        'status' => '400',
                        'message' => 'Belum ada item yang dipick oleh helper.'
                    ]);
                }

                // bandingkan qty yang diorder dengan qty yang sudah dipick
                $all_picked = true;

                foreach ($active_transaction_items as $item) {
                    $picked_item = $waiting_receipt_items->get($item->id);
                    $picked_qty = $picked_item ? $picked_item->total_picked : 0;

                    if ($picked_qty != $item->qty) {
                        $all_picked = false;
                        break;
                    }
                }

                if ($all_picked) {
                    OnlineTransactions::where('id', $to_id)
                        ->update(['internal_order_status' => 'WAITING RECEIPT', 'updated_at' => date('Y-m-d H:i:s')]);
                }

                if ($update) {
                    DB::commit();
                    return response()->json(['status' => '200', 'message' => 'Item berhasil melewati QC.']);
                } else {
                    DB::rollBack();
                    return response()->json(['status' => '400', 'message' => 'Gagal memperbarui status QC.']);
                }
            } elseif ($qc_status == 'failed') {
                $plst = DB::table('product_location_setup_transactions')
                    ->where('id', $plst_id)
                    ->first();

                if (!$plst) {
                    DB::rollBack();
                    return response()->json(['status' => '400', 'message' => 'Product location setup transaction not found.']);
                }

                $update_plst = DB::table('product_location_setup_transactions')
                    ->where('id', $plst_id)
                    ->update([
                        'u_id_helper_qc' => Auth::user()->id,
                        'u_id_instock' => Auth::user()->id,
                        'qc_status' => ProductLocationSetupTransaction::QC_STATUS_FAILED,
                        'plst_type' => 'IN',
                        'plst_status' => 'INSTOCK',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'done_qc_time' => date('Y-m-d H:i:s'),
                        'in_stock_time' => date('Y-m-d H:i:s'),
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

                // find pls_id for default failed QC bin

                $pls_default_failed_qc = DB::table('product_location_setups')
                    ->where('pl_id', $pl_default_failed_qc->first()->id)
                    ->where('pst_id', $plst->pst_id)
                    ->first();

                // jika tidak ada, buat baru
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

                //tambahkan qty pada bin default failed qc

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

                OnlineTransactions::where('id', $to_id)
                    ->update(['internal_order_status' => 'UNDER REVIEW', 'updated_at' => date('Y-m-d H:i:s')]);


                if ($update_plst) {
                    DB::commit();
                    return response()->json(['status' => '200', 'message' => 'Item gagal melewati QC.']);
                } else {
                    DB::rollBack();
                    return response()->json(['status' => '400', 'message' => 'Gagal memperbarui status QC.']);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => '400', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function cancelPickItem($plst_id)
    {

        $check = ProductLocationSetupTransaction::where('id', $plst_id)
            ->whereIn('plst_status', ['WAITING ONLINE'])
            ->whereNull('pls_id')
            ->first();

        if (!$check) {
            return response()->json(['status' => '400', 'message' => 'Transaksi tidak ditemukan atau tidak dapat dibatalkan.']);
        }

        $update = ProductLocationSetupTransaction::where('id', $plst_id)
            ->update([
                'plst_type' => 'IN',
                'plst_status' => 'INSTOCK',
                'cancel_pickup_time' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        // if it is the only item in the transaction, update the transaction status back to previous status
        $otd_id = $check->otd_id;
        $to_id = OnlineTransactionDetails::where('id', $otd_id)->value('to_id');

        $other_picks = ProductLocationSetupTransaction::where('otd_id', $otd_id)
            ->where('id', '!=', $plst_id)
            ->whereIn('plst_status', ['WAITING ONLINE', 'WAITING RECEIPT', 'WAITING PACKING', 'DONE ONLINE'])
            ->exists();

        if (!$other_picks) {
            OnlineTransactions::where('id', $to_id)
                ->update(['internal_order_status' => 'NEW TRX', 'updated_at' => date('Y-m-d H:i:s')]);
        }

        if ($update) {
            return response()->json(['status' => '200', 'message' => 'Pengambilan item berhasil dibatalkan.']);
        } else {
            return response()->json(['status' => '400', 'message' => 'Gagal membatalkan pengambilan item.']);
        }
    }

    public function waitingReceipt(Request $request)
    {
        $ot_id = $request->get('ot_id');

        $transactions = OnlineTransactions::where('id', $ot_id)->first();

        $trx_status = $transactions->internal_order_status;

        $print_nota = $transactions->online_print;

        $print_resi = $transactions->print_resi;

        $data = DB::table('product_location_setup_transactions')
            ->join('online_transaction_details', 'product_location_setup_transactions.otd_id', '=', 'online_transaction_details.id')
            ->join('online_transactions', 'online_transaction_details.to_id', '=', 'online_transactions.id')
            ->join('product_stocks', 'product_location_setup_transactions.pst_id', '=', 'product_stocks.id')
            ->join('products', 'product_stocks.p_id', '=', 'products.id')
            ->join('sizes', 'product_stocks.sz_id', '=', 'sizes.id')
            ->join('brands', 'products.br_id', '=', 'brands.id')
            ->select(
                'online_transactions.order_number',
                'product_stocks.ps_barcode as sku',
                'products.p_name',
                'products.p_color',
                'sizes.sz_name',
                'brands.br_name',
                'online_transaction_details.qty as qty',
                DB::raw('SUM(ts_product_location_setup_transactions.plst_qty) as plst_qty'),
                'product_stocks.ps_purchase_price as cogs',
                'product_stocks.ps_price_tag as jez_price',
                'online_transaction_details.original_price as platform_price',
                'online_transaction_details.total_discount as seller_discount',
                'online_transaction_details.price_after_discount'
            )
            ->where('online_transactions.id', $ot_id)
            ->whereIn('product_location_setup_transactions.plst_status', ['WAITING RECEIPT', 'WAITING PACKING', 'DONE ONLINE', 'DONE'])
            ->groupBy(
                'online_transaction_details.id',
                'product_stocks.ps_barcode',
                'products.p_name',
                'products.p_color',
                'sizes.sz_name',
                'brands.br_name',
                'online_transaction_details.qty',
                'product_stocks.ps_purchase_price',
                'product_stocks.ps_price_tag',
                'online_transaction_details.original_price',
                'online_transaction_details.total_discount',
                'online_transaction_details.price_after_discount',
                'online_transactions.order_number'
            )
            ->get()
            ->map(function ($item) {
                $item->final_price = $item->qty * $item->price_after_discount;
                return $item;
            });

        $data = [
            'transactions' => $data,
            'trx_status' => $trx_status,
            'print_nota' => $print_nota,
            'print_resi' => $print_resi,
        ];

        return response()->json(['status' => '200', 'data' => $data]);
    }

    public function printResi($to_id)
    {
        $order_number = OnlineTransactions::where('id', $to_id)->value('order_number');

        $pdf_path = public_path('storage/split_resi/' . $order_number . '.pdf');

        if (!file_exists($pdf_path)) {
            return response()->json([
                'status' => '400',
                'message' => 'Resi Tidak Ditemukan'
            ]);
        }

        $update_print_resi = OnlineTransactions::where('id', $to_id)
            ->update([
                'print_resi' => 1,
                'u_print_resi' => Auth::user()->id,
                'time_print_resi' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if (!$update_print_resi) {
            return response()->json([
                'status' => '400',
                'message' => 'Gagal memperbarui status cetak resi.'
            ]);
        }

        return response()->json([
            'status' => '200',
            'pdf_url' => asset('storage/split_resi/' . $order_number . '.pdf')
        ]);
    }

    public function printInvoice(Request $request, $to_id)
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

                        $cek_keep_online = ProductLocationSetupTransaction::join('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                            ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                            ->where('product_stocks.ps_barcode', '=', $data->sku)
                            ->where('st_id', '=', $st_id)
                            ->whereNull('pt_id')
                            ->where('plst_status', '=', 'WAITING ONLINE')
                            ->count();

                        $data_keep_online = ProductLocationSetupTransaction::select('product_location_setup_transactions.id as plst_id')
                            ->join('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                            ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                            ->where('product_stocks.ps_barcode', '=', $data->sku)
                            ->where('st_id', '=', $st_id)
                            ->whereNull('pt_id')
                            ->where('plst_status', '=', 'WAITING ONLINE')
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
                            $trx_id_new = $pos_transaction_check->id;
                        } else {
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

                            $updateResult = ProductLocationSetupTransaction::where('id', $cko->plst_id)->update($paramsPlst);
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
                ->where(['to_id' => $trx->id])->get();


            if (!empty($check_transaction_detail)) {
                $trx->subitem = $check_transaction_detail;
                array_push($get_invoice, $trx);
            }
        }

        $stores = Auth::user()->st_id;

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

    public function donePrint($to_id)
    {
        DB::beginTransaction();
        try {
            $transaction = OnlineTransactions::where('id', $to_id)->where('internal_order_status', 'WAITING RECEIPT')->first();

            if (!$transaction) {
                return response()->json(['status' => '400', 'message' => 'Transaksi tidak ditemukan.']);
            }

            //check already printed
            if (!$transaction->online_print || !$transaction->online_print) {
                return response()->json(['status' => '403', 'message' => 'Transaksi belum dicetak.']);
            }

            $transaction_items = OnlineTransactionDetails::where('to_id', $to_id)->get();

            if ($transaction_items->isEmpty()) {
                return response()->json(['status' => '400', 'message' => 'Tidak ada item dalam transaksi ini.']);
            }

            $update_trx = OnlineTransactions::where('id', $to_id)
                ->update(['internal_order_status' => 'WAITING PACKING', 'updated_at' => date('Y-m-d H:i:s')]);

            if ($update_trx === 0) {
                DB::rollBack();
                return response()->json(['status' => '400', 'message' => 'Gagal memperbarui status transaksi.']);
            }

            $item_ids = [];

            foreach ($transaction_items as $item) {
                $item_ids[] = $item->id;
            }

            $update_plst = DB::table('product_location_setup_transactions')
                ->whereIn('otd_id', $item_ids)
                ->where('plst_status', 'WAITING RECEIPT')
                ->update(['plst_status' => 'WAITING PACKING', 'updated_at' => date('Y-m-d H:i:s')]);

            if ($update_plst === 0) {
                DB::rollBack();
                return response()->json(['status' => '400', 'message' => 'Gagal memperbarui status item transaksi.']);
            }

            DB::commit();
            return response()->json(['status' => '200', 'message' => 'Status berhasil diperbarui ke WAITING PACKING.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => '400', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function scanPackingSingle(Request $request)
    {
        $to_id = $request->get('to_id');

        DB::beginTransaction();
        try {
            $transaction = OnlineTransactions::where('id', $to_id)->first();

            if (!$transaction) {
                return response()->json(['status' => '400', 'message' => 'Transaksi tidak ditemukan.']);
            }

            $transaction_items = OnlineTransactionDetails::where('to_id', $to_id)->get();

            if ($transaction_items->isEmpty()) {
                return response()->json(['status' => '400', 'message' => 'Tidak ada item dalam transaksi ini.']);
            }

            $update_trx = OnlineTransactions::where('id', $to_id)
                ->update(['internal_order_status' => 'DONE ONLINE', 'updated_at' => date('Y-m-d H:i:s')]);

            if ($update_trx === 0) {
                DB::rollBack();
                return response()->json(['status' => '400', 'message' => 'Gagal memperbarui status transaksi.']);
            }

            $item_ids = [];

            foreach ($transaction_items as $item) {
                $item_ids[] = $item->id;
            }

            $update_plst = DB::table('product_location_setup_transactions')
                ->whereIn('otd_id', $item_ids)
                ->where('plst_status', 'WAITING PACKING')
                ->update([
                    'u_id_packer' => Auth::user()->id,
                    'plst_status' => 'DONE ONLINE',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'packed_time' => date('Y-m-d H:i:s'),
                ]);

            if ($update_plst === 0) {
                DB::rollBack();
                return response()->json(['status' => '400', 'message' => 'Gagal memperbarui status item transaksi.']);
            }

            DB::commit();
            return response()->json(['status' => '200', 'message' => 'Transaksi dan item berhasil diperbarui ke DONE ONLINE.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => '400', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function printManifest()
    {
        $manifest_number = 'MN123456789';
        $manifest_date = date('Y-m-d');

        $expedition_name = 'JNE';
        $courier_name = 'John Doe';
        $courier_phone = '08123456789';

        $pickup_address = 'Jl. Example No.123, Jakarta, Indonesia';
        $store_name = 'Jez Store';
        $pic_seller = 'Jane Smith';
        $pic_phone = '08987654321';

        $items = [
            ['resi' => 'RESI001', 'marketplace' => 'Tokopedia', 'qty' => '2', 'city' => 'Jakarta', 'notes' => ''],
            ['resi' => 'RESI001', 'marketplace' => 'Tokopedia', 'qty' => '2', 'city' => 'Jakarta', 'notes' => ''],
            ['resi' => 'RESI001', 'marketplace' => 'Tokopedia', 'qty' => '2', 'city' => 'Jakarta', 'notes' => ''],
            ['resi' => 'RESI001', 'marketplace' => 'Tokopedia', 'qty' => '2', 'city' => 'Jakarta', 'notes' => ''],
            ['resi' => 'RESI001', 'marketplace' => 'Tokopedia', 'qty' => '2', 'city' => 'Jakarta', 'notes' => ''],
        ];

        $total_items = count($items);

        return view('app.helper_online.print_manifest', compact(
            'manifest_number',
            'manifest_date',
            'expedition_name',
            'courier_name',
            'courier_phone',
            'pickup_address',
            'store_name',
            'pic_seller',
            'pic_phone',
            'items',
            'total_items'
        ));
    }
}
