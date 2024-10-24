<?php

namespace App\Http\Controllers;

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
        if(request()->ajax()) {
            return datatables()->of(ProductLocationSetup::select('product_location_setups.id as pls_id', 'products.id as p_id', 'br_name', 'p_name', 'p_color', 'sz_name', 'mc_name', 'pls_qty', 'ps_barcode')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id') 
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
            ->where('pl_id', '=', $request->pl_id)
            ->where('product_locations.pl_code', '!=', 'TRIAL')
            ->where(function($w) use ($st_id){
                $w->where('product_locations.st_id', '=', $st_id);
            })
            ->where('pls_qty', '>', '0')
            ->groupBy('products.id'))
            ->editColumn('article', function($data){
                $arr_name = array();
                $arr_name = explode(" ", $data->p_name);
                $display_name = "";
                foreach($arr_name AS $word){
                    $length_name = strlen($display_name) + strlen($word);
                    if($length_name >= 20){
                     $display_name .= "<br />".$word." ";
                    }else{
                     $display_name .= $word." ";
                    } 
                }
                
                $arr_color = array();
                $arr_color = explode("/", $data->p_color);
                $display_color = "";
                foreach($arr_color AS $color){
                    $length_color = strlen($display_color) + strlen($color);
                    if($length_color >= 20){
                     $display_color .= "<br />".$color." ";
                    }else{
                     $display_color .= $color." ";
                    } 
                }
                return '<span style="white-space: nowrap;">['.$data->br_name.']<br/>'.$display_name.'<br/>'.$display_color.'</span>';
            })
            ->editColumn('qty', function($data) use ($request) {
                $check_pst = ProductLocationSetup::select('product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_location_setups.pl_id', '=', $request->pl_id)
                ->where('product_stocks.p_id', '=', $data->p_id)
                ->where('pls_qty', '>', 0)
                ->get();
                if (!empty($check_pst)){
                    $sz_name = '';
                    foreach ($check_pst as $row) {
                        if (!empty($row->ps_barcode)) {
                            $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-3" style="white-space: nowrap;">'.$row->sz_name.'</a> <a class="btn btn-sm btn-primary col-2" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')">'.$row->pls_qty.'</a> <a style="white-space: nowrap;" class="btn btn-sm btn-primary col-7" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')">'.$row->ps_barcode.'</a></div>';
                        } else {
                            $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-4" style="white-space: nowrap;">'.$row->sz_name.'</a> <a class="btn btn-sm btn-primary col-4" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')">'.$row->pls_qty.'</a></div>';
                        }
                    }
                    return $sz_name;
                } else {
                    return 'Data belum disetup';
                }
            })
            ->editColumn('action', function($data) use ($request) {
                $check_pst = ProductLocationSetup::select('product_location_setups.id as pls_id', 'product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_location_setups.pl_id', '=', $request->pl_id)
                ->where('product_stocks.p_id', '=', $data->p_id)
                ->where('pls_qty', '>', 0)
                ->get();
                if (!empty($check_pst)){
                    $action = '';
                    foreach ($check_pst as $row) {
                        $this->table_row += 1;
                        $action .= '
                        <input data-mutation-qty data-qty="'.$row->pls_qty.'" id="mutation_qty" type="text" class="form-control col-12 mutation_qty'.$this->table_row.'" style="padding:10px; margin-bottom:2px;" value="" title="'.$data->p_name.' '.$data->p_color.' '.$row->sz_name.'"/>
                        <i class="fa fa-eye d-none" onclick="return saveMutation('.$row->pls_id.', '.$this->table_row.', '.$row->pst_id.', '.$row->pls_qty.')" id="saveMutation'.$this->table_row.'"></i>';
                    }
                    return $action;
                } else {
                    return '-';
                }
            })
            ->rawColumns(['article', 'qty', 'action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
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

    public function endBinDatatables(Request $request)
    {
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }
        if(request()->ajax()) {
            return datatables()->of(ProductLocationSetup::select('product_location_setups.id as pls_id', 'products.id as p_id', 'br_name', 'p_name', 'p_color', 'sz_name', 'mc_name', 'pls_qty', 'ps_barcode')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id') 
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
            ->where('pl_id', '=', $request->pl_id)
            ->where('product_locations.pl_code', '!=', 'TRIAL')
            ->where(function($w) use ($st_id){
                $w->where('product_locations.st_id', '=', $st_id);
            })
            ->where('pls_qty', '>', '0')
            ->groupBy('products.id'))
            ->editColumn('article', function($data){
                $arr_name = array();
                $arr_name = explode(" ", $data->p_name);
                $display_name = "";
                foreach($arr_name AS $word){
                    $length_name = strlen($display_name) + strlen($word);
                    if($length_name >= 20){
                     $display_name .= "<br />".$word." ";
                    }else{
                     $display_name .= $word." ";
                    } 
                }

                $arr_color = array();
                $arr_color = explode("/", $data->p_color);
                $display_color = "";
                foreach($arr_color AS $color){
                    $length_color = strlen($display_color) + strlen($color);
                    if($length_color >= 20){
                     $display_color .= "<br />".$color." ";
                    }else{
                     $display_color .= $color." ";
                    } 
                }
                return '<span style="white-space: nowrap;">['.$data->br_name.']<br/>'.$display_name.'<br/>'.$display_color.'</span>';
            })
            ->editColumn('qty', function($data) use ($request) {
                $check_pst = ProductLocationSetup::select('product_stocks.id as pst_id', 'sz_name', 'pls_qty', 'ps_barcode')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.sz_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_location_setups.pl_id', '=', $request->pl_id)
                ->where('product_stocks.p_id', '=', $data->p_id)
                ->where('pls_qty', '>', 0)
                ->get();
                if (!empty($check_pst)){
                    $sz_name = '';
                    foreach ($check_pst as $row) {
                        if (!empty($row->ps_barcode)) {
                            $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-3" style="white-space: nowrap;">'.$row->sz_name.'</a> <a class="btn btn-sm btn-primary col-2" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')">'.$row->pls_qty.'</a> <a style="white-space: nowrap;" class="btn btn-sm btn-primary col-7" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')">'.$row->ps_barcode.'</a></div>';
                        } else {
                            $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-4" style="white-space: nowrap;">'.$row->sz_name.'</a> <a class="btn btn-sm btn-primary col-4" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')">'.$row->pls_qty.'</a></div>';
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
                    $instance->where(function($w) use($request){
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

        if(request()->ajax()) {
            return datatables()->of(ProductMutation::select('product_mutations.id as pmt_id', 'st_name', 'u_name', 'pmt_old_qty', 'pmt_qty', 'u_id', 'pls_id', 'product_mutations.pl_id as pl_id', 'product_mutations.created_at as pm_created_at')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_mutations.pl_id')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->leftJoin('users', 'users.id', '=', 'product_mutations.u_id'))
            ->editColumn('article', function($data) {
                $article = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                ->where('product_location_setups.id', $data->pls_id)
                ->get()->first();
                if (!empty($article)) {
                    return '<span class="btn btn-sm btn-primary" style="white-space: nowrap;">['.$article->br_name.'] '.$article->p_name.' '.$article->p_color.' '.$article->sz_name.'</span>';
                } else {
                    return '-';
                }
            })
            ->editColumn('pmt_old_qty_new', function($data) {
                if (!empty($data->pmt_old_qty)) {
                    return  $data->pmt_old_qty - $data->pmt_qty;
                } else {
                    return  '-';
                }
            })
            ->editColumn('pmt_qty', function($data) {
                return  '<span class="btn btn-sm btn-success">'.$data->pmt_qty.'</span>';
            })
            ->editColumn('start_bin', function($data) {
                $start_bin = ProductLocationSetup::select('pl_code')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_location_setups.id', $data->pls_id)->get()->first();
                if (!empty($start_bin)) {
                    return '<span class="btn btn-sm btn-primary">'.$start_bin->pl_code.'</span>';
                } else {
                    return '-';
                }
            })
            ->editColumn('end_bin', function($data) {
                $end_bin = ProductLocation::select('pl_code')->where('id', $data->pl_id)->get()->first();
                if (!empty($end_bin)) {
                    return '<span class="btn btn-sm btn-primary">'.$end_bin->pl_code.'</span>';
                } else {
                    return '-';
                }
            })
            ->editColumn('pm_created_at', function($data) {
                return date('d-m-Y H:i:s', strtotime($data->pm_created_at));
            })
            ->rawColumns(['article', 'start_bin', 'end_bin', 'pmt_qty'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('st_id'))) {
                    $instance->where(function($w) use($request){
                        $st_id = $request->get('st_id');
                        $w->where('product_locations.st_id', '=', $st_id);
                    });
                }
                if (!empty($request->get('date'))) {
                    $instance->where(function($w) use($request){
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
                    $instance
                    ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_mutations.pls_id')
                    ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                    ->where(function($w) use($request){
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

    public function productMutation(Request $request)
    {
        $pls_id = $request->_pls_id;
        $pst_id = $request->_pst_id;
        $pmt_old_qty = $request->_pmt_old_qty;
        $pmt_qty = $request->_pmt_qty;
        $pl_id_end = $request->_pl_id_end;

        $check_destination = ProductLocationSetup::where(['pl_id' => $pl_id_end, 'pst_id' => $pst_id])->exists();
        if ($check_destination) {
            $or_qty = ProductLocationSetup::select('pls_qty')->where(['id' => $pls_id])->get()->first()->pls_qty;
            if ($pmt_qty>$or_qty) {
              $r['status'] = '400';
              return false;
            }

            $data_destination = ProductLocationSetup::where(['pl_id' => $pl_id_end, 'pst_id' => $pst_id])->get()->first();
            $qty_destination = $data_destination->pls_qty;
            if ($qty_destination < 0) {
              $qty_destination = 0;
            }
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
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    if (!empty($mutation)) {
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                } else {
                    $r['status'] = '400';
                }
            } else {
                $r['status'] = '400';
            }
        } else {
            $or_qty = ProductLocationSetup::select('pls_qty')->where(['id' => $pls_id])->get()->first()->pls_qty;
            if ($pmt_qty>$or_qty) {
              $r['status'] = '400';
              return false;
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
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    if (!empty($mutation)) {
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                } else {
                    $r['status'] = '400';
                }
            } else {
                $r['status'] = '400';
            }
        }
        return json_encode($r);
    }

    private function checkAccess() {
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

    public function reloadStartBin() {
        $access = $this->checkAccess();

        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

        $pl_id = ProductLocation::selectRaw('ts_product_locations.id as pl_id, CONCAT(pl_code," (",st_name,")") as location')
        ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
        ->where('pl_delete', '!=', '1')
        ->where(function($w) use ($exception, $access) {
            $w->where('product_locations.st_id', '=', Auth::user()->st_id);
        })
        ->orderByDesc('pl_code')->pluck('location', 'pl_id');

        $data = [
            'pl_id' => $pl_id
        ];
        return view('app.product_location_setup_v2._start', compact('data'));
    }

    public function reloadEndBin() {
        $access = $this->checkAccess();

        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

        $pl_id = ProductLocation::selectRaw('ts_product_locations.id as pl_id, CONCAT(pl_code," (",st_name,")") as location')
        ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
        ->where('pl_delete', '!=', '1')
        ->where(function($w) use ($exception, $access) {
            if ($access != 1) {
                $w->whereNotIn('pl_code', $exception);
            }
            $w->where('product_locations.st_id', '=', Auth::user()->st_id);
        })
        ->orderByDesc('pl_code')->pluck('location', 'pl_id');

        $data = [
            'pl_id' => $pl_id,
        ];
        return view('app.product_location_setup_v2._end', compact('data'));
    }

    public function exportData(Request $request) {
        $st_id = $request->get('st_id');
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
        return Excel::download(new SetupHistoryExport($st_id, $start, $end), 'history_setup_barang.xlsx');
    }
}
