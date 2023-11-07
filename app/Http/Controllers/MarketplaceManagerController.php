<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\ExceptionLocation;
use App\Imports\MPCImport;
use App\Imports\MarketplaceImport;
use App\Exports\MarketplaceExport;
use Maatwebsite\Excel\Facades\Excel;

class MarketplaceManagerController extends Controller
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
        $select = ['u_name', 'u_email', 'u_phone', 'g_name', 'stt_name'];
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
            'std_id' => DB::table('store_type_divisions')->where('dv_delete', '!=', '1')->orderBy('dv_name')->pluck('dv_name', 'id'),
            'br_id' => DB::table('brands')->where('br_delete', '!=', '1')->orderBy('br_name')->pluck('br_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.marketplace_manager.marketplace_manager', compact('data'));
    }

    public function masterDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('no_symbol_articles')
            ->select("id", "pst_id", "brand", "name", "color", "size", "size", "fullname", "brandname"))
            ->rawColumns(['marketplace_code_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('fullname', 'LIKE', "%$search%")
                        ->orWhere('pst_id', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    private function doFilter($string)
    {
        return ltrim(str_replace(array('/', '-', '<', '>', '&', '{', '}', '*'), ' ', $string));
    }

    public function fetchData() {
        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

        $data = DB::table('product_stocks')
        ->select('product_stocks.id', 'br_name', 'p_name', 'p_color', 'sz_name')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_stocks.id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->whereNotIn('product_locations.pl_code', $exception)
        ->where('products.p_delete', '!=', '1')
        ->groupBy('product_stocks.id')
        ->get();
        if (!empty($data->first())) {
            $truncate = DB::table('no_symbol_articles')->truncate();
            $temp = array();
            $total_data = count($data);
            foreach ($data as $row) {
                $br_name = $this->doFilter($row->br_name);
                $p_name = $this->doFilter($row->p_name);
                $p_color = $this->doFilter($row->p_color);
                $sz_name = $this->doFilter($row->sz_name);
                $fullname = $br_name.' '.$p_name.' '.$p_color.' '.$sz_name;
                $brandname = $br_name.' '.$p_name;
                $temp[] = [
                    'pst_id' => $row->id,
                    'brand' => $br_name,
                    'name' => $p_name,
                    'color' => $p_color,
                    'size' => $sz_name,
                    'fullname' => $fullname,
                    'brandname' => $brandname,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $total_temp = count($temp);
                if ($total_data >= 2000 AND $total_temp >= 2000) {
                    $insert = DB::table('no_symbol_articles')->insert($temp);
                    if (!empty($insert)) {
                        $total_data = $total_data - $total_temp;
                        $temp = array();
                    }
                }
                if ($total_data < 2000) {
                    $insert = DB::table('no_symbol_articles')->insert($temp);
                    if (!empty($insert)) {
                        $temp = array();
                    }
                }
            }
        }
        $r['status'] = 200;
        return json_encode($r);
    }

    public function stockDatatables(Request $request)
    {
        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();
        $code = $request->get('code');
        $st_id = $request->get('st_id');
        $std_id = $request->get('std_id');
        $br_id = $request->get('br_id');

        if(request()->ajax()) {
            return datatables()->of(DB::table('product_stocks')
            ->selectRaw("ts_product_stocks.id as pst_id, pl_code, br_name, p_name, p_color, sz_name")
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_stocks.id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where(function($w) use ($exception, $st_id, $br_id) {
                $w->whereNotIn('product_locations.pl_code', $exception);
                if ($br_id != 'all') {
                    $w->where('products.br_id', $br_id);
                }
            })
            ->where('products.p_delete', '!=', '1')
            ->groupBy('product_stocks.id'))
            ->editColumn('marketplace_code_show', function($data) use ($std_id) {
                if ($std_id == 'all') {
                    return "Silahkan pilih Marketplace Divisi";
                } else {
                    $check_value = DB::table('marketplace_managers')->select('id', 'marketplace_code', 'pst_id', 'std_id')
                    ->where('pst_id', '=', $data->pst_id)
                    ->where('std_id', '=', $std_id)->first();
                    $value = null;
                    $mm_id = null;
                    if (!empty($check_value)) {
                        $value = $check_value->marketplace_code;
                        $mm_id = $check_value->id;
                    }
                    return "<input class='form-control' placeholder='Input kode template' data-pst_id='".$data->pst_id."' data-mm_id='".$mm_id."' value='".$value."' id='marketplace_code_input'/>";
                }
            })
            ->rawColumns(['marketplace_code_show'])
            ->filter(function ($instance) use ($request, $code, $std_id) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                    });
                }
                if ($code != 'all') {
                    $instance
                    ->leftJoin('marketplace_managers', 'marketplace_managers.pst_id', '=', 'product_stocks.id')
                    ->where(function($w) use($code, $std_id){
                        if ($code == '1') {
                            $w->where('marketplace_managers.std_id', '=', $std_id)
                            ->whereNotNull('marketplace_managers.marketplace_code');
                        } else {
                            $pst_id = array();
                            $check = DB::table('marketplace_managers')->select('pst_id')
                            ->where('marketplace_managers.std_id', '=', $std_id)
                            ->whereNotNull('marketplace_managers.marketplace_code')->get();
                            if (!empty($check->first())) {
                                foreach ($check as $row) {
                                    array_push($pst_id, $row->pst_id);
                                }
                            }
                            $w->whereNotIn('product_stocks.id', $pst_id);
                        }
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function checkCode(Request $request)
    {
        $code = $request->post('code');
        $exist = DB::table('marketplace_managers')->where('marketplace_code', '=', $code)->exists();
        if ($exist) {
            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }
        return json_encode($r);
    }

    public function updateCode(Request $request)
    {
        $mm_id = $request->post('mm_id');
        $pst_id = $request->post('pst_id');
        $std_id = $request->post('std_id');
        $code = $request->post('code');
        if (!empty($mm_id)) {
            $update = DB::table('marketplace_managers')->where('id', '=', $mm_id)
            ->update([
                'marketplace_code' => $code
            ]);
        } else {
            $update = DB::table('marketplace_managers')
            ->insert([
                'pst_id' => $pst_id,
                'std_id' => $std_id,
                'marketplace_code' => $code
            ]);
        }
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '200';
        }
        return json_encode($r);
    }

    public function refreshData(Request $request)
    {
        $column = $request->post('column');
        $row = $request->post('row');
        $st_id = $request->post('st_id');
        $import = new MarketplaceImport($column, $row, $st_id);
        Excel::import($import, request()->file('template'));
        return Excel::download(new MarketplaceExport($import->getData()), 'refresh_results.xlsx');
    }

    public function importData(Request $request) {
        $code_column = $request->post('code_column');
        $code_title = ltrim($request->post('code_title'));
        $article_column = $request->post('article_column');
        $variation_column = $request->post('variation_column');
        $std_id = $request->post('std_id');

        $import = new MPCImport(($code_column-1), $code_title, ($article_column-1), ($variation_column-1), $std_id);
        Excel::import($import, request()->file('template'));
        $r['status'] = 200;
        return json_encode($r);
    }
}
