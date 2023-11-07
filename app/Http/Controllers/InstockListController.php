<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;

class InstockListController extends Controller
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
    
    private function checkAccess() {
        $r = 0;
        $check = DB::table('instock_exception_approvals')
        ->where('instock_u_id_1', '=', Auth::user()->id)
        ->orWhere('instock_u_id_2', '=', Auth::user()->id)
        ->exists();
        if ($check) {
            $r = 1;
        }
        return $r;
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

        if ($user_data->g_name != 'administrator') {
            if ($this->checkAccess() != 1) {
                dd("Anda tidak memiliki akses ke fitur ini");
            }
        }

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'st_id' => DB::table('stores')->where('st_delete', '!=', '1')
            ->orderBy('st_name')->pluck('st_name', 'id'),
            'u_id' => DB::table('users')->where('u_delete', '!=', '1')
            ->selectRaw("CONCAT(u_name,' [',st_name,']') as u_name, ts_users.id as id")
            ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
            ->orderBy('u_name')->pluck('u_name', 'id'),
        ];
        return view('app.instock_list.instock_list', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        $st_id = $request->get('st_id');
        $user = new User;
        $select = ['u_name', 'u_email', 'u_phone', 'g_name'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();

        $arr = array();
        $inlist = DB::table('instock_exception_approvals')
        ->select('st_id')
        ->where('instock_u_id_1', '=', Auth::user()->id)
        ->orWhere('instock_u_id_2', '=', Auth::user()->id)
        ->get();
        if (!empty($inlist->first())) {
            foreach ($inlist as $i) {
                array_push($arr, $i->st_id);
            }
        }

        if(request()->ajax()) {
            return datatables()->of(DB::table('product_location_setup_transactions')
            ->selectRaw("ts_product_location_setup_transactions.id as id, plst_status, pls_id, st_name, CONCAT(ts_brands.br_name,' ',ts_products.p_name,' ',ts_products.p_color,' ',ts_sizes.sz_name) as article, pl_code, u_name, ts_product_location_setup_transactions.updated_at, ts_product_location_setup_transactions.created_at, plst_qty")
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('users', 'users.id', '=', 'product_location_setup_transactions.u_id_helper')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->where(function($w) use ($st_id, $user_data, $arr) {
                if ($user_data->g_name != 'administrator') {
                    $w->whereIn('product_locations.st_id', $arr);
                } else {
                    if (!empty($st_id)) {
                        $w->where('product_locations.st_id', '=', $st_id);
                    }
                }
                $w->where('plst_status', '=', 'INSTOCK APPROVAL');
            }))
            ->editColumn('approve', function($d) {
                $check = DB::table('instock_lists')->where('plst_id', '=', $d->id)->first();
                if (!empty($check)) {
                    
                } else {
                    return 'Menunggu Approval';
                }
            })
            ->editColumn('action', function($d) {
                return "
                    <a class='btn btn-sm btn-danger' data-plst_id='".$d->id."' data-plst_qty='".$d->plst_qty."' data-pls_id='".$d->pls_id."' id='decline'>Decline</a>
                    <a class='btn btn-sm btn-success' data-plst_id='".$d->id."' data-plst_qty='".$d->plst_qty."' data-pls_id='".$d->pls_id."' id='confirm'>Confirm</a>
                ";
            })
            ->editColumn('updated_at', function($d) {
                if (!empty($d->updated_at)) {
                    return date('d/m/Y H:i:s', strtotime($d->updated_at));
                } else {
                    return date('d/m/Y H:i:s', strtotime($d->created_at));
                }
            })
            ->rawColumns(['action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw("CONCAT(ts_brands.br_name,' ',ts_products.p_name,' ',ts_products.p_color,' ',ts_sizes.sz_name) LIKE ?", "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getHistoryDatatables(Request $request)
    {
        $st_id = $request->get('st_id');
        $user = new User;
        $select = ['u_name', 'u_email', 'u_phone', 'g_name'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();

        $arr = array();
        $inlist = DB::table('instock_exception_approvals')
        ->select('st_id')
        ->where('instock_u_id_1', '=', Auth::user()->id)
        ->orWhere('instock_u_id_2', '=', Auth::user()->id)
        ->get();
        if (!empty($inlist->first())) {
            foreach ($inlist as $i) {
                array_push($arr, $i->st_id);
            }
        }

        if(request()->ajax()) {
            return datatables()->of(DB::table('instock_lists')
            ->selectRaw("ts_instock_lists.id as id, plst_status, st_name, ts_instock_lists.u_id, CONCAT(ts_brands.br_name,' ',ts_products.p_name,' ',ts_products.p_color,' ',ts_sizes.sz_name) as article, 
            pl_code, u_name, ts_product_location_setup_transactions.updated_at, plst_qty,
            ts_instock_lists.created_at as approve_date")
            ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.id', '=', 'instock_lists.plst_id')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('users', 'users.id', '=', 'product_location_setup_transactions.u_id_helper')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->where(function($w) use ($st_id, $user_data, $arr) {
                if ($user_data->g_name != 'administrator') {
                    $w->whereIn('product_locations.st_id', $arr);
                } else {
                    if (!empty($st_id)) {
                        $w->where('product_locations.st_id', '=', $st_id);
                    }
                }
                $w->where('plst_status', '=', 'INSTOCK');
            }))
            ->editColumn('approve', function($d) {
                $uname = '';
                if (!empty($d->u_id)) {
                    $uname = DB::table('users')->select('u_name')->where('id', '=', $d->u_id)->first()->u_name;
                }
                return $uname;
            })
            ->editColumn('updated_at', function($d) {
                return date('d/m/Y H:i:s', strtotime($d->updated_at));
            })
            ->editColumn('approve_date', function($d) {
                return date('d/m/Y H:i:s', strtotime($d->approve_date));
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw("CONCAT(ts_brands.br_name,' ',ts_products.p_name,' ',ts_products.p_color,' ',ts_sizes.sz_name) LIKE ?", "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request) {
        $plst_id = $request->post('plst_id');
        $plst_qty = $request->post('plst_qty');
        $pls_id = $request->post('pls_id');
        $type = $request->post('type');

        if ($type == 'decline') {
            $stt_id = DB::table('product_location_setup_transactions')
            ->select('users.stt_id as stt_id')
            ->where('product_location_setup_transactions.id', '=', $plst_id)
            ->leftJoin('users', 'users.id', '=', 'product_location_setup_transactions.u_id')->first()->stt_id;
            if ($stt_id == '1') {
                $status = 'WAITING ONLINE';
            } else {
                $status = 'WAITING OFFLINE';
            }

            $update = DB::table('product_location_setup_transactions')
            ->where('id', '=', $plst_id)
            ->where('plst_status', '=', 'INSTOCK APPROVAL')
            ->update([
                'plst_status' => $status
            ]);
        } else {
            $status = 'INSTOCK';
            $update = DB::table('product_location_setup_transactions')
            ->where('id', '=', $plst_id)
            ->where('plst_status', '=', 'INSTOCK APPROVAL')
            ->update([
                'plst_status' => $status
            ]);
            $qty = 0;
            $current_qty = DB::table('product_location_setups')->select('pls_qty')
            ->where('id', '=', $pls_id)->first();
            if (!empty($current_qty)) {
                $qty = $current_qty->pls_qty;
            }
            $update = DB::table('product_location_setups')
            ->where('id', '=', $pls_id)
            ->update([
                'pls_qty' => ($qty + $plst_qty)
            ]);
            $update = DB::table('instock_lists')->insert([
                'plst_id' => $plst_id,
                'u_id' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if (!empty($update)) {
            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }
        return json_encode($r);
    }
}
