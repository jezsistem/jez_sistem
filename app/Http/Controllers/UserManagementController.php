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

class UserManagementController extends Controller
{
    protected function validateAccess($slug = null)
    {
        $ma_slug = $slug ?? request()->segment(1);
        $validate = DB::table('user_menu_accesses')
        ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
            'u_id' => Auth::user()->id,
            'ma_slug' => $ma_slug
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
            'gr_id' => Group::where('g_delete', '!=', '1')->orderByDesc('id')->pluck('g_name', 'id'),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'stt_id' => StoreType::where('stt_delete', '!=', '1')->orderByDesc('id')->pluck('stt_name', 'id'),
        ];
        return view('app.user_management.user_management', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(User::select('users.id as uid', 'groups.id as gr_id', 'stores.id as st_id', 'stt_id', 'u_nip', 'u_ktp', 'u_secret_code', 'u_name', 'stt_name', 'st_name', 'u_email', 'u_phone', 'u_address', 'g_name', 'u_delete')
            ->leftJoin('user_groups', 'user_groups.user_id', '=', 'users.id')
            ->leftJoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'users.stt_id')
            ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
            ->where('u_delete', '!=', '1')
            ->where('users.stt_id', '=', Auth::user()->stt_id)
            ->where('users.u_name', '!=', 'Naning Wihardini'))
            ->editColumn('st_name', function($data){ 
                return '<span style="white-space: nowrap;">'.$data->st_name.'</span>';
            })
            ->rawColumns(['st_name'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('u_name', 'LIKE', "%$search%")
                        ->orWhere('u_phone', 'LIKE', "%$search%");
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
        $stt_id = Auth::user()->stt_id;
        $code = $request->u_secret_code;
        if (empty($code)) {
            $code = 'nocode123';
        }
        $data = [
			'st_id' => $request->st_id,
			'stt_id' => $stt_id,
			'u_name' => $request->u_name,
			'u_nip' => $request->u_nip,
			'u_ktp' => $request->u_ktp,
			'u_secret_code' => $code,
			'u_email' => $request->u_email,
			'u_phone' => $request->u_phone,
			'u_address' => $request->u_address,
			'u_delete' => '0',
		];
        $group_id = 7;
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

    public function indexUpdated()
    {
        $this->validateAccess('user_management');
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'user_management')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'gr_id' => Group::where('g_delete', '!=', '1')->orderByDesc('id')->pluck('g_name', 'id'),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'stt_id' => StoreType::where('stt_delete', '!=', '1')->orderByDesc('id')->pluck('stt_name', 'id'),
        ];
        return view('app.updated_user_management.user_management', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = User::select('users.id as uid', 'groups.id as gr_id', 'stores.id as st_id', 'stt_id', 'u_nip', 'u_ktp', 'u_secret_code', 'u_name', 'stt_name', 'st_name', 'u_email', 'u_phone', 'u_address', 'g_name', 'u_delete')
                ->leftJoin('user_groups', 'user_groups.user_id', '=', 'users.id')
                ->leftJoin('groups', 'groups.id', '=', 'user_groups.group_id')
                ->leftJoin('store_types', 'store_types.id', '=', 'users.stt_id')
                ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
                ->where('u_delete', '!=', '1')
                ->where('users.stt_id', '=', Auth::user()->stt_id)
                ->where('users.u_name', '!=', 'Naning Wihardini');

            if ($search) {
                $query->where(function($w) use($search){
                    $w->orWhere('u_name', 'LIKE', "%$search%")
                        ->orWhere('u_phone', 'LIKE', "%$search%");
                });
            }

            // Get all data first to handle pagination
            $allData = $query->get();

            $total = $allData->count();
            $totalPages = ceil($total / $perPage);

            $data = $allData->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->map(function ($row) {
                    return [
                        'uid' => $row->uid,
                        'gr_id' => $row->gr_id,
                        'st_id' => $row->st_id,
                        'stt_id' => $row->stt_id,
                        'u_name' => $row->u_name ?? '-',
                        'g_name' => $row->g_name ?? '-',
                        'stt_name' => $row->stt_name ?? '-',
                        'st_name' => $row->st_name ?? '-',
                        'u_nip' => $row->u_nip ?? '-',
                        'u_ktp' => $row->u_ktp ?? '-',
                        'u_secret_code' => $row->u_secret_code ?? '-',
                        'u_phone' => $row->u_phone ?? '-',
                        'u_email' => $row->u_email ?? '-',
                        'u_address' => $row->u_address ?? '-',
                    ];
                })
                ->values()
                ->all();

            $no = ($page - 1) * $perPage + 1;
            foreach ($data as &$row) {
                $row['no'] = $no++;
            }

            return response()->json([
                'data' => $data,
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
                'per_page' => (int) $perPage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage(),
                'data' => [],
                'total' => 0,
                'total_pages' => 0,
                'current_page' => 1,
                'per_page' => 25
            ], 500);
        }
    }
}
