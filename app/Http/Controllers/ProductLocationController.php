<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductLocation;
use App\Models\Store;
use App\Models\UserActivity;

class ProductLocationController extends Controller
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
            'st_id' => Store::selectRaw('ts_stores.id as sid, CONCAT(st_name) as store')
            ->where('st_delete', '!=', '1')
            ->orderByDesc('sid')->pluck('store', 'sid'),
        ];
        return view('app.product_location.product_location', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(ProductLocation::select('product_locations.id as pl_id', 'st_name', 'pl_code', 'pl_name', 'pl_description', 'pl_default')
            ->join('stores', 'stores.id', '=','product_locations.st_id')
            ->where('pl_delete', '!=', '1')
            ->where('st_id', '=', $request->st_id))
            ->editColumn('pl_default_show', function($data){ 
                if ($data->pl_default == '1') {
                    return 'Ya';
                } else {
                    return '-';
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pl_name', 'LIKE', "%$search%")
                        ->orWhere('pl_code', 'LIKE', "%$search%")
                        ->orWhere('pl_description', 'LIKE', "%$search%");
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

        if ($request->input('pl_default') == '1') {
            ProductLocation::where('st_id', '=', $request->input('st_id'))->update([
                'pl_default' => '0'
            ]);
        }

        $data = [
            'st_id' => $request->input('st_id'),
            'pl_code' => strtoupper($request->input('pl_code')),
            'pl_name' => $request->input('pl_name'),
            'pl_description' => $request->input('pl_description'),
            'pl_default' => $request->input('pl_default'),
            'pl_delete' => '0',
        ];

        $save = $product_location->storeData($mode, $id, $data);
        if ($save) {
            $this->UserActivity('menambah data lokasi '.strtoupper($request->input('pl_code')));
            $r['status'] = '200';
        } else {
            $this->UserActivity('mengubah data lokasi '.strtoupper($request->input('pl_code')));
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

    public function checkCode(Request $request)
    {
        $product_location = new ProductLocation;
        $pl_code = $request->input('_pl_code');
        $st_id = $request->input('_st_id');
        $select = ['pl_name'];
        $where = [
            'pl_code' => strtoupper($pl_code),
            'st_id' => $st_id
        ];
        $check = $product_location->checkData($select, $where);
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
