<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ExceptionLocation;
use App\Models\ProductLocation;
use App\Models\UserActivity;

class ExceptionLocationController extends Controller
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
            'pl_id' => ProductLocation::where('pl_delete', '!=', '1')->orderByDesc('id')->pluck('pl_code', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.exception_location.exception_location', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(ExceptionLocation::select('exception_locations.id as el_id', 'st_name', DB::raw('sum(ts_product_location_setups.pls_qty) as qty'), 'product_locations.id as pl_id', 'pl_code', 'el_description')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->leftJoin('product_location_setups', 'product_location_setups.pl_id', '=', 'product_locations.id')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->groupBy('exception_locations.id'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pl_code', 'LIKE', "%$search%")
                        ->orWhere('el_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $mode = $request->input('_mode');
        $id = $request->input('_id');
        $data = [
            'pl_id' => $request->input('pl_id'),
            'el_description' => $request->input('el_description'),
        ];
        if ($mode == 'add') {
          $save = ExceptionLocation::insert($data);
          $this->UserActivity('menambah data exception location '.strtoupper($request->input('el_description')));
        } else {
          $save = ExceptionLocation::where('id', '=', $id)->update($data);
          $this->UserActivity('mengubah data exception location '.strtoupper($request->input('el_description')));
        }
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $id = $request->input('_id');
        $save = ExceptionLocation::where('id', '=', $id)->delete();
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsProductLocation(Request $request)
    {
        $check = ProductCategory::where(['pc_name' => strtoupper($request->_pc_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
