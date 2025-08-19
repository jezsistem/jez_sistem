<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductStock;
use App\Models\ProductLocation;
use App\Models\ProductLocationSetup;
use App\Models\BinAdjustment;
use App\Models\UserActivity;
use App\Models\Store;

class AdjustmentController extends Controller
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

    private $table_row = 0;
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
        $path = "
        <li class='breadcrumb-item'>
            <a href='' class='text-muted'>Stock</a>
        </li>
        <li class='breadcrumb-item'>
            <a href='' class='text-muted'>Adjustment</a>
        </li>";
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'path' => $path,
            'user' => $user_data,
            'segment' => request()->segment(1),
            'pst_id' => ProductStock::selectRaw('ts_product_stocks.id as pst_id, CONCAT("[",br_name,"] ", p_name," ",p_color," ",sz_name) as p_name')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->where('p_delete', '!=', '1')
                ->orderBy('p_name')->pluck('p_name', 'pst_id'),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'pl_id' => ProductLocation::selectRaw('ts_product_locations.id as id, CONCAT(pl_code," (",st_name,")") as location')
                ->join('stores', 'stores.id', '=', 'product_locations.st_id')
                ->where('pl_adjustment', '!=', '1')
                ->where('pl_delete', '!=', '1')
                ->orderByDesc('pl_code')->pluck('location', 'id')
        ];
        return view('app.adjustment.adjustment', compact('data'));
    }

    public function reloadLocation()
    {
        $data = [
            'pl_id' => ProductLocation::selectRaw('ts_product_locations.id as id, CONCAT(pl_code," (",st_name,")") as location')
                ->join('stores', 'stores.id', '=', 'product_locations.st_id')
                ->where('pl_adjustment', '!=', '1')
                ->where('pl_delete', '!=', '1')
                ->orderByDesc('pl_code')->pluck('location', 'id')
        ];
        return view('app.adjustment._reload_location', compact('data'));
    }

    public function adjustmentHistoryDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(BinAdjustment::select('bin_adjustments.id as ba_id', 'ps_barcode', 'pls_id', 'st_name', 'pl_code', 'u_name', 'ba_approve', 'ba_executor', 'br_name', 'p_name', 'p_color', 'sz_name', 'ba_code', 'ba_note', 'ba_old_qty', 'ba_new_qty', 'ba_adjust', 'ba_adjust_type', 'bin_adjustments.updated_at as ba_updated_at', 'ba_status')
                ->leftJoin('users', 'users.id', '=', 'bin_adjustments.u_id')
                ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'bin_adjustments.pls_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->orderBy('bin_adjustments.updated_at', 'desc'))
                ->editColumn('ba_approve', function ($data) {
                    if (!empty($data->ba_approve)) {
                        return DB::table('users')->where('id', '=', $data->ba_approve)->first()->u_name;
                    } else {
                        return '-';
                    }
                })
                ->editColumn('ba_executor', function ($data) {
                    if (!empty($data->ba_executor)) {
                        return DB::table('users')->where('id', '=', $data->ba_executor)->first()->u_name;
                    } else {
                        return '-';
                    }
                })
                ->editColumn('ba_status', function ($data) {
                    $value = $data->ba_status;
                    switch ($value) {
                        case BinAdjustment::UNKNOWN:
                            return '<span class="badge badge-secondary" value="' . $value . '">Unknown</span>';
                        case BinAdjustment::NEED_APPROVAL:
                            return '<span class="badge badge-warning" value="' . $value . '">Need Approval</span>';
                        case BinAdjustment::NEED_EXECUTION:
                            return '<span class="badge badge-info" value="' . $value . '">Need Execution</span>';
                        case BinAdjustment::CANCEL:
                            return '<span class="badge badge-danger" value="' . $value . '">Cancelled</span>';
                        case BinAdjustment::DONE:
                            return '<span class="badge badge-success" value="' . $value . '">Done</span>';
                        case BinAdjustment::REJECTED:
                            return '<span class="badge badge-danger" value="' . $value . '">Rejected</span>';
                        default:
                            return '<span class="badge badge-secondary" value="' . $value . '">Unknown</span>';
                    }
                })
                ->editColumn('pl_code', function ($data) {
                    return '<span class="btn btn-sm btn-primary">' . $data->pl_code . '</span>';
                })
                ->editColumn('article', function ($data) {
                    return '<span class="btn btn-sm btn-primary" style="white-space: nowrap;">[' . $data->br_name . '] ' . $data->p_name . ' ' . $data->p_color . ' ' . $data->sz_name . '</span>';
                })
                ->editColumn('ba_updated_at', function ($data) {
                    return date('d-m-Y H:i:s', strtotime($data->ba_updated_at));
                })
                ->editColumn('adjust', function ($data) {
                    if ($data->ba_adjust_type == '+') {
                        return '<span class="btn btn-sm btn-success" style="white-space: nowrap;">' . $data->ba_adjust_type . ' ' . $data->ba_adjust . '</span>';
                    } else {
                        return '<span class="btn btn-sm btn-danger" style="white-space: nowrap;">' . $data->ba_adjust_type . ' ' . $data->ba_adjust . '</span>';
                    }
                })
                ->rawColumns(['pl_code', 'article', 'adjust', 'ba_status'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->status)) {
                        $instance->where('ba_status', '=', $request->status);
                    }
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('ba_code', 'LIKE', "%$search%")
                                ->orWhere('pl_code', 'LIKE', "%$search%")
                                ->orWhereRaw('CONCAT(br_name," ", p_name," ",p_color," ",sz_name) LIKE ?', "%$search%");
                        });
                    }
                    if (!empty($request->adjustment_date)) {
                        $range = $request->adjustment_date;
                        $exp = explode('|', $range);
                        if (count($exp) > 1) {
                            $instance->whereDate('bin_adjustments.created_at', '>=', $exp[0])
                                ->whereDate('bin_adjustments.created_at', '<=', $exp[1]);
                        } else {
                            $instance->whereDate('bin_adjustments.created_at', $range);
                        }
                    }
                    if (!empty($request->st_id)) {
                        $instance->where('product_locations.st_id', '=', $request->st_id);
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function articleDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(ProductLocationSetup::select('product_location_setups.id as pls_id', 'products.id as p_id', 'br_name', 'p_name', 'p_color', 'sz_name', 'mc_name', 'pls_qty', 'ps_barcode')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
                ->where('pls_qty', '>', '0')
                ->where('pl_id', '=', $request->pl_id)
                ->groupBy('products.id'))
                ->editColumn('article', function ($data) {
                    $arr_name = array();
                    $arr_name = explode(" ", $data->p_name);
                    $display_name = "";
                    foreach ($arr_name as $word) {
                        $length_name = strlen($display_name) + strlen($word);
                        if ($length_name >= 20) {
                            $display_name .= "<br />" . $word . " ";
                        } else {
                            $display_name .= $word . " ";
                        }
                    }

                    $arr_color = array();
                    $arr_color = explode("/", $data->p_color);
                    $display_color = "";
                    foreach ($arr_color as $color) {
                        $length_color = strlen($display_color) + strlen($color);
                        if ($length_color >= 20) {
                            $display_color .= "<br />" . $color . " ";
                        } else {
                            $display_color .= $color . " ";
                        }
                    }
                    return '<span style="white-space: nowrap;">[' . $data->br_name . ']<br/>' . $display_name . '<br/>' . $display_color . '</span>';
                })
                ->editColumn('qty', function ($data) use ($request) {
                    $check_pst = ProductLocationSetup::select('product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('product_location_setups.pl_id', '=', $request->pl_id)
                        ->where('product_stocks.p_id', '=', $data->p_id)
                        ->where('pls_qty', '>', 0)
                        ->get();
                    if (!empty($check_pst)) {
                        $sz_name = '';
                        foreach ($check_pst as $row) {
                            if (!empty($row->ps_barcode)) {
                                $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-3" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')" style="white-space: nowrap;">' . $row->sz_name . '</a> <a class="btn btn-sm btn-primary col-2" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->pls_qty . '</a> <a style="white-space: nowrap;" class="btn btn-sm btn-primary col-7" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->ps_barcode . '</a></div>';
                            } else {
                                $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-6" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')" style="white-space: nowrap;">' . $row->sz_name . '</a> <a class="btn btn-sm btn-primary col-6" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->pls_qty . '</a></div>';
                            }
                        }
                        return $sz_name;
                    } else {
                        return 'Data belum disetup';
                    }
                })
                ->editColumn('action', function ($data) use ($request) {
                    $check_pst = ProductLocationSetup::select('product_location_setups.id as pls_id', 'product_stocks.id as pst_id', 'product_locations.id as pl_id', 'sz_name', 'pls_qty', 'ps_barcode')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('product_location_setups.pl_id', '=', $request->pl_id)
                        ->where('product_stocks.p_id', '=', $data->p_id)
                        ->where('pls_qty', '>', 0)
                        ->get();
                    if (!empty($check_pst)) {
                        $action = '';
                        foreach ($check_pst as $row) {
                            $this->table_row += 1;
                            $action .= '
                            <div class="row">
                            <input data-adjustment-qty data-qty="' . $row->pls_qty . '" id="adjustment_qty" type="number" class="form-control col-6 adjustment_qty' . $this->table_row . '" style="padding:10px; margin-bottom:2px; width:100px;" value="" title="' . $data->p_name . ' ' . $data->p_color . ' ' . $row->sz_name . '" placeholder="qty"/>
                            <select class="form-control col-6 adjustment_note' . $this->table_row . '" id="ba_note">
                                <option value="">-- Pilih Note Adjustment --</option>
                                <option value="STOCK OPNAME">STOCK OPNAME</option>
                                <option value="PARTIAL">PARTIAL</option>
                                <option value="REJECT">REJECT</option>
                                <option value="CACAT">CACAT</option>
                                <option value="PERBAIKAN">PERBAIKAN</option>
                                <option value="PROMOSI">PROMOSI</option>
                                <option value="OPERASIONAL">OPERASIONAL</option>
                                <option value="SSR">SSR</option>
                                <option value="RESELLER">RESELLER</option>
                                <option value="KESALAHAN SYSTEM">KESALAHAN SYSTEM</option>
                                <option value="CYCLE COUNT">CYCLE COUNT</option>
                                <option value="RETUR IN">RETUR IN</option>
                                <option value="RETUR OUT">RETUR OUT</option>
                                <option value="MARKETPLACE IN">MARKETPLACE IN</option>
                                <option value="MARKETPLACE IN">KERUGIAN RETUR MP</option>
                            </select>
                            <i class="fa fa-eye d-none" onclick="return saveAdjustment(' . $row->pls_id . ', ' . $this->table_row . ', ' . $row->pst_id . ', ' . $row->pls_qty . ', ' . $row->pl_id . ')" id="saveAdjustment' . $this->table_row . '"></i>
                            </div>';
                        }
                        return $action;
                    } else {
                        return '-';
                    }
                })
                ->rawColumns(['article', 'qty', 'action'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('p_name', 'LIKE', "%$search%")
                                ->orWhere('br_name', 'LIKE', "%$search%")
                                ->orWhere('p_color', 'LIKE', "%$search%")
                                ->orWhere('sz_name', 'LIKE', "%$search%")
                                ->orWhere('ps_barcode', 'LIKE', "%$search%"); // Added search for ps_barcode
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function validatedDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(ProductLocation::select('product_locations.id as pl_id', 'pl_code', 'u_name')
                ->join('users', 'users.id', '=', 'product_locations.u_id_adjustment')
                ->where('pl_adjustment', '=', '1'))
                ->editColumn('pl_code', function ($data) {
                    return '<span data-pl_id="' . $data->pl_id . '" data-pl_code="' . $data->pl_code . '" id="validated_bin" class="btn btn-sm btn-success">' . $data->pl_code . '</span>';
                })
                ->rawColumns(['pl_code'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function notValidatedDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(ProductLocation::select('id', 'pl_code', 'pl_name', 'pl_description')
                ->where('pl_adjustment', '=', '0'))
                ->editColumn('pl_code', function ($data) {
                    return '<span class="btn btn-sm btn-primary">' . $data->pl_code . '</span>';
                })
                ->editColumn('pl_name', function ($data) {
                    return $data->pl_name . ' ' . $data->pl_description;
                })
                ->rawColumns(['pl_code', 'pl_name'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function productAdjustment(Request $request)
    {
        $response = []; // <- Tambahkan ini

        $pls_id = $request->_pls_id;
        $pst_id = $request->_pst_id;
        $pl_id = $request->_pl_id;
        $ba_qty = $request->_ba_qty;
        $ba_note = $request->_ba_note;
        $pls_qty = $request->_pls_qty;

        $check_location = ProductLocationSetup::where(['id' => $pls_id])->exists();
        if ($check_location) {
            $adjust_qty = 0;
            $adjust_type = '';
            if ($ba_qty > $pls_qty) {
                $adjust_qty = $ba_qty - $pls_qty;
                $adjust_type = '+';
            } else if ($ba_qty < $pls_qty) {
                $adjust_qty = $pls_qty - $ba_qty;
                $adjust_type = '-';
            }

            $ba_code = 'ADJ' . date('YmdHis');

            $store = ProductLocation::select('st_name')
                ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
                ->where('product_locations.id', $pl_id)
                ->first();

            $aliases = [
                'JEZ MALANG' => 'MLG',
                'EVENT JEZ MALANG' => 'MLG',
                'EVENT JEZ SURABAYA' => 'SBY',
                'JEZ SURABAYA' => 'SBY',
                'JEZ KEDIRI' => 'KDR',
                'JEZ JEMBER' => 'JBR',
                'JEZ SIDOARJO' => 'SDA',
                'EVENT JEZ SIDOARJO' => 'SDA'
            ];

            $st_name = $store->st_name ?? '';
            $alias = $aliases[$st_name] ?? $st_name;
            $final_note = $alias . ' - ' . ($ba_note ?: '-');

            $bin_history = BinAdjustment::create([
                'pls_id' => $pls_id,
                'u_id' => Auth::user()->id,
                'ba_code' => $ba_code,
                'ba_old_qty' => $pls_qty,
                'ba_new_qty' => $ba_qty,
                'ba_adjust' => $adjust_qty,
                'ba_adjust_type' => $adjust_type,
                'ba_note' => $final_note,
                'ba_status' => BinAdjustment::NEED_APPROVAL,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $response['status'] = '200';
            $response['message'] = 'Adjustment berhasil dibuat';
        } else {
            $response['status'] = '400';
            $response['message'] = 'Product location tidak ditemukan';
        }

        return response()->json($response);
    }



    public function finishAdjustment()
    {
        $location = ProductLocation::where([
            'pl_adjustment' => '1'
        ])->update([
            'pl_adjustment' => '0',
            'u_id_adjustment' => null
        ]);
        if (!empty($location)) {
            $this->UserActivity('menyelesaikan adjustment');
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function addArticle(Request $request)
    {
        $pls_qty = $request->_pls_qty;
        $pst_id = $request->_pst_id;
        $article_note = $request->_article_note;
        $bin = $request->_bin;
        $pl_id = $bin;
        $check_location = ProductLocationSetup::where(['pst_id' => $pst_id, 'pl_id' => $pl_id])->exists();
        if ($check_location) {
            $pls = ProductLocationSetup::select('id', 'pls_qty')->where(['pst_id' => $pst_id, 'pl_id' => $pl_id])->get()->first();
            $pls_id = $pls->id;
            $pls_current_qty = $pls->pls_qty;
            $ba_code = 'ADJ' . date('YmdHis');

            $store = ProductLocation::select('st_name')
                ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
                ->where('product_locations.id', $pl_id)
                ->first();

            $aliases = [
                'JEZ MALANG' => 'MLG',
                'EVENT JEZ MALANG' => 'MLG',
                'EVENT JEZ SURABAYA' => 'SBY',
                'JEZ SURABAYA' => 'SBY',
                'JEZ KEDIRI' => 'KDR',
                'JEZ JEMBER' => 'JBR',
                'JEZ SIDOARJO' => 'SDA',
                'EVENT JEZ SIDOARJO' => 'SDA'
            ];

            $st_name = $store->st_name ?? '';
            $alias = $aliases[$st_name] ?? $st_name;
            $final_note = $alias . ' - ' . ($article_note ?: '-');

            $bin_history = BinAdjustment::create([
                'pls_id' => $pls_id,
                'u_id' => Auth::user()->id,
                'ba_code' => $ba_code,
                'ba_old_qty' => $pls_current_qty,
                'ba_new_qty' => $pls_current_qty + $pls_qty,
                'ba_adjust' => $pls_qty,
                'ba_adjust_type' => '+',
                'ba_note' => $final_note,
                'ba_status' => BinAdjustment::NEED_APPROVAL,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            if (!empty($bin_history)) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
            $insert_id = DB::table('product_location_setups')->insertGetId([
                'pst_id' => $pst_id,
                'pl_id' => $pl_id,
                'pls_qty' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            if (!empty($insert_id)) {
                $ba_code = 'ADJ' . date('YmdHis');
                $bin_history = BinAdjustment::create([
                    'pls_id' => $insert_id,
                    'u_id' => Auth::user()->id,
                    'ba_code' => $ba_code,
                    'ba_old_qty' => '0',
                    'ba_new_qty' => $pls_qty,
                    'ba_adjust' => $pls_qty,
                    'ba_adjust_type' => '+',
                    'ba_note' => $article_note,
                    'ba_status' => BinAdjustment::NEED_APPROVAL,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                if (!empty($bin_history)) {
                    $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->where('product_stocks.id', $pst_id)
                        ->get()->first();
                    $this->UserActivity('menambah artikel [' . $item->br_name . '] ' . $item->p_name . ' ' . $item->p_color . ' ' . $item->sz_name . ' pada BIN ' . $bin);
                    $r['status'] = '200';
                } else {
                    $r['status'] = '400';
                }
            }
        }
        return json_encode($r);
    }

    function fetchArticle(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = ProductStock::selectRaw('ts_product_stocks.id as pst_id, CONCAT("[",br_name,"] ", p_name," ",p_color," ",sz_name) as p_name, ts_product_stocks.ps_barcode')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->where('p_delete', '!=', '1')
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->whereRaw('CONCAT(p_name," ", p_color," ", sz_name) LIKE ?', ["%$query%"])
                        ->orWhere('ps_barcode', 'LIKE', "%$query%");
                })
                ->orderBy('p_name')
                ->limit(10)
                ->get();

            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach ($data as $row) {
                    $output .= '
                    <li><a class="btn btn-sm btn-inventory col-12" data-pst_id="' . $row->pst_id . '" data-p_name="' . $row->p_name . '" id="add_to_item_list">
                        <span class="btn-sm btn-primary">' . $row->p_name . ' (Barcode: ' . $row->ps_barcode . ')</span>
                    </a></li>';
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';

            echo $output;
        }
    }

    public function getDetailAdjustment($id)
    {
        $data = BinAdjustment::select(
            'bin_adjustments.id as ba_id',
            'ps_barcode',
            'pls_id',
            'st_name',
            'pl_code',
            'users.u_name as user_name',
            'executor.u_name as executor_name',
            'approver.u_name as approver_name',
            'ba_approve',
            'ba_executor',
            'br_name',
            'p_name',
            'p_color',
            'sz_name',
            'ba_code',
            'ba_note',
            'ba_old_qty',
            'ba_new_qty',
            'ba_adjust',
            'ba_adjust_type',
            'bin_adjustments.created_at as ba_created',
            'ba_status',
            'approved_at',
            'execute_at'
        )
            ->leftJoin('users', 'users.id', '=', 'bin_adjustments.u_id')
            ->leftJoin('users as executor', 'executor.id', '=', 'bin_adjustments.ba_executor')
            ->leftJoin('users as approver', 'approver.id', '=', 'bin_adjustments.ba_approve')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'bin_adjustments.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('bin_adjustments.id', $id)
            ->first();

        // Example: safely access user fields, fallback to '-' if null
        $result = [
            'ba_id' => $data->ba_id ?? null,
            'ps_barcode' => $data->ps_barcode ?? null,
            'pls_id' => $data->pls_id ?? null,
            'st_name' => $data->st_name ?? null,
            'pl_code' => $data->pl_code ?? null,
            'user_name' => $data->user_name ?? '-',
            'executor_name' => $data->executor_name ?? '-',
            'approver_name' => $data->approver_name ?? '-',
            'ba_approve' => $data->ba_approve ?? null,
            'ba_executor' => $data->ba_executor ?? null,
            'br_name' => $data->br_name ?? null,
            'p_name' => $data->p_name ?? null,
            'p_color' => $data->p_color ?? null,
            'sz_name' => $data->sz_name ?? null,
            'ba_code' => $data->ba_code ?? null,
            'ba_note' => $data->ba_note ?? null,
            'ba_old_qty' => $data->ba_old_qty ?? null,
            'ba_new_qty' => $data->ba_new_qty ?? null,
            'ba_adjust' => $data->ba_adjust ?? null,
            'ba_adjust_type' => $data->ba_adjust_type ?? null,
            'ba_created' => $data->ba_created ?? null,
            'ba_status' => $data->ba_status ?? null,
            'approved_at' => $data->approved_at ?? null,
            'execute_at' => $data->execute_at ?? null
        ];

        if ($data) {
            return response()->json(['status' => '200', 'data' => $result]);
        } else {
            return response()->json(['status' => '404', 'message' => 'Data not found']);
        }
    }

    public function approveAdjustment($id)
    {
        $adjustment = BinAdjustment::query()
            ->where('id', $id)
            ->where('ba_status', BinAdjustment::NEED_APPROVAL)
            ->first();

        if (!$adjustment) {
            return response()->json(['status' => '404', 'message' => 'Adjustment not found']);
        }

        $pls = ProductLocationSetup::query()
            ->where('id', $adjustment->pls_id)
            ->first();

        if (!$pls) {
            return response()->json(['status' => '404', 'message' => 'Bin tidak ditemukan']);
        }

        if ($adjustment && $adjustment->ba_old_qty != $pls->pls_qty) {
            return response()->json(['status' => '400', 'message' => 'Jumlah stok sudah berubah, silakan buat adjustment baru']);
        }

        $role_id = DB::table('store_types')
            ->where('stt_name', 'PURCHASING')
            ->value('id');

        // Only allow users with PURCHASING role or admin to approve
        if (Auth::user()->stt_id != $role_id && !User::isAdmin(Auth::user()->id)) {
            return response()->json(['status' => '403', 'message' => 'You do not have permission to approve adjustments']);
        }

        if ($adjustment) {
            $adjustment->update([
                'ba_approve' => Auth::user()->id,
                'ba_status' => BinAdjustment::NEED_EXECUTION,
                'approved_at' => now()
            ]);

            $this->UserActivity('menyetujui adjustment dengan kode ' . $adjustment->ba_code);
            return response()->json(['status' => '200', 'message' => 'Adjustment approved successfully']);
        } else {
            return response()->json(['status' => '404', 'message' => 'Adjustment not found']);
        }
    }

    public function executeAdjustment($id)
    {

        $adjustment = BinAdjustment::query()
            ->where('id', $id)
            ->where('ba_status', BinAdjustment::NEED_EXECUTION)
            ->first();

        if (!$adjustment) {
            return response()->json(['status' => '404', 'message' => 'Adjustment not found']);
        }

        $pls = ProductLocationSetup::query()
            ->where('id', $adjustment->pls_id)
            ->first();

        if (!$pls) {
            return response()->json(['status' => '404', 'message' => 'Bin tidak ditemukan']);
        }

        if ($adjustment && $adjustment->ba_old_qty != $pls->pls_qty) {
            return response()->json(['status' => '400', 'message' => 'Jumlah stok sudah berubah, silakan buat adjustment baru']);
        }

        if ($adjustment) {
            $adjustment->update([
                'ba_executor' => Auth::user()->id,
                'ba_status' => BinAdjustment::DONE,
                'execute_at' => now()
            ]);

            // Update the product location setup with the new quantity
            $pls = ProductLocationSetup::find($adjustment->pls_id);
            if ($pls) {
                $new_qty = $adjustment->ba_new_qty;
                // Update the quantity in the product location setup
                $pls->update(['pls_qty' => $new_qty]);
            }

            $this->UserActivity('mengeksekusi adjustment dengan kode ' . $adjustment->ba_code);
            return response()->json(['status' => '200', 'message' => 'Adjustment executed successfully']);
        } else {
            return response()->json(['status' => '404', 'message' => 'Adjustment not found']);
        }
    }

    public function cancelAdjustment($id)
    {
        $adjustment = BinAdjustment::query()
            ->where('id', $id)
            ->where('ba_status', BinAdjustment::NEED_EXECUTION)
            ->first();

        if ($adjustment) {
            $adjustment->update([
                'ba_status' => BinAdjustment::CANCEL,
                'ba_executor' => Auth::user()->id,
                'execute_at' => now()
            ]);

            $this->UserActivity('membatalkan adjustment dengan kode ' . $adjustment->ba_code);
            return response()->json(['status' => '200', 'message' => 'Adjustment cancelled successfully']);
        } else {
            return response()->json(['status' => '404', 'message' => 'Adjustment not found']);
        }
    }

    public function rejectAdjustment($id)
    {
        $adjustment = BinAdjustment::query()
            ->where('id', $id)
            ->where('ba_status', BinAdjustment::NEED_APPROVAL)
            ->first();

        if ($adjustment) {
            $adjustment->update([
                'ba_status' => BinAdjustment::REJECTED,
                'ba_executor' => Auth::user()->id,
                'execute_at' => now()
            ]);

            $this->UserActivity('menolak adjustment dengan kode ' . $adjustment->ba_code);
            return response()->json(['status' => '200', 'message' => 'Adjustment rejected successfully']);
        } else {
            return response()->json(['status' => '404', 'message' => 'Adjustment not found']);
        }
    }
}
