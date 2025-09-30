<?php

namespace App\Http\Controllers;

use App\Imports\StockLocationImport;
use App\Models\TempMutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
use App\Models\ProductMutation;
use App\Models\ExceptionLocation;
use App\Exports\SetupHistoryExport;
use App\Imports\TempMultiBinMutationImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductLocationSetupV2Controller extends Controller
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

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'mc_id' => MainColor::where('mc_delete', '!=', '1')->orderByDesc('id')->pluck('mc_name', 'id'),
            'sz_id' => Size::where('sz_delete', '!=', '1')->orderByDesc('id')->pluck('sz_name', 'id'),
        ];

        return view('app.product_location_setup_v2.product_location_setup_v2', compact('data'));
    }

    public function startBinDatatables(Request $request)
    {
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }
        $st_city = Store::select('st_code')->where('id', '=', Auth::user()->st_id)->first();
        if (request()->ajax()) {
            $count = TempMutasi::where('u_id', Auth::user()->id)->where('pl_end', null)->count();
            if ($count > 0) {

                // Convert pl_id to array if it's not already
                $pl_ids = is_array($request->pl_id) ? $request->pl_id : [$request->pl_id];

                $pls_ids = TempMutasi::select('pls_id')
                    ->where('u_id', Auth::user()->id)
                    ->where('pl_end', null)
                    ->groupBy('pls_id')
                    ->pluck('pls_id')->toArray();

                return datatables()->of(ProductLocationSetup::select(
                    'product_location_setups.id as pls_id',
                    'products.id as p_id',
                    'br_name',
                    'p_name',
                    'p_color',
                    'sz_name',
                    'mc_name',
                    'product_location_setups.pls_qty',
                    'product_stocks.ps_barcode',
                    'product_locations.pl_code',
                    'product_locations.id as pl_id',
                )
                    ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                    ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
                    ->join('temp_mutasi', 'temp_mutasi.pls_id', '=', 'product_location_setups.id')
                    // ->whereIn('product_location_setups.id', $pls_ids)
                    ->where('product_locations.pl_code', '!=', 'TRIAL')
                    ->where(function ($w) use ($st_id, $st_city) {
                        $w->where('product_locations.pl_description', '=', $st_city->st_code);
                    })
                    ->where('temp_mutasi.u_id', '=', Auth::user()->id)
                    // ->where('product_location_setups.pls_qty', '>', '0')
                    ->groupBy('products.id', 'product_location_setups.pl_id'))
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
                    ->editColumn('qty', function ($data) use ($pls_ids) {
                        $check_pst = ProductLocationSetup::select('product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                            ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                            ->whereIn('product_location_setups.id', $pls_ids)
                            ->where('product_stocks.p_id', '=', $data->p_id)
                            ->get();
                        // dd($check_pst);
                        if (!empty($check_pst)) {
                            $sz_name = '';
                            foreach ($check_pst as $row) {
                                if (!empty($row->ps_barcode)) {
                                    $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-3" style="white-space: nowrap;padding-bottom: 10px;padding-top: 10px;">' . $row->sz_name . '</a> <a class="btn btn-sm btn-primary col-2" style="white-space: nowrap;padding-bottom: 10px;padding-top: 10px;" onclick="return mutation(' . $row->pst_id . ', 0, \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->pls_qty . '</a> <a style="white-space: nowrap;padding-bottom: 10px;padding-top: 10px;" class="btn btn-sm btn-primary col-7" onclick="return mutation(' . $row->pst_id . ', 0, \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->ps_barcode . '</a></div>';
                                } else {
                                    $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-4" style="white-space: nowrap;padding-bottom: 10px;padding-top: 10px;">' . $row->sz_name . '</a> 
                                                <a class="btn btn-sm btn-primary col-4" style="white-space: nowrap;padding-bottom: 10px;padding-top: 10px;" onclick="return mutation(' . $row->pst_id . ', 0, \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->pls_qty . '</a></div>';
                                }
                            }
                            return $sz_name;
                        } else {
                            return 'Data belum disetup';
                        }
                    })
                    ->editColumn('bin_code', function ($data) {
                        return '<span class="btn btn-sm btn-primary">' . $data->pl_code . '</span>';
                    })
                    ->editColumn('action', function ($data) use ($pls_ids) {
                        $check_pst = ProductLocationSetup::select('product_location_setups.id as pls_id', 'product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                            ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                            ->whereIn('product_location_setups.id', $pls_ids)
                            ->where('product_stocks.p_id', '=', $data->p_id)
                            ->get();

                        if (!empty($check_pst)) {
                            $action = '';
                            foreach ($check_pst as $row) {
                                $initial_pst_get = TempMutasi::where('ps_barcode', $row->ps_barcode)->where('u_id', Auth::user()->id)->where('pl_end', null)->first();
                                $initial_pst = $initial_pst_get ? $initial_pst_get->pls_qty : "";
                                $initial_note = $initial_pst_get ? $initial_pst_get->notes : "";

                                $this->table_row += 1;
                                $action .= '
                                    <div class="d-flex align-items-center mb-2">
                                        <input data-mutation-qty data-qty="' . $row->pls_qty . '" id="mutation_qty" type="text" class="form-control mutation_qty' . $this->table_row . '" style="padding:10px; margin-right:5px; width: 50%; min-width:150px;" value="' . $initial_pst . '" title="' . $data->p_name . ' ' . $data->p_color . ' ' . $row->sz_name . '"/>
                                        <input type="text" class="form-control mutation_note' . $this->table_row . '" style="padding:10px; width: 50%; min-width:150px;" placeholder="Note" title="Note" value="' . $initial_note . '"/>
                                    </div>
                                    <i class="fa fa-eye d-none" onclick="return saveMutation(' . $row->pls_id . ', ' . $this->table_row . ', ' . $row->pst_id . ', ' . $row->pls_qty . ', document.querySelector(\'.mutation_note' . $this->table_row . '\').value)" id="saveMutation' . $this->table_row . '"></i>';
                            }
                            return $action;
                        } else {
                            return '-';
                        }
                    })
                    ->rawColumns(['article', 'qty', 'bin_code', 'action'])
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->where(function ($w) use ($request) {
                                $search = $request->get('search');
                                $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
                                    ->orWhere('p_name', 'LIKE', "%$search%")
                                    ->orWhere('br_name', 'LIKE', "%$search%")
                                    ->orWhere('article_id', 'LIKE', "%$search%")
                                    ->orWhere('product_stocks.ps_barcode', 'LIKE', "%$search%")
                                    ->orWhere('p_color', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->addIndexColumn()
                    ->make(true);
                } else {
                    // Convert pl_id to array if it's not already
                    $pl_ids = is_array($request->pl_id) ? $request->pl_id : [$request->pl_id];
                    
                    return datatables()->of(ProductLocationSetup::select(
                        'product_location_setups.id as pls_id', 
                        'products.id as p_id', 
                        'br_name', 
                        'p_name', 
                        'p_color', 
                        'sz_name', 
                        'mc_name', 
                        'pls_qty', 
                        'product_stocks.ps_barcode',
                        'product_locations.pl_code'
                    )
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
                        ->whereIn('pl_id', $pl_ids)
                        ->where('product_locations.pl_code', '!=', 'TRIAL')
                        ->where(function ($w) use ($st_id, $st_city) {
                            $w->where('product_locations.pl_description', '=', $st_city->st_code);
                        })
                        ->groupBy('products.id', 'product_location_setups.pl_id'))
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
                        ->editColumn('qty', function ($data) use ($pl_ids) {
                            $check_pst = ProductLocationSetup::select('product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                                ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                                ->whereIn('product_location_setups.pl_id', $pl_ids)
                                ->where('product_stocks.p_id', '=', $data->p_id)
//                            ->where('pls_qty', '>', 0)
                                ->get();
                            if (!empty($check_pst)) {
                                $sz_name = '';
                                foreach ($check_pst as $row) {
                                    if (!empty($row->ps_barcode)) {
                                        $sz_name .= '<div class="pb-2" style="white-space: nowrap;">
                                            <a class="btn btn-sm btn-primary col-3" style="white-space: nowrap;padding-bottom: 10px;padding-top: 10px;">' . $row->sz_name . '</a>
                                            <a class="btn btn-sm btn-primary col-2" style="white-space: nowrap;padding-bottom: 10px;padding-top: 10px;" onclick="return mutation(' . $row->pst_id . ', 0, \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->pls_qty . '</a>
                                            <a style="white-space: nowrap;padding-bottom: 10px;padding-top: 10px;" class="btn btn-sm btn-primary col-7" onclick="return mutation(' . $row->pst_id . ', 0, \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->ps_barcode . '</a>
                                        </div>';
                                    } else {
                                        $sz_name .= '<div class="pb-2" style="white-space: nowrap;">
                                            <a class="btn btn-sm btn-primary col-4" style="white-space: nowrap;padding-bottom: 10px;padding-top: 10px;">' . $row->sz_name . '</a>
                                            <a class="btn btn-sm btn-primary col-4" style="white-space: nowrap;padding-bottom: 10px;padding-top: 10px;" onclick="return mutation(' . $row->pst_id . ', 0, \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->pls_qty . '</a>
                                        </div>';
                                    }
                                }
                                return $sz_name;
                            } else {
                                return 'Data belum disetup';
                            }
                        })
                        ->editColumn('bin_code', function ($data) {
                            return '<span class="btn btn-sm btn-primary">' . $data->pl_code . '</span>';
                        })
                        ->editColumn('action', function ($data) use ($pl_ids) {
                            $check_pst = ProductLocationSetup::select('product_location_setups.id as pls_id', 'product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                                ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                                ->whereIn('product_location_setups.pl_id', $pl_ids)
                                ->where('product_stocks.p_id', '=', $data->p_id)
                                ->get();

                            if (!empty($check_pst)) {
                                $action = '';
                                foreach ($check_pst as $row) {
                                    $initial_pst_get = TempMutasi::where('ps_barcode', $row->ps_barcode)->where('u_id', Auth::user()->id)->first();
                                    $initial_pst = $initial_pst_get ? $initial_pst_get->pls_qty : "";
                                    $initial_note = $initial_pst_get ? $initial_pst_get->notes : "";

                                    $this->table_row += 1;
                                    $action .= '
                                <div class="d-flex align-items-center mb-2">
                                    <input data-mutation-qty data-qty="' . $row->pls_qty . '" id="mutation_qty" type="text" class="form-control mutation_qty' . $this->table_row . '" style="padding:10px; margin-right:5px; width: 50%; min-width:150px;" value="' . $initial_pst . '" title="' . $data->p_name . ' ' . $data->p_color . ' ' . $row->sz_name . '"/>
                                    <input type="text" class="form-control mutation_note' . $this->table_row . '" style="padding:10px; width: 50%; min-width:150px;" placeholder="Note" title="Note" value="' . $initial_note . '"/>
                                </div>
                                <i class="fa fa-eye d-none" onclick="return saveMutation(' . $row->pls_id . ', ' . $this->table_row . ', ' . $row->pst_id . ', ' . $row->pls_qty . ', document.querySelector(\'.mutation_note' . $this->table_row . '\').value)" id="saveMutation' . $this->table_row . '"></i>';
                                }
                                return $action;
                            } else {
                                return '-';
                            }
                        })
                        ->rawColumns(['article', 'qty', 'bin_code', 'action'])
                        ->filter(function ($instance) use ($request) {
                            if (!empty($request->get('search'))) {
                                $instance->where(function ($w) use ($request) {
                                    $search = $request->get('search');
                                    $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
                                        ->orWhere('p_name', 'LIKE', "%$search%")
                                        ->orWhere('br_name', 'LIKE', "%$search%")
                                        ->orWhere('article_id', 'LIKE', "%$search%")
                                        ->orWhere('product_stocks.ps_barcode', '=', "$search")
                                        ->orWhere('p_color', 'LIKE', "%$search%");
                                });
                            }
                        })
                        ->addIndexColumn()
                        ->make(true);
                    }
        }
    }

    public function importData(Request $request)
    {
        $check_temp = TempMutasi::where('u_id', Auth::user()->id)->count();
        if ($check_temp > 0) {
            $r['status'] = '409';
            $r['message'] = 'Anda sudah melakukan import data, silahkan hapus data terlebih dahulu sebelum melakukan import ulang.';
            return json_encode($r);
        }
        try {
            if ($request->hasFile('importFile')) {
                $file = $request->file('importFile');

                $nama_file = rand() . $file->getClientOriginalName();

                $file->move('excel', $nama_file);

                $import = new StockLocationImport();
                $data = Excel::toArray($import, public_path('excel/' . $nama_file));

                if (count($data) >= 0) {
                    $processData = $this->processImportData($data[0]);
                    $r['data'] = $processData;
                    $r['status'] = '200';
                } else {
                    $r['status'] = '419';
                }
            } else {
                $r['status'] = '400';
            }

            unlink(public_path('excel/' . $nama_file));

            return json_encode($r);
        } catch (\Exception $e) {
            unlink(public_path('excel/' . $nama_file));
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
            return json_encode($r);
        }
    }

    public function cancelImportData(Request $request)
    {
        $delete = TempMutasi::where('u_id', Auth::user()->id)->delete();

        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
            $r['message'] = 'Error';
        }
        return json_encode($r);
    }

    private function processImportData($data)
    {
        DB::beginTransaction();

        try {
            $processedData = [];
            $missingBarcode = array();

            foreach ($data as $item) {
                $barcode = $item[1];
                $start_bin = $item[0];
                $qty = $item[2];
                $notes = $item[3];
                // get id from barcode
                $product_id = ProductStock::where('ps_barcode', '=', $barcode)->get()->first();

                if (!$product_id) {
                    $missingBarcode[] = [$start_bin, $barcode];
                    continue; // Skip to the next item if product_id is not found
                }

                $start_bin_id = ProductLocation::where('pl_code', '=', $start_bin)->where('st_id', Auth::user()->st_id)->get()->first();
                $pls_id = ProductLocationSetup::where('pst_id', '=', $product_id->id)
                    ->where('pl_id', '=', $start_bin_id->id)->get()->first();

                if (!empty($pls_id)) {
                    // Check if barcode already exists in processedData
                    $existingKey = array_search($barcode, array_column($processedData, 'barcode'));

                    if ($existingKey !== false) {
                        // If barcode exists, add the quantity to the existing entry
                        $processedData[$existingKey]['qty'] += $qty;
                    } else {
                        // If barcode doesn't exist, create a new entry
                        $rowData = [
                            'u_id' => Auth::user()->id,
                            'product_stock_id' => $product_id->id,
                            'barcode' => $barcode,
                            'qty' => $qty,
                        ];
                        $processedData[] = $rowData;

                        $params = [
                            'u_id' => Auth::user()->id,
                            'pls_id' => $pls_id->id,
                            'ps_barcode' => $barcode,
                            'pls_qty' => $qty,
                            'notes' => $notes,
                        ];
                        TempMutasi::create($params);
                    }
                } else {
                    $missingBarcode[] = [$start_bin, $barcode];
                }
            }

            DB::commit();

            return [
                'processedData' => $processedData,
                'missingBarcode' => $missingBarcode
            ];
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function endBinDatatables(Request $request)
    {
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }

        $st_city = Store::select('st_code')->where('id', '=', Auth::user()->st_id)->first();
        if (request()->ajax()) {
            return datatables()->of(ProductLocationSetup::select('product_location_setups.id as pls_id', 'products.id as p_id', 'br_name', 'p_name', 'p_color', 'sz_name', 'mc_name', 'pls_qty', 'ps_barcode')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
                ->where('pl_id', '=', $request->pl_id)
                ->where('product_locations.pl_code', '!=', 'TRIAL')
                ->where(function ($w) use ($st_id, $st_city) {
                    //                    $w->where('product_locations.st_id', '=', $st_id);
                    $w->where('product_locations.pl_description', '=', $st_city->st_code);
                })
                //                ->where('pls_qty', '>', '0')
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
                        //                        ->where('pls_qty', '>', 0)
                        ->get();
                    if (!empty($check_pst)) {
                        $sz_name = '';
                        foreach ($check_pst as $row) {
                            if (!empty($row->ps_barcode)) {
                                $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-3" style="white-space: nowrap;">' . $row->sz_name . '</a> <a class="btn btn-sm btn-primary col-2" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->pls_qty . '</a> <a style="white-space: nowrap;" class="btn btn-sm btn-primary col-7" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->ps_barcode . '</a></div>';
                            } else {
                                $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-4" style="white-space: nowrap;">' . $row->sz_name . '</a> <a class="btn btn-sm btn-primary col-4" onclick="return mutation(' . $row->pst_id . ', ' . $request->_pl_id . ', \'' . $data->p_name . '\', \'' . $data->p_color . '\', \'' . $row->sz_name . '\', ' . $row->pls_qty . ')">' . $row->pls_qty . '</a></div>';
                            }
                        }
                        return $sz_name;
                    } else {
                        return 'Data belum disetup';
                    }
                })
                ->rawColumns(['article', 'qty'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
                                ->orWhere('p_name', 'LIKE', "%$search%")
                                ->orWhere('br_name', 'LIKE', "%$search%")
                                ->orWhere('p_color', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function binHistoryDatatables(Request $request)
    {

        if (request()->ajax()) {
            return datatables()->of(ProductMutation::select('product_mutations.id as pmt_id', 'ps_barcode', 'st_name', 'p_name', 'u_name', 'pmt_old_qty', 'pmt_qty', 'u_id', 'pls_id', 'product_mutations.pl_id as pl_id', 'product_mutations.created_at as pm_created_at','product_mutations.notes')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_mutations.pl_id')
                ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
                ->leftJoin('users', 'users.id', '=', 'product_mutations.u_id')
                ->join('product_location_setups', 'product_mutations.pls_id', '=',  'product_location_setups.id')
                ->join('product_stocks', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                ->join('products', 'products.id', '=', 'product_stocks.p_id')
                ->where(function ($query) use ($request) {
                    if (!empty($request->get('history_bin_start'))) {
                        $query->where('product_location_setups.pl_id', '=', $request->get('history_bin_start'));
                    }
                })
                ->where(function ($query) use ($request) {
                    if (!empty($request->get('history_bin_end'))) {
                        $query->where('product_mutations.pl_id', '=', $request->get('history_bin_end'));
                    }
                }))
                ->editColumn('article', function ($data) {
                    $article = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                        ->where('product_location_setups.id', $data->pls_id)
                        ->get()->first();
                    if (!empty($article)) {
                        return '<span class="btn btn-sm btn-primary" style="white-space: nowrap;">[' . $article->br_name . '] ' . $article->p_name . ' ' . $article->p_color . ' ' . $article->sz_name . '</span>';
                    } else {
                        return '-';
                    }
                })
                ->editColumn('pmt_old_qty_new', function ($data) {
                    if (!empty($data->pmt_old_qty)) {
                        return $data->pmt_old_qty - $data->pmt_qty;
                    } else {
                        return '-';
                    }
                })
                ->editColumn('pmt_qty', function ($data) {
                    return '<span class="btn btn-sm btn-success">' . $data->pmt_qty . '</span>';
                })
                ->editColumn('start_bin', function ($data) {
                    $start_bin = ProductLocationSetup::select('pl_code')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('product_location_setups.id', $data->pls_id)->get()->first();
                    if (!empty($start_bin)) {
                        return '<span class="btn btn-sm btn-primary">' . $start_bin->pl_code . '</span>';
                    } else {
                        return '-';
                    }
                })
                ->editColumn('end_bin', function ($data) {
                    $end_bin = ProductLocation::select('pl_code')->where('id', $data->pl_id)->get()->first();
                    if (!empty($end_bin)) {
                        return '<span class="btn btn-sm btn-primary">' . $end_bin->pl_code . '</span>';
                    } else {
                        return '-';
                    }
                })
                ->editColumn('pm_created_at', function ($data) {
                    return date('d-m-Y H:i:s', strtotime($data->pm_created_at));
                })
                ->rawColumns(['article', 'start_bin', 'end_bin', 'pmt_qty'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('st_id'))) {
                        $instance->where(function ($w) use ($request) {
                            $st_id = $request->get('st_id');
                            $w->where('product_locations.st_id', '=', $st_id);
                        });
                    }
                    if (!empty($request->get('date'))) {
                        $instance->where(function ($w) use ($request) {
                            $date = $request->get('date');
                            $start = null;
                            $end = null;
                            $exp = explode('|', $date);
                            if (count($exp) > 1) {
                                if ($exp[0] != $exp[1]) {
                                    $start = $exp[0];
                                    $end = $exp[1];
                                } else {
                                    $start = $exp[0];
                                }
                            } else {
                                $start = $date;
                            }
                            if (!empty($end)) {
                                $w->whereDate('product_mutations.created_at', '>=', $start)
                                    ->whereDate('product_mutations.created_at', '<=', $end);
                            } else {
                                $w->whereDate('product_mutations.created_at', $start);
                            }
                        });
                    }
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('product_stocks.ps_barcode', 'LIKE', "%$search%")
                                ->orWhere('users.u_name', 'LIKE', "%$search%")
                                ->orWhere('products.p_name', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function productMutation(Request $request)
    {
        $pls_id = $request->_pls_id;
        $pst_id = $request->_pst_id;
        $pmt_old_qty = $request->_pmt_old_qty;
        $pmt_qty = $request->_pmt_qty;
        $pl_id_end = $request->_pl_id_end;
        $note = $request->_note;

        $u_id = Auth::user()->id;

        DB::beginTransaction();
        try {
            // Delete Temp Data with where condition
            TempMutasi::where('u_id', $u_id)->delete();

            $check_destination = ProductLocationSetup::where(['pl_id' => $pl_id_end, 'pst_id' => $pst_id])->exists();
            if ($check_destination) {
                $or_qty = ProductLocationSetup::select('pls_qty')->where(['id' => $pls_id])->get()->first()->pls_qty;
                if ($pmt_qty > $or_qty) {
                    $r['status'] = '400';
                    $r['message'] = 'Qty mutasi lebih besar dari qty asal';
                    DB::rollBack();
                    return json_encode($r);
                }

                $data_destination = ProductLocationSetup::where(['pl_id' => $pl_id_end, 'pst_id' => $pst_id])->get()->first();
                $qty_destination = $data_destination->pls_qty;
                $update_data_destination = [
                    'pls_qty' => $pmt_qty + $qty_destination
                ];
                $update_destination = ProductLocationSetup::where(['pl_id' => $pl_id_end, 'pst_id' => $pst_id])->update($update_data_destination);
                if (!empty($update_destination)) {
                    $data_origin = ProductLocationSetup::select('pls_qty')->where(['id' => $pls_id])->get()->first();
                    $qty_origin = $data_origin->pls_qty;
                    $remain = $qty_origin - $pmt_qty;
                    $update_data_origin = [
                        'pls_qty' => $remain
                    ];
                    $update_origin = ProductLocationSetup::where(['id' => $pls_id])->update($update_data_origin);
                    if (!empty($update_origin)) {
                        $mutation = ProductMutation::create([
                            'pls_id' => $pls_id,
                            'pl_id' => $pl_id_end,
                            'u_id' => Auth::user()->id,
                            'pmt_old_qty' => $pmt_old_qty,
                            'pmt_qty' => $pmt_qty,
                            'notes' => $note,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                        if (!empty($mutation)) {
                            $r['status'] = '200';
                            $r['message'] = 'Mutasi berhasil';
                        } else {
                            $r['status'] = '400';
                            $r['message'] = 'Gagal menyimpan mutasi';
                        }
                    } else {
                        $r['status'] = '400';
                        $r['message'] = 'Gagal update qty asal';
                    }
                } else {
                    $r['status'] = '400';
                    $r['message'] = 'Gagal update qty tujuan';
                }
            } else {
                $or_qty = ProductLocationSetup::select('pls_qty')->where(['id' => $pls_id])->get()->first()->pls_qty;
                if ($pmt_qty > $or_qty) {
                    $r['status'] = '400';
                    $r['message'] = 'Qty mutasi lebih besar dari qty asal';
                    DB::rollBack();
                    return json_encode($r);
                }
                $insert_data_destination = [
                    'pls_qty' => $pmt_qty,
                    'pl_id' => $pl_id_end,
                    'pst_id' => $pst_id,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $insert_destination = ProductLocationSetup::create($insert_data_destination);
                if (!empty($insert_destination)) {
                    $data_origin = ProductLocationSetup::select('pls_qty')->where(['id' => $pls_id])->get()->first();
                    $qty_origin = $data_origin->pls_qty;
                    $remain = $qty_origin - $pmt_qty;
                    $update_data_origin = [
                        'pls_qty' => $remain
                    ];
                    $update_origin = ProductLocationSetup::where(['id' => $pls_id])->update($update_data_origin);
                    if (!empty($update_origin)) {
                        $mutation = ProductMutation::create([
                            'pls_id' => $pls_id,
                            'pl_id' => $pl_id_end,
                            'u_id' => Auth::user()->id,
                            'pmt_old_qty' => $pmt_old_qty,
                            'pmt_qty' => $pmt_qty,
                            'notes' => $note,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                        if (!empty($mutation)) {
                            $r['status'] = '200';
                            $r['message'] = 'Mutasi berhasil';
                        } else {
                            $r['status'] = '400';
                            $r['message'] = 'Gagal menyimpan mutasi';
                        }
                    } else {
                        $r['status'] = '400';
                        $r['message'] = 'Gagal update qty asal';
                    }
                } else {
                    $r['status'] = '400';
                    $r['message'] = 'Gagal insert qty tujuan';
                }
            }

            if ($r['status'] === '200') {
                DB::commit();
            } else {
                DB::rollBack();
            }
            return json_encode($r);
        } catch (\Exception $e) {
            DB::rollBack();
            $r['status'] = '400';
            $r['message'] = $e->getMessage();
            return json_encode($r);
        }
    }

    private function checkAccess()
    {
        $r = 0;
        $check = DB::table('instock_exception_approvals')
            ->where('exception_u_id_1', '=', Auth::user()->id)
            ->orWhere('exception_u_id_2', '=', Auth::user()->id)
            ->exists();
        if ($check) {
            $r = 1;
        }
        return $r;
    }

    public function reloadStartBin()
    {
        $access = $this->checkAccess();

        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

        $st_city = Store::select('st_code')->where('id', '=', Auth::user()->st_id)->first();
        $pl_id = ProductLocation::selectRaw('ts_product_locations.id as pl_id, CONCAT(pl_code," (",st_name,")") as location')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->where('pl_delete', '!=', '1')
            ->where(function ($w) use ($exception, $access, $st_city) {
//                $w->where('product_locations.st_id', '=', Auth::user()->st_id);
                $w->where('product_locations.pl_description', '=', $st_city->st_code);
            })
            ->orderByDesc('pl_code')->pluck('location', 'pl_id');

        $data = [
            'pl_id' => $pl_id
        ];
        return view('app.product_location_setup_v2._start', compact('data'));
    }

    public function reloadEndBin()
    {
        $access = $this->checkAccess();

        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

        $st_city = Store::select('st_code')->where('id', '=', Auth::user()->st_id)->first();
        $pl_id = ProductLocation::selectRaw('ts_product_locations.id as pl_id, CONCAT(pl_code," (",st_name,")") as location')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->where('pl_delete', '!=', '1')
            ->where(function ($w) use ($exception, $access, $st_city) {
//                $w->where('product_locations.st_id', '=', Auth::user()->st_id);
                $w->where('product_locations.pl_description', '=', $st_city->st_code);
            })
            ->orderByDesc('pl_code')->pluck('location', 'pl_id');

        $data = [
            'pl_id' => $pl_id,
        ];
        return view('app.product_location_setup_v2._end', compact('data'));
    }

    public function reloadHistoryStartBin()
    {
        $access = $this->checkAccess();

        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

        $pl_id = ProductLocation::selectRaw('ts_product_locations.id as pl_id, CONCAT(pl_code," (",st_name,")") as location')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->where('pl_delete', '!=', '1')
            ->orderByDesc('pl_code')->pluck('location', 'pl_id');

        $data = [
            'pl_id' => $pl_id
        ];
        return view('app.product_location_setup_v2._history_start', compact('data'));
    }

    public function reloadHistoryEndBin()
    {
        $access = $this->checkAccess();

        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();
        $pl_id = ProductLocation::selectRaw('ts_product_locations.id as pl_id, CONCAT(pl_code," (",st_name,")") as location')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->where('pl_delete', '!=', '1')
            ->orderByDesc('pl_code')->pluck('location', 'pl_id');

        $data = [
            'pl_id' => $pl_id,
        ];
        return view('app.product_location_setup_v2._history_end', compact('data'));
    }

    public function exportData(Request $request)
    {
        $st_id = $request->get('st_id');
        $date = $request->get('date');
        $search = $request->get('search');
    
        // Tangani string "undefined" dari frontend
        $history_bin_start = $request->get('history_bin_start');
        $history_bin_start = ($history_bin_start === 'undefined' || $history_bin_start === '') ? null : $history_bin_start;
    
        $history_bin_end = $request->get('history_bin_end');
        $history_bin_end = ($history_bin_end === 'undefined' || $history_bin_end === '') ? null : $history_bin_end;
    
        $start = null;
        $end = null;
    
        $exp = explode('|', $date);
        if (count($exp) > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            $start = $date;
        }
    
        $fileName = 'setup_history_' . date('Y_m_d_H_i_s') . '.xlsx';
    
        return Excel::download(
            new SetupHistoryExport($st_id, $start, $end, $search, $history_bin_start, $history_bin_end),
            $fileName
        );
    }
    

    public function importMultiBinsMutation(Request $request)
    {
        $check_temp = TempMutasi::where('u_id', Auth::user()->id)->count();
        if ($check_temp > 0) {
            $r['status'] = '409';
            $r['message'] = 'Anda sudah melakukan import data, silahkan hapus data terlebih dahulu sebelum melakukan import ulang.';
            return json_encode($r);
        }

        $import_data = Excel::toArray(new TempMultiBinMutationImport, $request->file('mutationInput'));

        if (isset($import_data[0][0]) && $import_data[0][0] === ["BIN AWAL", "BIN TUJUAN", "SKU", "QUANTITY", "NOTES"]) {
            array_shift($import_data[0]); // Remove the header row
            $data = $this->processImportMultiBinData($import_data[0]);

            if (count($data['missingBarcode']) != 0 || count($data['missingBins']) != 0 || count($data['missingItemsOnBin']) != 0 || count($data['qtyInvalid']) != 0) {
                $r['status'] = '406';
                $r['message'] = 'Data tidak lengkap atau ada kesalahan dalam format data yang diimpor.';
                $r['missingBarcode'] = $data['missingBarcode'];
                $r['missingBins'] = $data['missingBins'];
                $r['missingItemsOnBin'] = $data['missingItemsOnBin'];
                $r['qtyInvalid'] = $data['qtyInvalid'];
                return json_encode($r);
            }

            DB::beginTransaction();
            try {
                TempMutasi::insert($data['processedData']);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $r['status'] = '500';
                $r['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
                return json_encode($r);
            }

        } else {
            $r['status'] = '400';
            $r['message'] = 'Format data tidak sesuai';
            return json_encode($r);
        }

        $r['status'] = '200';
        $r['message'] = 'Data berhasil diimport';
        return json_encode($r);
    }

    private function processImportMultiBinData($data)
    {
        $processedData = [];
        $missingBarcode = array();
        $missingBins = array();
        $missingItemsOnBin = array();
        $qtyInvalid = array();

        foreach ($data as $item) {
            $start_bin = $item[0];
            $end_bin = $item[1];
            $barcode = $item[2];
            $qty = $item[3];
            $notes = isset($item[4]) ? $item[4] : null;
            // get id from barcode
            $product_id = ProductStock::where('ps_barcode', '=', $barcode)->get()->first();

            if (empty($product_id)) {
                $missingBarcode[] = [$start_bin, $barcode];
                continue; // Skip to the next item if barcode is not found
            }

            $start_bin_id = ProductLocation::where('pl_code', '=', $start_bin)->where('st_id', Auth::user()->st_id)->get()->first();
            if (empty($start_bin_id)) {
                if (!in_array([$start_bin, $barcode], $missingBins)) {
                    $missingBins[] = [$start_bin, $barcode];
                }
                continue; // Skip to the next item if start bin is not found
            }

            $end_bin_id = ProductLocation::where('pl_code', '=', $end_bin)->where('st_id', Auth::user()->st_id)->get()->first();
            if (empty($end_bin_id)) {
                if (!in_array([$end_bin, $barcode], $missingBins)) {
                    $missingBins[] = [$end_bin, $barcode];
                }
                continue; // Skip to the next item if end bin is not found
            }

            $pls_id = ProductLocationSetup::where('pst_id', '=', $product_id->id)
                ->where('pl_id', '=', $start_bin_id->id)->get()->first();

            if (empty($pls_id)) {
                if (!in_array([$start_bin, $barcode], $missingItemsOnBin)) {
                    $missingItemsOnBin[] = [$start_bin, $barcode];
                }
                continue; // Skip to the next item if product is not found in start bin
            }


            if (!empty($pls_id)) {
                if ($pls_id->pls_qty < $qty) {
                    $qtyInvalid[] = [$start_bin, $barcode, $qty, $pls_id->pls_qty];
                }
                // If barcode doesn't exist, create a new entry
                $params = [
                    'u_id' => Auth::user()->id,
                    'pls_id' => $pls_id->id,
                    'pl_end' => $end_bin_id->id,
                    'ps_barcode' => $barcode,
                    'pls_qty' => $qty,
                    'notes' => $notes
                ];
                $processedData[] = $params;
            }
        }

        return [
            'processedData' => $processedData,
            'missingBarcode' => $missingBarcode,
            'missingBins' => $missingBins,
            'missingItemsOnBin' => $missingItemsOnBin,
            'qtyInvalid' => $qtyInvalid
        ];
    }

    public function tempMultiBinsDatatable(Request $request)
    {
        if (request()->ajax()) {
            $data = TempMutasi::select(
                'product_location_setups.id as pls_id',
                'temp_mutasi.ps_barcode',
                'product_locations.pl_code as start_bin',
                'destination_locations.pl_code as end_bin',
                'product_location_setups.pls_qty as start_qty',
                'temp_mutasi.pls_qty as mutation_qty',
                'temp_mutasi.notes'
            )
                ->where('temp_mutasi.u_id', Auth::user()->id)
                ->join('product_location_setups', 'product_location_setups.id', '=', 'temp_mutasi.pls_id')
                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->join('product_locations as destination_locations', 'destination_locations.id', '=', 'temp_mutasi.pl_end')
                ->get();

            return datatables()->of($data)
                ->editColumn('pls_qty', function ($data) {
                    return '<span class="btn btn-sm btn-primary">' . $data->pls_qty . '</span>';
                })
                ->editColumn('ps_barcode', function ($data) {
                    return '<span class="btn btn-sm btn-primary">' . $data->ps_barcode . '</span>';
                })
                ->rawColumns(['pls_qty', 'ps_barcode'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function multibinsMutation()
    {
        $datas = TempMutasi::query()->where('u_id', Auth::user()->id)->get();
        $processedData = [];
        $missingBins = [];
        $missingItemsOnBin = [];
        $qtyInvalid = [];

        if ($datas->isEmpty()) {
            return response()->json([
                'status' => '404',
                'message' => 'Tidak ada data mutasi yang ditemukan'
            ]);
        }

        foreach ($datas as $data) {
            $pls = ProductLocationSetup::find($data->pls_id);
            if (!$pls) {
                $missingItemsOnBin[] = ['pls_id' => $data->pls_id, 'sku' => $data->ps_barcode, 'qty' => $data->pls_qty];
                continue; // Skip to the next iteration if ProductLocationSetup not found
            }

            $pl = ProductLocation::find($data->pl_end);
            if (!$pl) {
                $missingBins[] = ['pl_id' => $data->pl_end, 'sku' => $data->ps_barcode];
                continue; // Skip if ProductLocation not found
            }

            if ($pls->pls_qty < $data->pls_qty) {
                $pl_code = ProductLocation::find($pls->pl_id)->pl_code ?? 'Unknown';
                $qtyInvalid[] = ['pl_code' => $pl_code, 'sku' => $data->ps_barcode, 'qty_mutasi' =>  $data->pls_qty, 'qty_available' => $pls->pls_qty];
                continue; // Skip if quantity is invalid
            }

            $processedData[] = [
                'pls_id' => $data->pls_id,
                'pl_end' => $data->pl_end,
                'ps_barcode' => $data->ps_barcode,
                'pls_qty' => $data->pls_qty,
                'notes' => $data->notes ?: null,
            ];
        }
        if (!empty($missingBins) || !empty($missingItemsOnBin) || !empty($qtyInvalid)) {
            return response()->json([
                'status' => '400',
                'message' => 'Data mutasi ada yang bermasalah, silahkan perbaiki data dan import ulang',
                'missingBins' => $missingBins,
                'missingItemsOnBin' => $missingItemsOnBin,
                'qtyInvalid' => $qtyInvalid
            ]);
        }

        DB::beginTransaction();

        try {
            foreach ($processedData as $data) {
                $pls = ProductLocationSetup::find($data['pls_id']);
                if ($pls) {
                    $pls->update(['pls_qty' => $pls->pls_qty - $data['pls_qty']]);
                } else {
                    return response()->json(['status' => '404', 'message' => 'Data setup tidak ditemukan untuk SKU: ' . $data['ps_barcode']]);
                }

                $plEnd = ProductLocation::find($data['pl_end']);
                if ($plEnd) {
                    $setup = ProductLocationSetup::where('pl_id', $plEnd->id)
                        ->where('pst_id', $pls->pst_id)
                        ->first();

                    if ($setup) {
                        $setup->update([
                            'pls_qty' => $setup->pls_qty + $data['pls_qty']
                        ]);
                    } else {
                        ProductLocationSetup::create([
                            'pl_id' => $plEnd->id,
                            'pst_id' => $pls->pst_id,
                            'pls_qty' => $data['pls_qty']
                        ]);
                    }
                } else {
                    return response()->json(['status' => '404', 'message' => 'Lokasi tujuan tidak ditemukan untuk SKU: ' . $data['ps_barcode']]);
                }

                ProductMutation::create([
                    'pls_id' => $data['pls_id'],
                    'pl_id' => $data['pl_end'],
                    'u_id' => Auth::user()->id,
                    'pmt_old_qty' => $pls->pls_qty + $data['pls_qty'],
                    'pmt_qty' => $data['pls_qty'],
                    'notes' => $data['notes'],
                    'created_at' => now()
                ]);
            }

            TempMutasi::where('u_id', Auth::user()->id)->delete();
            DB::commit();
            return response()->json(['status' => '200', 'message' => 'Mutasi berhasil dilakukan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => '500', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
