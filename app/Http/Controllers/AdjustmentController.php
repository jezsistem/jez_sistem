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
        if(request()->ajax()) {
            return datatables()->of(BinAdjustment::select('bin_adjustments.id as ba_id', 'pls_id', 'st_name', 'pl_code', 'u_name', 'br_name', 'p_name', 'p_color', 'sz_name', 'ba_code', 'ba_note', 'ba_old_qty', 'ba_new_qty', 'ba_adjust', 'ba_adjust_type', 'bin_adjustments.created_at as ba_created')
            ->leftJoin('users', 'users.id', '=', 'bin_adjustments.u_id')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'bin_adjustments.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id'))
            ->editColumn('pl_code', function($data){
                return '<span class="btn btn-sm btn-primary">'.$data->pl_code.'</span>';
            })
            ->editColumn('article', function($data){
                return '<span class="btn btn-sm btn-primary" style="white-space: nowrap;">['.$data->br_name.'] '.$data->p_name.' '.$data->p_color.' '.$data->sz_name.'</span>';
            })
            ->editColumn('ba_created', function($data){
                return date('d-m-Y H:i:s', strtotime($data->ba_created));
            })
            ->editColumn('adjust', function($data){
                if ($data->ba_adjust_type == '+') {
                    return '<span class="btn btn-sm btn-success" style="white-space: nowrap;">'.$data->ba_adjust_type.' '.$data->ba_adjust.'</span>';
                } else {
                    return '<span class="btn btn-sm btn-danger" style="white-space: nowrap;">'.$data->ba_adjust_type.' '.$data->ba_adjust.'</span>';
                }
            })
            ->rawColumns(['pl_code', 'article', 'adjust'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
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
        if(request()->ajax()) {
            return datatables()->of(ProductLocationSetup::select('product_location_setups.id as pls_id', 'products.id as p_id', 'br_name', 'p_name', 'p_color', 'sz_name', 'mc_name', 'pls_qty', 'ps_barcode')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('main_colors', 'main_colors.id', '=', 'products.mc_id')
            ->where('pls_qty', '>', '0')
            ->where('pl_id', '=', $request->pl_id)
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
                            $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-3" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')" style="white-space: nowrap;">'.$row->sz_name.'</a> <a class="btn btn-sm btn-primary col-2" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')">'.$row->pls_qty.'</a> <a style="white-space: nowrap;" class="btn btn-sm btn-primary col-7" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')">'.$row->ps_barcode.'</a></div>';
                        } else {
                            $sz_name .= '<div class="pb-2" style="white-space: nowrap;"><a class="btn btn-sm btn-primary col-6" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')" style="white-space: nowrap;">'.$row->sz_name.'</a> <a class="btn btn-sm btn-primary col-6" onclick="return mutation('.$row->pst_id.', '.$request->_pl_id.', \''.$data->p_name.'\', \''.$data->p_color.'\', \''.$row->sz_name.'\', '.$row->pls_qty.')">'.$row->pls_qty.'</a></div>';
                        }
                    }
                    return $sz_name;
                } else {
                    return 'Data belum disetup';
                }
            })
            ->editColumn('action', function($data) use ($request) {
                $check_pst = ProductLocationSetup::select('product_location_setups.id as pls_id', 'product_stocks.id as pst_id', 'product_locations.id as pl_id', 'sz_name', 'pls_qty', 'ps_barcode')
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
                        <div class="row">
                        <input data-adjustment-qty data-qty="'.$row->pls_qty.'" id="adjustment_qty" type="number" class="form-control col-6 adjustment_qty'.$this->table_row.'" style="padding:10px; margin-bottom:2px; width:100px;" value="" title="'.$data->p_name.' '.$data->p_color.' '.$row->sz_name.'" placeholder="qty"/>
                        <input class="form-control col-6 adjustment_note'.$this->table_row.'" type="text" id="ba_note" placeholder="catatan"/>
                        <i class="fa fa-eye d-none" onclick="return saveAdjustment('.$row->pls_id.', '.$this->table_row.', '.$row->pst_id.', '.$row->pls_qty.', '.$row->pl_id.')" id="saveAdjustment'.$this->table_row.'"></i>
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
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('p_name', 'LIKE', "%$search%")
                        ->orWhere('br_name', 'LIKE', "%$search%")
                        ->orWhere('p_color', 'LIKE', "%$search%")
                        ->orWhere('sz_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function validatedDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(ProductLocation::select('product_locations.id as pl_id', 'pl_code', 'u_name')
            ->join('users', 'users.id', '=', 'product_locations.u_id_adjustment')
            ->where('pl_adjustment', '=', '1'))
            ->editColumn('pl_code', function($data){
                return '<span data-pl_id="'.$data->pl_id.'" data-pl_code="'.$data->pl_code.'" id="validated_bin" class="btn btn-sm btn-success">'.$data->pl_code.'</span>';
            })
            ->rawColumns(['pl_code'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function notValidatedDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(ProductLocation::select('id', 'pl_code', 'pl_name', 'pl_description')
            ->where('pl_adjustment', '=', '0'))
            ->editColumn('pl_code', function($data){
                return '<span class="btn btn-sm btn-primary">'.$data->pl_code.'</span>';
            })
            ->editColumn('pl_name', function($data){
                return $data->pl_name.' '.$data->pl_description;
            })
            ->rawColumns(['pl_code', 'pl_name'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function productAdjustment(Request $request)
    {
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
            $check_adjustment = ProductLocation::where(['pl_adjustment' => '1'])->exists();
            if ($check_adjustment) {
                $ba_code = BinAdjustment::select('ba_code')->orderByDesc('id')->limit(1)->get()->first()->ba_code;
                if (empty($ba_code)) {
                    $ba_code = 'ADJ'.date('YmdHis');
                } else {
                    $ba_code = $ba_code;
                }
            } else {
                $ba_code = 'ADJ'.date('YmdHis');
            }
            $bin_history = BinAdjustment::create([
                'pls_id' => $pls_id,
                'u_id' => Auth::user()->id,
                'ba_code' => $ba_code,
                'ba_old_qty' => $pls_qty,
                'ba_new_qty' => $ba_qty,
                'ba_adjust' => $adjust_qty,
                'ba_adjust_type' => $adjust_type,
                'ba_note' => $ba_note,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            if (!empty($bin_history)) {
                $pls_update = ProductLocationSetup::where(['id' => $pls_id])->update([
                    'pls_qty' => $ba_qty
                ]);
                if (!empty($pls_update)) {
                    $product_location = ProductLocation::where(['id' => $pl_id])->update([
                        'pl_adjustment' => '1',
                        'u_id_adjustment' => Auth::user()->id
                    ]);
                    if (!empty($product_location)) {
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '420';
                    }
                }
            } else {
                $r['status'] = '400';
            }
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
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
            $check_adjustment = ProductLocation::where(['pl_adjustment' => '1'])->exists();
            if ($check_adjustment) {
                $ba_code = BinAdjustment::select('ba_code')->orderByDesc('id')->limit(1)->get()->first()->ba_code;
                if (empty($ba_code)) {
                    $ba_code = 'ADJ'.date('YmdHis');
                } else {
                    $ba_code = $ba_code;
                }
            } else {
                $ba_code = 'ADJ'.date('YmdHis');
            }
            $bin_history = BinAdjustment::create([
                'pls_id' => $pls_id,
                'u_id' => Auth::user()->id,
                'ba_code' => $ba_code,
                'ba_old_qty' => $pls_current_qty,
                'ba_new_qty' => $pls_current_qty+$pls_qty,
                'ba_adjust' => $pls_qty,
                'ba_adjust_type' => '+',
                'ba_note' => $article_note,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            if (!empty($bin_history)) {
                $pls_update = ProductLocationSetup::where(['id' => $pls_id])->update([
                    'pls_qty' => $pls_current_qty+$pls_qty
                ]);
                if (!empty($pls_update)) {
                    $r['status'] = '200';
                }
            } else {
                $r['status'] = '400';
            }
        } else {
            $insert_id = DB::table('product_location_setups')->insertGetId([
                'pst_id' => $pst_id,
                'pl_id' => $pl_id,
                'pls_qty' => $pls_qty,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            if (!empty($insert_id)) {
                $check_adjustment = ProductLocation::where(['pl_adjustment' => '1'])->exists();
                if ($check_adjustment) {
                    $ba_code = BinAdjustment::select('ba_code')->orderByDesc('id')->limit(1)->get()->first()->ba_code;
                    if (empty($ba_code)) {
                        $ba_code = 'ADJ'.date('YmdHis');
                    } else {
                        $ba_code = $ba_code;
                    }
                } else {
                    $ba_code = 'ADJ'.date('YmdHis');
                }
                $bin_history = BinAdjustment::create([
                    'pls_id' => $insert_id,
                    'u_id' => Auth::user()->id,
                    'ba_code' => $ba_code,
                    'ba_old_qty' => '0',
                    'ba_new_qty' => $pls_qty,
                    'ba_adjust' => $pls_qty,
                    'ba_adjust_type' => '+',
                    'ba_note' => $article_note,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                if (!empty($bin_history)) {
                    $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                    ->where('product_stocks.id', $pst_id)
                    ->get()->first();
                    $this->UserActivity('menambah artikel ['.$item->br_name.'] '.$item->p_name.' '.$item->p_color.' '.$item->sz_name.' pada BIN '.$bin);
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
        if($request->get('query'))
        {
            $query = $request->get('query');
            $type = $request->get('type');
            $data = ProductStock::selectRaw('ts_product_stocks.id as pst_id, CONCAT("[",br_name,"] ", p_name," ",p_color," ",sz_name) as p_name')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('p_delete', '!=', '1')
            ->whereRaw('CONCAT(p_name," ", p_color," ", sz_name) LIKE ?', "%$query%")
            ->orderBy('p_name')
            ->limit(10)
            ->get();
            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach($data as $row) {
                    $output .= '
                    <li><a class="btn btn-sm btn-inventory col-12" data-pst_id="'.$row->pst_id.'" data-p_name="'.$row->p_name.'" id="add_to_item_list"><span class="btn-sm btn-primary">'.$row->p_name.'</span></a></li>
                    ';
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }
}
