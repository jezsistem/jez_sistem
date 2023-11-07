<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\BuyOneGetOne;
use App\Models\ProductLocation;
use App\Models\Store;
use App\Models\UserActivity;

class B1g1Controller extends Controller
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
        $path = "
        <li class='breadcrumb-item'>
            <a href='' class='text-muted'>Pengaturan</a>
        </li>
        <li class='breadcrumb-item'>
            <a href='' class='text-muted'>B1G1 Location</a>
        </li>";
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'path' => $path,
            'user' => $user_data,
            'pl_id' => ProductLocation::where('pl_delete', '!=', '1')->orderByDesc('id')->pluck('pl_code', 'id'),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.b1g1_location.b1g1_location', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('product_locations')
            ->select('product_locations.id', 'pl_code', 'bogo_description', 'pl_id')
            ->leftJoin('buy_one_get_ones', 'buy_one_get_ones.pl_id', '=', 'product_locations.id')
            ->where(function($w) use ($request) {
                $st_id = $request->get('st_id');
                if (!empty($st_id)) {
                    $w->where('product_locations.st_id', '=', $st_id);
                } else {
                    $w->where('product_locations.st_id', '=', '%^&*');
                }
            })
            ->orderByDesc('buy_one_get_ones.pl_id'))
            ->editColumn('action', function($d) {
                if (!empty($d->pl_id)) {
                    return "<input type='checkbox' data-pl_id='".$d->pl_id."' id='unchecked' checked/>";
                } else {
                    return "<input type='checkbox' data-pl_id='".$d->id."' id='checked'/>";
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pl_code', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function updateData(Request $request) {
        $type = $request->post('type');
        $pl_id = $request->post('pl_id');
        if ($type == 'checked') {
            $save = DB::table('buy_one_get_ones')->insert([
                'pl_id' => $pl_id
            ]);
        } else {
            $save = DB::table('buy_one_get_ones')->where('pl_id', '=', $pl_id)->delete();
        }
        
        if (!empty($save)) {
            $r['status'] = 200;
        } else {
            $r['status'] = 400;
        }
        return json_encode($r);
    }

    public function storeData(Request $request)
    {
        $mode = $request->input('_mode');
        $id = $request->input('_id');
        $data = [
            'pl_id' => $request->input('pl_id'),
            'bogo_description' => $request->input('bogo_description'),
        ];
        if ($mode == 'add') {
          $save = BuyOneGetOne::insert($data);
          $this->UserActivity('menambah data b1g1 location '.strtoupper($request->input('bogo_description')));
        } else {
          $save = BuyOneGetOne::where('id', '=', $id)->update($data);
          $this->UserActivity('mengubah data b1g1 location '.strtoupper($request->input('bogo_description')));
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
        $save = BuyOneGetOne::where('id', '=', $id)->delete();
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
