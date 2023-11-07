<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Group;
use App\Models\Store;
use App\Models\StoreType;
use App\Models\UserActivity;

class UserController extends Controller
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

    protected function getLabel($table, $field, $id)
    {
        $label = DB::table($table)->select($field)->where('id', '=', $id)->get()->first();
        if (!empty($label)) {
            return $label->$field;
        } else {
            return '[field not found]';
        }
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
            'user' => $user_data,
            'segment' => request()->segment(1),
            'gr_id' => Group::where('g_delete', '!=', '1')->orderByDesc('id')->pluck('g_name', 'id'),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'stt_id' => StoreType::where('stt_delete', '!=', '1')->orderByDesc('id')->pluck('stt_name', 'id'),
            'ma_id' => DB::table('menu_accesses')->orderBy('ma_sort')->pluck('ma_title', 'id'),
            'sidebar' => $this->sidebar()
        ];
        return view('app.user.user', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(User::selectRaw("ts_users.id as uid, ts_groups.id as gr_id, ts_stores.id as st_id, stt_id, u_nip, u_ktp, u_secret_code
            , u_name, stt_name, st_name, u_email, u_phone, u_address, delete_access, g_name, u_delete,
            count(ts_user_menu_accesses.id) as uma")
            ->leftJoin('user_groups', 'user_groups.user_id', '=', 'users.id')
            ->leftJoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'users.stt_id')
            ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
            ->leftJoin('user_menu_accesses', 'user_menu_accesses.u_id', '=', 'users.id')
            ->groupBy('uid')
            ->where('u_delete', '!=', '1'))
            ->editColumn('st_name', function($data){
                return '<span style="white-space: nowrap;">'.$data->st_name.'</span>';
            })
            ->editColumn('g_name', function($data){
                return strtoupper($data->g_name);
            })
            ->editColumn('menu_access', function($data){
                return "<span class='badge badge-sm badge-primary' id='menu_access_btn' data-id='".$data->uid."'><i class='fa fa-eye'></i></span>";
            })
            ->editColumn('delete_access_show', function($data){
                if ($data->delete_access == '1') {
                    $delete = 'Ya';
                } else {
                    $delete = '-';
                }
                return "<span class='badge badge-sm badge-primary'>".$delete."</span>";
            })
            ->rawColumns(['st_name', 'menu_access', 'delete_access_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('g_name', 'LIKE', "%$search%")
                        ->orWhere('st_name', 'LIKE', "%$search%")
                        ->orWhere('stt_name', 'LIKE', "%$search%")
                        ->orWhere('u_name', 'LIKE', "%$search%")
                        ->orWhere('u_nip', 'LIKE', "%$search%")
                        ->orWhere('u_ktp', 'LIKE', "%$search%")
                        ->orWhere('u_secret_code', 'LIKE', "%$search%")
                        ->orWhere('u_phone', 'LIKE', "%$search%")
                        ->orWhere('u_address', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $user = new User;
        $mode = $request->_mode;
        $id = $request->_id;
        $data = [
			'st_id' => $request->st_id,
			'stt_id' => $request->stt_id,
			'u_name' => $request->u_name,
			'u_nip' => $request->u_nip,
			'u_ktp' => $request->u_ktp,
			'u_secret_code' => $request->u_secret_code,
			'u_email' => $request->u_email,
			'u_phone' => $request->u_phone,
			'u_address' => $request->u_address,
			'delete_access' => $request->delete_access,
			'u_delete' => '0',
		];
        $group_id = $request->gr_id;
        $password = $request->u_password;
        $created = [
			'created_at' => date('Y-m-d H:i:s')
		];
        $updated = [
			'updated_at' => date('Y-m-d H:i:s')
		];
        if ($user->storeData($mode, $id, $data, $group_id, $password, $created, $updated)) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data user ['.$request->u_ktp.'] ['.$request->u_nip.'] '.$request->u_name);
            } else {
                $this->UserActivity('mengubah data user ['.$request->u_ktp.'] ['.$request->u_nip.'] '.$request->u_name);
            }
            $r['status'] = "200";
        } else {
            $r['status'] = "400";
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $user = new User;
        $id = $request->input('_id');
        $save = User::where('id', $id)->update([
            'u_delete' => '1'
        ]);
        if ($save) {
            $item_name = User::select('u_name', 'u_ktp')->where('id', $id)->get()->first();
            $this->UserActivity('menghapus data user ['.$item_name->u_ktp.'] '.$item_name->u_name);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsSecretCode(Request $request)
    {
        $check = User::where(['u_secret_code' => $request->_u_secret_code])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    function autocompleteMenu(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');
            $data = DB::table('menu_accesses')->select("id", "ma_title")
            ->whereRaw('ma_title LIKE ?', "%$query%")
            ->orderBy('ma_title')
            ->limit(10)
            ->get();
            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach($data as $row) {
                    $output .= '
                    <li><a class="btn btn-sm btn-inventory col-12" data-id="'.$row->id.'" data-ma_title="'.$row->ma_title.'" id="add_ma_to_list"><span class="btn-sm btn-primary">'.$row->ma_title.'</span></a></li>
                    ';
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    function autocompleteStore(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');
            $data = DB::table('stores')->select("id", "st_name")
            ->whereRaw('st_name LIKE ?', "%$query%")
            ->where('st_delete', '!=', '1')
            ->orderBy('st_name')
            ->limit(10)
            ->get();
            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach($data as $row) {
                    $output .= '
                    <li><a class="btn btn-sm btn-inventory col-12" data-id="'.$row->id.'" data-st_name="'.$row->st_name.'" id="add_st_to_list"><span class="btn-sm btn-primary">'.$row->st_name.'</span></a></li>
                    ';
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    function loadUserMenu(Request $req)
    {
        $u_id = $req->post('u_id');
        $ma_id = array();
        $uma = DB::table('user_menu_accesses')->select('ma_id')->where('u_id', '=', $u_id)->get();
        if (!empty($uma)) {
            foreach ($uma as $row) {
                $ma_id[] = [$row->ma_id];
            }
        }
        $ma = DB::table('menu_accesses')->select('ma_title', 'id')->whereNotIn('id', $ma_id)->get();
        $r = array();
        foreach ($ma as $row) {
            $r[] = [
                'ma_title' => $row->ma_title,
                'id' => $row->id
            ];
        }
        return json_encode($r);
    }

    public function loadStore() {
        $store = DB::table('stores')
        ->where('id', '=', Auth::user()->st_id)->first()->st_name;
        $r['status'] = 200;
        $r['store'] = $store;
        return json_encode($r);
    }

}
