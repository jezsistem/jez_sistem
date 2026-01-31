<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Store;
use App\Models\ProductStock;
use App\Models\ProductLocation;
use App\Models\ProductLocationSetup;
use App\Models\Brand;
use App\Models\MainColor;
use App\Models\Size;
use App\Exports\ProductLocationSetupExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductLocationSetupController extends Controller
{
    protected function validateAccess()
    {
        $segment = request()->segment(1);
        // Strip _v2 suffix if present for access validation
        $slug = str_replace('_v2', '', $segment);
        
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => $slug
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
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'pl_id' => ProductLocation::selectRaw('ts_product_locations.id as pl_id, CONCAT(pl_code," (",st_name,")") as location')
                ->join('stores', 'stores.id', '=', 'product_locations.st_id')
                ->where('pl_delete', '!=', '1')
                ->orderByDesc('pl_code')->pluck('location', 'pl_id'),
            'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'mc_id' => MainColor::where('mc_delete', '!=', '1')->orderByDesc('id')->pluck('mc_name', 'id'),
            'sz_id' => Size::where('sz_delete', '!=', '1')->orderByDesc('id')->pluck('sz_name', 'id'),
        ];
        return view('app.product_location_setup.product_location_setup', compact('data'));
    }

    public function indexV2()
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
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'setup_lokasi_stok')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => 'setup_lokasi_stok',
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'pl_id' => ProductLocation::selectRaw('ts_product_locations.id as pl_id, CONCAT(pl_code," (",st_name,")") as location')
                ->join('stores', 'stores.id', '=', 'product_locations.st_id')
                ->where('pl_delete', '!=', '1')
                ->orderByDesc('pl_code')->pluck('location', 'pl_id'),
            'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'mc_id' => MainColor::where('mc_delete', '!=', '1')->orderByDesc('id')->pluck('mc_name', 'id'),
            'sz_id' => Size::where('sz_delete', '!=', '1')->orderByDesc('id')->pluck('sz_name', 'id'),
        ];
        return view('app.updated_setup_lokasi_stok.setup_lokasi_stok', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(ProductLocation::select('product_locations.id as pl_id', 'st_name', 'pl_code', 'pl_name', 'pl_description', 'pl_capacity')
                ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
                ->where('pl_delete', '!=', '1'))
                ->editColumn('pl_location', function ($data) {
                    return '<span style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-12">' . $data->pl_code . '</a></span>';
                })
                ->editColumn('pl_location_plain', function ($data) {
                    return '<span style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-12" style="white-space: nowrap;">' . $data->pl_code . '</a></span>';
                })
                ->editColumn('pl_product', function ($data) {
                    $check = ProductLocationSetup::where('pl_id', '=', $data->pl_id)->get();
                    if (!empty($check)) {
                        $total_product = 0;
                        foreach ($check as $row) {
                            $total_product += $row->pls_qty;
                        }
                        return '<a class="btn btn-sm btn-primary col-7" style="white-space: nowrap;">' . $total_product . '</a>';
                    } else {
                        return '<a class="btn btn-sm btn-primary">0</a>';
                    }
                })
                ->editColumn('pl_capacity', function ($data) {
                    // Ambil kapasitas dari tabel product_locations
                    $capacity = $data->pl_capacity ?? 0;
                    // Hitung jumlah qty dari tabel product_location_setups berdasarkan pl_id
                    $total_product = ProductLocationSetup::where('pl_id', '=', $data->pl_id)->sum('pls_qty');
                    // Tentukan warna tombol berdasarkan kondisi stok
                    if ($capacity == 0) {
                        return '<a class="btn btn-sm btn-light">Setup Kapasitas</a>';
                    } elseif ($total_product < $capacity) {
                        return '<a class="btn btn-sm btn-info">' . $total_product . '/' . $capacity . '</a>';
                    } elseif ($total_product == $capacity) {
                        return '<a class="btn btn-sm btn-success">' . $total_product . '/' . $capacity . '</a>';
                    } else { // $total_product > $capacity
                        return '<a class="btn btn-sm btn-warning">' . $total_product . '/' . $capacity . '</a>';
                    }                  
                })
                ->editColumn('pl_percent', function ($data) {
                    $capacity = $data->pl_capacity ?? 0;
                    $total_product = ProductLocationSetup::where('pl_id', '=', $data->pl_id)->sum('pls_qty');
                
                    if ($capacity == 0) {
                        return '<a class="btn btn-sm btn-light">-</span>';
                    } else {
                        $percent = ($total_product / $capacity) * 100;
                        return '<a class="btn btn-sm btn-success">' . number_format($percent) . '%</span>';
                    }
                })                
                ->rawColumns(['pl_location', 'pl_location_plain', 'pl_product', 'pl_capacity', 'pl_percent'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('st_id'))) {
                        $instance->where(function ($w) use ($request) {
                            $st_id = $request->get('st_id');
                            $w->orWhere('st_id', '=', $st_id);
                        });
                    }
                    if (!empty($request->get('st_id'))) {
                        $instance->where(function ($w) use ($request) {
                            $st_id = $request->get('st_id');
                            $w->orWhere('st_id', '=', $st_id);
                        });
                    }
                    if ($request->filled('bin_kl_filter')) {
                        $instance->where('pl_code', 'like', '%' . $request->bin_kl_filter . '%');
                    }                    
                    if (!empty($request->get('search'))) {
                        $instance->leftJoin('product_location_setups', 'product_location_setups.pl_id', '=', 'product_locations.id')
                            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                            ->where('pls_qty', '>=', '0')
                            ->groupBy('product_locations.id');
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhereRaw('CONCAT(br_name," ",p_name," ",p_color," ",sz_name) LIKE ?', "%$search%")
                                ->orWhere('pl_code', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getDatatablesLocation(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(ProductLocationSetup::select('product_location_setups.id as pls_id', 'products.id as p_id', 'br_name', 'p_name', 'p_color', 'sz_name', 'mc_name', 'pls_qty', 'ps_barcode')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
                ->where('pls_qty', '>', 0)
                ->where('pl_id', '=', $request->_pl_id)
                ->groupBy('products.id'))
                ->editColumn('p_name', function ($data) {
                    return '<span style="white-space: nowrap;">[' . $data->br_name . '] ' . $data->p_name . '</span>';
                })
                ->editColumn('p_color', function ($data) {
                    return '<span style="white-space: nowrap;">(' . $data->mc_name . ') ' . $data->p_color . '</span>';
                })
                ->editColumn('p_size', function ($data) use ($request) {
                    $check_pst = ProductLocationSetup::select('product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('product_location_setups.pl_id', '=', $request->_pl_id)
                        ->where('product_stocks.p_id', '=', $data->p_id)
                        ->where('pls_qty', '>', 0)
                        ->get();
                    if (!empty($check_pst)) {
                        $sz_name = '';
                        foreach ($check_pst as $row) {
                            if (!empty($row->ps_barcode)) {
                                $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-3" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')" style="white-space: nowrap;">' . $row->sz_name . '</a> <a class="btn btn-sm btn-primary col-2" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->pls_qty . '</a> <a style="white-space: nowrap;" class="btn btn-sm btn-primary col-7" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->ps_barcode . '</a></div>';
                            } else {
                                $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-4" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')" style="white-space: nowrap;">' . $row->sz_name . '</a> <a class="btn btn-sm btn-primary col-4" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->pls_qty . '</a></div>';
                            }
                        }
                        return $sz_name;
                    } else {
                        return 'Data belum disetup';
                    }
                })
                ->rawColumns(['p_name', 'p_size', 'p_color'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhereRaw('CONCAT(br_name," ",p_name," ",p_color," ",sz_name) LIKE ?', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $product_location = new ProductLocation;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'st_id' => $request->input('st_id'),
            'pl_code' => strtoupper($request->input('pl_code')),
            'pl_name' => $request->input('pl_name'),
            'pl_description' => $request->input('pl_description'),
            'pl_delete' => '0',
        ];

        $save = $product_location->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $product_location = new ProductLocation;
        $id = $request->input('_id');
        $save = $product_location->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkProductInLocation(Request $request)
    {
        try {
            $pl_id = $request->input('_pl_id') ?? $request->_pl_id;
            
            if (!$pl_id) {
                return response()->json(['error' => 'pl_id is required'], 400);
            }
            
            $check = ProductLocationSetup::select(
                    'product_location_setups.id as pls_id', 
                    'products.p_name', 
                    'products.p_color', 
                    'products.p_image', 
                    'sizes.sz_name', 
                    'main_colors.mc_name', 
                    'product_location_setups.pls_qty', 
                    'product_stocks.ps_barcode'
                )
                ->join('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
                ->where('product_location_setups.pl_id', '=', $pl_id)
                ->get();
            
            if (!empty($check)) {
                $get_product = $check;
            } else {
                $get_product = null;
            }
            
            // Limit pl_id to avoid memory issues - only get locations from same store as the selected location
            $selected_location = ProductLocation::find($pl_id);
            $store_id = $selected_location ? $selected_location->st_id : null;
            
            $pl_id_query = ProductLocation::where('pl_delete', '!=', '1');
            if ($store_id) {
                // Only get locations from the same store to reduce memory usage
                $pl_id_query->where('st_id', $store_id);
            }
            $pl_id_list = $pl_id_query->orderByDesc('id')->pluck('pl_name', 'id');
            
            $data = [
                'product' => $get_product,
                'pl_id' => $pl_id_list,
            ];
            
            return view('app.product_location_setup._product_location_setup_detail', compact('data'));
        } catch (\Exception $e) {
            Log::error('Error in checkProductInLocation: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function productMutation(Request $request)
    {
        $pl_id_origin = $request->_pl_id_origin;
        $pst_id = $request->_pst_id;
        $mt_qty = $request->mt_qty;
        $pl_id_destination = $request->pl_id_destination;

        $check_setup = ProductLocationSetup::where(['pl_id' => $pl_id_destination, 'pst_id' => $pst_id])->exists();
        if ($check_setup) {
            $data_destination = ProductLocationSetup::where(['pl_id' => $pl_id_destination, 'pst_id' => $pst_id])->get()->first();
            $qty_destination = $data_destination->pls_qty;
            if ($qty_destination < 0) {
                $qty_destination = 0;
            }
            $update_data_destination = [
                'pls_qty' => $mt_qty + $qty_destination
            ];
            $update_destination = ProductLocationSetup::where(['pl_id' => $pl_id_destination, 'pst_id' => $pst_id])->update($update_data_destination);
            if (!empty($update_destination)) {
                $data_origin = ProductLocationSetup::where(['pl_id' => $pl_id_origin, 'pst_id' => $pst_id])->get()->first();
                $qty_origin = $data_origin->pls_qty;
                $remain = $qty_origin - $mt_qty;
                $update_data_origin = [
                    'pls_qty' => $remain
                ];
                $update_origin = ProductLocationSetup::where(['pl_id' => $pl_id_origin, 'pst_id' => $pst_id])->update($update_data_origin);
                if (!empty($update_origin)) {
                    $r['status'] = '200';
                } else {
                    $r['status'] = '400';
                }
            } else {
                $r['status'] = '400';
            }
        } else {
            $insert_data_destination = [
                'pls_qty' => $mt_qty,
                'pl_id' => $pl_id_destination,
                'pst_id' => $pst_id,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $insert_destination = ProductLocationSetup::create($insert_data_destination);
            if (!empty($insert_destination)) {
                $data_origin = ProductLocationSetup::where(['pl_id' => $pl_id_origin, 'pst_id' => $pst_id])->get()->first();
                $qty_origin = $data_origin->pls_qty;
                $remain = $qty_origin - $mt_qty;
                $update_data_origin = [
                    'pls_qty' => $remain
                ];
                if ($remain <= 0) {
                    $update_origin = ProductLocationSetup::where(['pl_id' => $pl_id_origin, 'pst_id' => $pst_id])->delete();
                } else {
                    $update_origin = ProductLocationSetup::where(['pl_id' => $pl_id_origin, 'pst_id' => $pst_id])->update($update_data_origin);
                }
                if (!empty($update_origin)) {
                    $r['status'] = '200';
                } else {
                    $r['status'] = '400';
                }
            } else {
                $r['status'] = '400';
            }
        }
        return json_encode($r);
    }

    public function productSetup(Request $request)
    {
        $pst_id = $request->_pst_id_add;
        $mt_qty = $request->mt_qty_add;
        $pl_id_destination = $request->pl_id_destination_add;

        $check_setup = ProductLocationSetup::where(['pl_id' => $pl_id_destination, 'pst_id' => $pst_id])->exists();
        if ($check_setup) {
            $data_destination = ProductLocationSetup::where(['pl_id' => $pl_id_destination, 'pst_id' => $pst_id])->get()->first();
            $qty_destination = $data_destination->pls_qty;
            if ($qty_destination < 0) {
                $qty_destination = 0;
            }
            $update_data_destination = [
                'pls_qty' => $mt_qty + $qty_destination
            ];
            $update_destination = ProductLocationSetup::where(['pl_id' => $pl_id_destination, 'pst_id' => $pst_id])->update($update_data_destination);
            if (!empty($update_destination)) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
            $insert_data_destination = [
                'pls_qty' => $mt_qty,
                'pl_id' => $pl_id_destination,
                'pst_id' => $pst_id,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $insert_destination = ProductLocationSetup::create($insert_data_destination);
            if (!empty($insert_destination)) {
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        }
        return json_encode($r);
    }

    public function exportData(Request $request)
    {
        $store = $request->get('st_id');
        return Excel::download(new ProductLocationSetupExport($store), 'product_location_setup.xlsx');
    }
}
