<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\ExceptionLocation;
use App\Imports\MassImport;
use App\Exports\MassExport;
use App\Exports\MassExportByDate;
use App\Exports\MassResult;
use Maatwebsite\Excel\Facades\Excel;

class CycleCountController extends Controller
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
            'st_id' => DB::table('stores')->where('st_delete', '!=', '1')->orderBy('st_name')->pluck('st_name', 'id'),
            'psc_id' => DB::table('product_sub_categories')->where('psc_delete', '!=', '1')->orderBy('psc_name')->pluck('psc_name', 'id'),
            'br_id' => DB::table('brands')->where('br_delete', '!=', '1')->orderBy('br_name')->pluck('br_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.cycle_counts.cycle_count', compact('data'));
    }

    public function indexUpdated()
    {
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => 'cycle_counts'
            ])->exists();
        if (!$validate) {
            dd("Anda tidak memiliki akses ke menu ini, hubungi Administrator");
        }
        
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        $data = [
            'title' => $title,
            'subtitle' => 'Cycle Counts',
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'st_id' => DB::table('stores')->where('st_delete', '!=', '1')->orderBy('st_name')->pluck('st_name', 'id'),
            'psc_id' => DB::table('product_sub_categories')->where('psc_delete', '!=', '1')->orderBy('psc_name')->pluck('psc_name', 'id'),
            'br_id' => DB::table('brands')->where('br_delete', '!=', '1')->orderBy('br_name')->pluck('br_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.updated_cycle_counts.cycle_count', compact('data'));
    }

    public function getItemDetails(Request $request)
    {
        $plsId = $request->input('pls_id');
        $sku = $request->input('sku');

        $search = DB::table('product_location_setups as T1')
            ->leftJoin('product_locations as T2', 'T2.id', '=', 'T1.pl_id')
            ->leftJoin('product_stocks as T3', 'T3.id', '=', 'T1.pst_id')
            ->leftJoin('sizes as T4', 'T4.id', '=', 'T3.sz_id')
            ->leftJoin('products as T5', 'T5.id', '=', 'T3.p_id')
            ->where('T3.ps_barcode', $sku)
            ->where('T1.pl_id', $plsId)
            ->select('T5.p_name', 'T4.sz_name', 'T2.pl_code', 'T1.pls_qty')
            ->first();

        if ($search) {
            $data = $search;
        } else {
            $data = DB::table('product_stocks as T1')
                ->leftJoin('products as T2', 'T2.id', '=', 'T1.p_id')
                ->leftJoin('sizes as T3', 'T3.id', '=', 'T1.sz_id')
                ->where('T1.ps_barcode', $sku)
                ->select('T2.p_name', 'T3.sz_name')
                ->first();
        }


//        dd($data);

        if ($data) {
            return response()->json(['status' => '200', 'data' => $data]);
        } else {
            return response()->json(['status' => '404', 'message' => 'Item tidak ditemukan']);
        }

    }


    public function createCycleCount(Request $request){
        $ccn_number = $request->input('ccn_number');
        $st_id = $request->input('st_id');
        $pl_id = $request->input('pl_id');
        
        try {
            // Check if cycle count header already exists
            $existing = DB::table('cycle_counts')->where('ccn_number', $ccn_number)->first();
            
            if (!$existing) {
                // Insert new cycle count header
                $cycleCountId = DB::table('cycle_counts')->insertGetId([
                    'ccn_number' => $ccn_number,
                    'st_id' => $st_id,
                    'pl_id' => !empty($pl_id) ? (is_array($pl_id) ? json_encode($pl_id) : $pl_id) : null,
                    'u_id' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                return response()->json([
                    'status' => '200',
                    'data' => [
                        'ccn_number' => $ccn_number,
                        'id' => $cycleCountId
                    ],
                    'message' => 'Cycle count header berhasil dibuat'
                ]);
            } else {
                return response()->json([
                    'status' => '200',
                    'data' => [
                        'ccn_number' => $ccn_number,
                        'id' => $existing->id
                    ],
                    'message' => 'Cycle count header sudah ada'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => '500',
                'message' => 'Gagal membuat cycle count header: ' . $e->getMessage()
            ]);
        }
    }

    public function storeCycleCountDetail(Request $request)
    {
        $ccn_number = $request->input('ccn_number');
        $pl_id = $request->input('pls_id'); // Note: this is actually pl_id from bin_filter, not pls_id
        $sku = $request->input('sku');
        
        try {
            // Get cycle count header
            $cycleCount = DB::table('cycle_counts')->where('ccn_number', $ccn_number)->first();
            
            if (!$cycleCount) {
                return response()->json([
                    'status' => '404',
                    'message' => 'Cycle count header tidak ditemukan'
                ]);
            }
            
            // Get product location setup data - use pl_id to find pls_id
            $pls = DB::table('product_location_setups as T1')
                ->leftJoin('product_locations as T2', 'T2.id', '=', 'T1.pl_id')
                ->leftJoin('product_stocks as T3', 'T3.id', '=', 'T1.pst_id')
                ->leftJoin('products as T5', 'T5.id', '=', 'T3.p_id')
                ->where('T3.ps_barcode', $sku)
                ->where('T1.pl_id', $pl_id) // Use pl_id from bin_filter
                ->select('T1.id as pls_id', 'T1.pls_qty', 'T3.ps_barcode', 'T5.id as p_id')
                ->first();
            
            if (!$pls) {
                return response()->json([
                    'status' => '404',
                    'message' => 'Item tidak ditemukan'
                ]);
            }
            
            // Check if detail already exists
            $existing = DB::table('cycle_count_details')
                ->where('ccn_id', $cycleCount->id)
                ->where('pls_id', $pls->pls_id)
                ->where('ps_barcode', $sku)
                ->first();
            
            if ($existing) {
                return response()->json([
                    'status' => '200',
                    'message' => 'Item sudah ada dalam cycle count detail'
                ]);
            }
            
            // Insert cycle count detail
            DB::table('cycle_count_details')->insert([
                'ccn_id' => $cycleCount->id,
                'pls_id' => $pls->pls_id,
                'ps_barcode' => $sku,
                'qty_system' => $pls->pls_qty,
                'qty_count' => $pls->pls_qty,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $this->UserActivity('Menambahkan item ke cycle count detail: ' . $sku);
            
            return response()->json([
                'status' => '200',
                'message' => 'Item berhasil disimpan ke cycle_count_details'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => '500',
                'message' => 'Gagal menyimpan item: ' . $e->getMessage()
            ]);
        }
    }

    public function stockDatatables(Request $request)
    {
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();
        
        // Check if this is for Simple-DataTables (client-side) by checking if length=-1
        if ($request->has('length') && $request->get('length') == -1) {
            // Return JSON for Simple-DataTables
            $query = DB::table('product_location_setups')
                ->selectRaw("ts_product_location_setups.id as id, pl_code, br_name, p_name, p_color, sz_name, psc_name,
                pls_qty, avg(ts_purchase_order_article_details.poad_purchase_price) as purchase_2, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase_1, ps_purchase_price as purchase_3, ps_sell_price, p_sell_price, ps_purchase_price, p_purchase_price")
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
                ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(function ($w) use ($exception, $request) {
                    $st_id = $request->get('st_id');
                    $psc_id = $request->get('psc_id');
                    $br_id = $request->get('br_id');
                    $pl_id = $request->get('pl_id');
                    $qty_filter = $request->get('qty_filter');
                    $w->whereNotIn('product_locations.pl_code', $exception);
                    if ($st_id && $st_id != 'all') {
                        $w->where('product_locations.st_id', $st_id);
                    }
                    if ($psc_id && $psc_id != 'all') {
                        $w->where('products.psc_id', $psc_id);
                    }
                    if ($br_id && $br_id != 'all') {
                        $w->where('products.br_id', $br_id);
                    }
                    if (!empty($pl_id) && is_array($pl_id)) {
                        $w->whereIn('product_locations.id', $pl_id);
                    } elseif (!empty($pl_id)) {
                        $w->where('product_locations.id', $pl_id);
                    }
                    if ($qty_filter == '1') {
                        $w->where('product_location_setups.pls_qty', '>', '0');
                    }
                });
            
            // Apply search filter
            if (!empty($request->get('search'))) {
                $search = $request->get('search');
                $query->where(function ($w) use ($search) {
                    $w->orWhere('pl_code', 'LIKE', "%$search%")
                      ->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', ["%$search%"]);
                });
            }
            
            $data = $query->groupBy('product_location_setups.id')->get();
            
            // Format data for Simple-DataTables
            $formattedData = $data->map(function ($row) {
                $purchase = $row->ps_purchase_price ?? 0;
                $sell = !empty($row->ps_sell_price) ? $row->ps_sell_price : ($row->p_sell_price ?? 0);
                
                return [
                    'id' => $row->id,
                    'pl_code' => $row->pl_code,
                    'br_name' => $row->br_name,
                    'p_name' => $row->p_name,
                    'p_color' => $row->p_color,
                    'sz_name' => $row->sz_name,
                    'psc_name' => $row->psc_name,
                    'pls_qty' => $row->pls_qty,
                    'purchase' => number_format($purchase),
                    'sell' => number_format($sell),
                ];
            });
            
            return response()->json(['data' => $formattedData]);
        }
        
        // Original DataTables server-side format
        if (request()->ajax()) {
            return datatables()->of(DB::table('product_location_setups')->selectRaw("ts_product_location_setups.id as id, pl_code, br_name, p_name, p_color, sz_name, psc_name,
            pls_qty, avg(ts_purchase_order_article_details.poad_purchase_price) as purchase_2, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase_1, ps_purchase_price as purchase_3, ps_sell_price, p_sell_price, ps_purchase_price, p_purchase_price")
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
                ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where(function ($w) use ($exception, $request) {
                    $st_id = $request->get('st_id');
                    $psc_id = $request->get('psc_id');
                    $br_id = $request->get('br_id');
                    $pl_id = $request->get('pl_id');
                    $qty_filter = $request->get('qty_filter');
                    $w->whereNotIn('product_locations.pl_code', $exception);
                    if ($st_id != 'all') {
                        $w->where('product_locations.st_id', $st_id);
                    }
                    if ($psc_id != 'all') {
                        $w->where('products.psc_id', $psc_id);
                    }
                    if ($br_id != 'all') {
                        $w->where('products.br_id', $br_id);
                    }
                    if (!empty($pl_id)) {
                        $w->whereIn('product_locations.id', $pl_id);
                    }
                    if ($qty_filter == '1') {
                        $w->where('product_location_setups.pls_qty', '>', '0');
                    }
                })
                ->groupBy('product_location_setups.id'))
                ->editColumn('purchase', function ($data) {
                    return number_format($data->ps_purchase_price);
                })
                ->editColumn('sell', function ($data) {
                    if (!empty($data->ps_sell_price)) {
                        return number_format($data->ps_sell_price);
                    } else {
                        return number_format($data->p_sell_price);
                    }
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('pl_code', 'LIKE', "%$search%")
                                ->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function adjustmentDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(DB::table('mass_adjustments')->select('mass_adjustments.id as id', 'ma_code', 'ma_approve', 'ma_editor', 'ma_executor', 'ma_status', 'ma_approve_time','ma_executor_time','st_name','st_code', 'u_name', 'mass_adjustments.created_at', 'mass_adjustments.updated_at', 'mass_adjustments.note_adjustment as note', 'mass_adjustments.tipe_adjustment as tipe', 'product_stocks.ps_barcode')
                ->leftJoin('stores', 'stores.id', '=', 'mass_adjustments.st_id')
                ->leftJoin('users', 'users.id', '=', 'mass_adjustments.u_id')
                ->leftJoin('mass_adjustment_details', 'mass_adjustment_details.ma_id', '=', 'mass_adjustments.id')
                ->leftJoin('product_location_setups', 'mass_adjustment_details.pls_id', '=', 'product_location_setups.id')
                ->leftJoin('product_stocks', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                ->groupBy('mass_adjustments.id')
                ->where(function ($query) use ($request) {
                    if ($request->has('st_id') && !empty($request->get('st_id')) && $request->get('st_id') != 'all') {
                        $query->where('mass_adjustments.st_id', '=', $request->get('st_id'));
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->has('filter') && $request->get('filter') !== null) {
                        $query->where('mass_adjustments.ma_status', '=', $request->get('filter'));
                    }
                    if ($request->has('note_adjustment') && $request->get('note_adjustment') !== null) {
                        $query->where('mass_adjustments.note_adjustment', 'LIKE', '%' . $request->get('note_adjustment') . '%');
                    }
                })
                )
                
                ->editColumn('ma_code_show', function ($d) {
                    return "<a class='btn btn-primary' id='madj_btn' data-id='" . $d->id . "'>" . $d->ma_code . "</a>";
                })
                ->editColumn('approve', function ($d) {
                    if (!empty($d->ma_approve)) {
                        return DB::table('users')->where('id', '=', $d->ma_approve)->first()->u_name;
                    } else {
                        return 'Menunggu Approval';
                    }
                })
                ->editColumn('editor', function ($d) {
                    if (!empty($d->ma_editor)) {
                        return DB::table('users')->where('id', '=', $d->ma_editor)->first()->u_name;
                    } else {
                        return '-';
                    }
                })
                ->editColumn('executor', function ($d) {
                    if (!empty($d->ma_executor)) {
                        return DB::table('users')->where('id', '=', $d->ma_executor)->first()->u_name;
                    } else {
                        return '-';
                    }
                })
                ->editColumn('created_at', function ($d) {
                    return date('d/m/Y H:i:s', strtotime($d->created_at));
                })
                ->editColumn('updated_at', function ($d) {
                    return date('d/m/Y H:i:s', strtotime($d->updated_at));
                })
                ->editColumn('ma_status', function ($d) {
                    if ($d->ma_status == '0') {
                        return 'Menunggu Eksekusi';
                    } else If($d->ma_status == '2'){
                        return 'Cancel';
                    } else {
                        return 'Selesai';
                    }
                })
                ->editColumn('action', function ($d) {
                    if ($d->ma_status == '0') {
                        return "<a class='btn btn-success' id='btn_cancel' data-id='" . $d->id . "'>Batalkan</a>";
                    } else if($d->ma_status == '2'){
                        return "<a class='btn btn-primary'>Cancel</a>";
                    } else {
                        return "<a class='btn btn-danger'>Done</a>";
                    }
                })
                ->editColumn('note', function ($d) {
                    $aliases = [
                        'MALANG' => 'MLG',
                        'SURABAYA' => 'SBY',
                        'KEDIRI' => 'KDR',
                        'JEMBER' => 'JBR',
                        'SIDOARJO' => 'SDA'
                    ];
                    $st_code_alias = $aliases[$d->st_code] ?? $d->st_code;
                    return $st_code_alias . ' - ' . ($d->note ?? '-');
                })
                ->rawColumns(['ma_code_show', 'action', 'note'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('ma_code', 'LIKE', "%$search%")
                              ->orWhere('u_name', 'LIKE', "%$search%")
                              ->orWhere('note_adjustment', 'LIKE', "%$search%")
                              ->orWhere('ps_barcode', 'LIKE', "%$search%");
                        });
                    }
                    if (!empty($request->get('filter'))) {
                        $instance->where(function ($w) use ($request) {
                            $filter = $request->get('filter');
                            $w->orWhere('ma_status', '=', "$filter");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function adjustmentDetailDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(
                DB::table('mass_adjustment_details')
                    ->selectRaw("ts_mass_adjustment_details.id as id, ts_mass_adjustment_details.created_at as adjustment_date,br_name, psc_name, p_name, p_color, sz_name, pl_code, qty_export, qty_so, mad_type, mad_diff,
            avg(ts_purchase_order_article_details.poad_purchase_price) as purchase_2, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase_1, ps_sell_price, p_sell_price, ps_purchase_price, p_purchase_price, ps_barcode")
                    ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'mass_adjustment_details.pls_id')
                    ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
                    ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                    ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                    ->where('mass_adjustment_details.ma_id', '=', $request->get('ma_id'))
                    ->groupBy('mass_adjustment_details.id')
            )
                ->editColumn('purchase', function ($data) {
                    if (!empty($data->purchase_1)) {
                        return number_format($data->purchase_1);
                    } else if (!empty($data->purchase_2)) {
                        return number_format($data->purchase_2);
                    } else if (!empty($data->ps_purchase_price)) {
                        return number_format($data->ps_purchase_price);
                    } else {
                        return number_format($data->p_purchase_price);
                    }
                })
                ->editColumn('sell', function ($data) {
                    if (!empty($data->ps_sell_price)) {
                        return number_format($data->ps_sell_price);
                    } else {
                        return number_format($data->p_sell_price);
                    }
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('pl_code', 'LIKE', "%$search%")
                                ->orWhere('ps_barcode', 'LIKE', "%$search%")
                                ->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', ["%$search%"]);
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function loadAsset(Request $req)
    {
        $st_id = $req->post('st_id');
        $psc_id = $req->post('psc_id');
        $br_id = $req->post('br_id');
        $pl_id = $req->post('pl_id');
        $qty_filter = $req->post('qty_filter');

        $cc_qty = null;
        $c_qty = null;
        $cc_value = null;
        $c_value = null;

        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        $asset_cc = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, p_name, p_color, sz_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereNotIn('pl_code', $exception)
            ->where(function ($w) use ($st_id, $psc_id, $br_id, $pl_id, $qty_filter) {
                if ($st_id != 'all') {
                    $w->where('product_locations.st_id', '=', $st_id);
                }
                if ($psc_id != 'all') {
                    $w->where('products.psc_id', '=', $psc_id);
                }
                if ($br_id != 'all') {
                    $w->where('products.br_id', '=', $br_id);
                }
                if (!empty($pl_id)) {
                    $w->whereIn('product_locations.id', $pl_id);
                }
                if ($qty_filter == '1') {
                    $w->where('product_location_setups.pls_qty', '>', '0');
                }
            })
            ->where('product_location_setups.pls_qty', '>', '0')
            ->whereIn('stkt_id', ['1', '3'])
            ->groupBy('product_location_setups.id')
            ->get();
        if (!empty($asset_cc->first())) {
            foreach ($asset_cc as $row) {
                $purchase = 0;
                if (!empty($row->purchase)) {
                    $purchase = round($row->purchase);
                } else {
                    if (!empty($row->ps_purchase_price)) {
                        $purchase = $row->ps_purchase_price;
                    } else {
                        $purchase = $row->p_purchase_price;
                    }
                }
                $cc_qty += $row->qty;
                $cc_value += ($row->qty * $purchase);
            }
        }

        $asset_c = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, p_name, p_color, sz_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereNotIn('pl_code', $exception)
            ->where(function ($w) use ($st_id, $psc_id, $br_id, $pl_id, $qty_filter) {
                if ($st_id != 'all') {
                    $w->where('product_locations.st_id', '=', $st_id);
                }
                if ($psc_id != 'all') {
                    $w->where('products.psc_id', '=', $psc_id);
                }
                if ($br_id != 'all') {
                    $w->where('products.br_id', '=', $br_id);
                }
                if (!empty($pl_id)) {
                    $w->whereIn('product_locations.id', $pl_id);
                }
                if ($qty_filter == '1') {
                    $w->where('product_location_setups.pls_qty', '>', '0');
                }
            })
            ->where('product_location_setups.pls_qty', '>', '0')
            ->where('stkt_id', '=', '2')
            ->groupBy('product_location_setups.id')
            ->get();
        if (!empty($asset_c->first())) {
            foreach ($asset_c as $row) {
                $purchase = 0;
                if (!empty($row->purchase)) {
                    $purchase = round($row->purchase);
                } else {
                    if (!empty($row->ps_purchase_price)) {
                        $purchase = $row->ps_purchase_price;
                    } else {
                        $purchase = $row->p_purchase_price;
                    }
                }
                $c_qty += $row->qty;
                $c_value += ($row->qty * $purchase);
            }
        }

        $r['status'] = '200';
        $r['cc_qty'] = number_format($cc_qty);
        $r['c_qty'] = number_format($c_qty);
        $r['cc_value'] = number_format($cc_value);
        $r['c_value'] = number_format($c_value);
        return json_encode($r);
    }

    public function loadLocation(Request $req)
    {
        $st_id = $req->post('st_id');
        $pl_id = $req->post('pl_id');
        $data = DB::table('product_locations')->where('st_id', '=', $st_id)
            ->where('pl_delete', '!=', '1')
            ->where(function ($w) use ($pl_id) {
                if (!empty($pl_id)) {
                    $w->whereNotIn('id', $pl_id);
                }
            })->orderBy('pl_code')->pluck('pl_code', 'id');

        return response()->json([
            'status' => '200',
            'data' => $data
        ]);
    }

    public function exportData(Request $req)
    {
        $st_id = $req->get('st_id');
        $psc_id = $req->get('psc_id');
        $br_id = $req->get('br_id');
        $pl_id = $req->get('pl_id');
        $qty_filter = $req->get('qty_filter');
        return Excel::download(new MassExport($st_id, $psc_id, $br_id, $pl_id, $qty_filter), 'mass_adjustment_template.xlsx');
    }

    public function exportResult(Request $req)
    {
        $ma_id = $req->post('ma_id');
        return Excel::download(new MassResult($ma_id), 'mass_adjustment_results.xlsx');
    }

    public function importData(Request $req)
    {
        $st_id = $req->post('st_id');
        $psc_id = $req->post('psc_id');
        $br_id = $req->post('br_id');
        $pl_id = $req->post('pl_id');
        $qty_filter = $req->post('qty_filter');
        $note = $req->post('note_adjustment');
        $tipe = $req->post('tipe_adjustment');


        if (request()->hasFile('template')) {
            try {
                $import = new MassImport($st_id, $psc_id, $br_id, $pl_id, $qty_filter, $note, $tipe);
                Excel::import($import, request()->file('template'));;
                if (!empty($import->invalidPlsIds)) {
                    // Kalau ada data tidak valid
                    $r['status'] = '500';
                    $r['invalid_skus'] = $import->invalidPlsIds;
                } else {
                    // Kalau tidak ada error, sukses
                    $r['status'] = '200';
                    $r['ma_id'] = $import->getRowCount()['ma_id'];
                    $r['ma_code'] = $import->getRowCount()['ma_code'];
                }
            } catch (\Exception $e) {
                $r['status'] = '500';
                $r['message'] = $e->getMessage();
            }
        } else {
            $r['status'] = '500';
        }
        return json_encode($r);
    }

    public function loadApproval(Request $req)
    {
        $ma_id = $req->post('ma_id');
        $get = DB::table('mass_adjustments')->where('id', '=', $ma_id)->first();
        if (!empty($get)) {
            $approval = '';
            $approval_label = '';
            if (!empty($get->ma_approve)) {
                $approval = $get->ma_approve;
                $approval_label = DB::table('users')->select('u_name')->where('id', '=', $approval)->first()->u_name;
            }
            $r['approval'] = $approval;
            $r['approval_label'] = $approval_label;
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function approvalData(Request $req)
    {
        $ma_id = $req->post('ma_id');
        $update = DB::table('mass_adjustments')->where('id', '=', $ma_id)->update([
            'ma_approve' => Auth::user()->id,
            'ma_approve_time' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function cancelAdjustment(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'ma_id' => 'required|integer|exists:mass_adjustments,id',
        ]);

        try {
            // Find the record by ID
             DB::table('mass_adjustments')->where('id', '=', $request->ma_id)->update(['ma_status' => 2]);

            // Return success response
            return response()->json([
                'status' => '200',
                'message' => 'Data berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            // Log the error if needed
            \Log::error('Error deleting mass adjustment: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'status' => '500',
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ]);
        }
    }

    public function execData(Request $req)
    {
        $ma_id = $req->post('ma_id');

        $data = DB::table('mass_adjustment_details')
        ->join('product_location_setups', 'product_location_setups.id', '=', 'mass_adjustment_details.pls_id')
        ->where('ma_id', '=', $ma_id);

        $differences = [];

        foreach ($data->get() as $row) {
            $productLocation = DB::table('product_location_setups')
            ->select('pls_qty', 'pst_id','ps_barcode')
            ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->where('product_location_setups.id', '=', $row->pls_id)
            ->first();

            if ($productLocation && $row->qty_export != $productLocation->pls_qty) {

            $differences[] = [
                'sku' => $productLocation->ps_barcode,
                'qty_export' => $row->qty_export,
                'pls_qty' => $productLocation->pls_qty,
            ];
            }
        }

        if (!empty($differences)) {
            $r['status'] = '500';
            $r['differences'] = $differences;
            return json_encode($r);
        }

        $check = DB::table('mass_adjustments')->where('id', '=', $ma_id)
            ->whereNotNull('ma_approve')
            ->where('ma_status', '=', '0')->exists();
        if (!$check) {
            $r['status'] = '400';
            return json_encode($r);
        }
        $get = DB::table('mass_adjustment_details')->select('pls_id', 'qty_so')->where('ma_id', '=', $ma_id)->get();
        if (!empty($get->first())) {
            $update = DB::table('mass_adjustments')->where('id', '=', $ma_id)->update([
                'ma_executor' => Auth::user()->id,
                'ma_status' => '1',
                'ma_executor_time' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            foreach ($get as $row) {
                DB::table('product_location_setups')->where('id', '=', $row->pls_id)->update([
                    'pls_qty' => $row->qty_so
                ]);
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function exportMassByDate(Request $request)
    {
        $ma_date = $request->input('ma_date');
        $st_id = $request->input('st_id'); // Get the store ID from the request
        $filter = $request->input('filter'); // Get the filter from the request
        $data = array();
        $start = null;
        $end = null;
        $range = null;
        if (!empty($ma_date)) {
            $exp = explode('|', $ma_date);
            if (count($exp) > 1) {
                $start = $exp[0];
                $end = $exp[1];
                $range = 'true';
            } else {
                $start = $ma_date;
                $end = $ma_date;
                $range = 'false';
            }
        }
        $data = DB::table('mass_adjustment_details')
            ->selectRaw("ts_mass_adjustment_details.id as id, ts_mass_adjustment_details.created_at as adjustment_date, br_name, psc_name, p_name, p_color, sz_name, pl_code, qty_export, qty_so, mad_type, mad_diff,ts_mass_adjustments.ma_approve_time, ts_mass_adjustments.ma_executor_time,
        avg(ts_purchase_order_article_details.poad_purchase_price) as purchase_2, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase_1, ps_sell_price, p_sell_price, ps_purchase_price, p_purchase_price, ps_barcode, ts_mass_adjustments.ma_code, ts_mass_adjustments.note_adjustment as adjust_note, ts_mass_adjustments.tipe_adjustment as adjust_type, ts_mass_adjustments.ma_approve_time,ts_mass_adjustments.ma_executor_time,ts_stores.st_name, ts_stores.st_code")
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'mass_adjustment_details.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('mass_adjustments', 'mass_adjustments.id', '=', 'mass_adjustment_details.ma_id')
            ->leftJoin('stores', 'stores.id', '=', 'mass_adjustments.st_id')
            ->where(function ($w) use ($start, $end, $range) {
                if ($range == 'true') {
                    $w->whereDate('mass_adjustment_details.created_at', '>=', $start)
                        ->whereDate('mass_adjustment_details.created_at', '<=', $end);
                } else {
                    $w->whereDate('mass_adjustment_details.created_at', '=', $start);
                }
            })
            ->when(!empty($st_id) && $st_id != 'all', function ($query) use ($st_id) {
                $query->where('mass_adjustments.st_id', '=', $st_id); // Apply filter by st_id
            })
            ->when($filter !== null, function ($query) use ($request) {
                $query->where('mass_adjustments.ma_status', '=', $request->get('filter'));
            })
            ->orderBy('mass_adjustment_details.created_at', 'desc')
            ->groupBy('mass_adjustment_details.id')
            ->get()
            ->map(function ($data) {
                // Determine the purchase price by checking each field in order
                $purchasePrice = !empty($data->purchase_1) ? round($data->purchase_1, 2) : (!empty($data->purchase_2) ? round($data->purchase_2, 2) : (!empty($data->ps_purchase_price) ? $data->ps_purchase_price :
                    round($data->p_purchase_price, 2)));

                // Round the purchase price to two decimal places
                $roundedPurchasePrice = round($purchasePrice, 2);

                // Format the rounded purchase price with two decimal places, using '.' as the decimal separator and ',' as the thousands separator
                $data->purchase = number_format($roundedPurchasePrice, 2, '.', ',');

                // Determine the sell price, handling the case where ps_sell_price might be empty
                $data->sell = !empty($data->ps_sell_price) ? number_format($data->ps_sell_price) : number_format($data->p_sell_price);

                $aliases = [
                    'MALANG' => 'MLG',
                    'SURABAYA' => 'SBY',
                    'KEDIRI' => 'KDR',
                    'JEMBER' => 'JBR',
                    'SIDOARJO' => 'SDA'
                ];
                $st_code = $data->st_code ?? '';
                $alias = $aliases[$st_code] ?? $st_code;
                $data->adjust_note_formatted = $alias . ' - ' . ($data->adjust_note ?? '-');
            

                return $data;
            });
        $r['data'] = $data;
        return json_encode($r);
    }

    public function exportMassByDateExcel(Request $request)
    {
        $ma_date = $request->input('ma_date');
        $st_id = $request->input('st_id'); // Get the store ID from the request
        $filter = $request->input('filter'); // Get the filter from the request
        $start = null;
        $end = null;
        $range = null;

        if (!empty($ma_date)) {
            $exp = explode('|', $ma_date);
            if (count($exp) > 1) {
                $start = $exp[0];
                $end = $exp[1];
                $range = 'true';
            } else {
                $start = $ma_date;
                $end = $ma_date;
                $range = 'false';
            }
        }

        $data = DB::table('mass_adjustment_details')
            ->selectRaw("ts_mass_adjustment_details.id as id, ts_mass_adjustment_details.created_at as adjustment_date, br_name, psc_name, p_name, p_color, sz_name, pl_code, qty_export, qty_so, mad_type, mad_diff,ts_mass_adjustments.ma_approve_time, ts_mass_adjustments.ma_executor_time,
    avg(ts_purchase_order_article_details.poad_purchase_price) as purchase_2, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase_1, ps_sell_price, p_sell_price, ps_purchase_price, p_purchase_price, ps_barcode, ts_mass_adjustments.ma_code,ts_mass_adjustments.note_adjustment as adjust_note, ts_mass_adjustments.tipe_adjustment as adjust_type,ts_mass_adjustments.ma_approve_time,ts_mass_adjustments.ma_executor_time, ts_stores.st_name, ts_stores.st_code")
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'mass_adjustment_details.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('mass_adjustments', 'mass_adjustments.id', '=', 'mass_adjustment_details.ma_id')
            ->leftJoin('stores', 'stores.id', '=', 'mass_adjustments.st_id')
            ->where(function ($w) use ($start, $end, $range) {
                if ($range == 'true') {
                    $w->whereDate('mass_adjustment_details.created_at', '>=', $start)
                        ->whereDate('mass_adjustment_details.created_at', '<=', $end);
                } else {
                    $w->whereDate('mass_adjustment_details.created_at', '=', $start);
                }
            })
            ->when(!empty($st_id) && $st_id != 'all', function ($query) use ($st_id) {
                $query->where('mass_adjustments.st_id', '=', $st_id); // Apply filter by st_id
            })
            ->when($filter !== null, function ($query) use ($request) {
                $query->where('mass_adjustments.ma_status', '=', $request->get('filter'));
            })
            ->orderBy('mass_adjustment_details.updated_at', 'desc')
            ->groupBy('mass_adjustment_details.id')
            ->get()
            ->map(function ($data) {
                $purchasePrice = !empty($data->purchase_1) ? round($data->purchase_1) : (!empty($data->purchase_2) ? round($data->purchase_2) : (!empty($data->ps_purchase_price) ? round($data->ps_purchase_price) :
                    round($data->p_purchase_price)));

                $data->purchase = round($purchasePrice);
                $data->sell = !empty($data->ps_sell_price) ? round($data->ps_sell_price) : round($data->p_sell_price);

                $aliases = [
                    'MALANG' => 'MLG',
                    'SURABAYA' => 'SBY',
                    'KEDIRI' => 'KDR',
                    'JEMBER' => 'JBR',
                    'SIDOARJO' => 'SDA'
                ];
            
                $alias = $aliases[$data->st_code] ?? $data->st_code;
                $data->adjust_note = $alias . ' - ' . ($data->adjust_note ?? '-');

                return $data;
            });
        $fileName = 'mass_adjustment_by_date_' . ($start ?? 'unknown') . '_to_' . ($end ?? 'unknown') . '.xlsx';
        return Excel::download(new MassExportByDate($data), $fileName);
    }
}
