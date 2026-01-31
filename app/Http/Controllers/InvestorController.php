<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use Hash;

class InvestorController extends Controller
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

    protected function getLabel($table, $field, $id)
    {
        $label = DB::table($table)->select($field)->where('id', '=', $id)->get()->first();
        if (!empty($label)) {
            return $label->$field;
        } else {
            return false;
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
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'st_id' => DB::table('stores')->orderBy('st_name')->pluck('st_name', 'id'),
        ];
        return view('app.investor.investor', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('investors')->select('investors.id as id', 'st_id', 'st_name', 'i_name', 'i_username', 'i_email', 'i_phone', 'i_address')
            ->leftJoin('stores', 'stores.id', '=', 'investors.st_id'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('i_phone', 'LIKE', "%$search%")
                        ->orWhere('i_name', 'LIKE', "%$search%");
                    });
                }
                if (!empty($request->get('st_id'))) {
                    $instance->where(function($w) use($request){
                        $st_id = $request->get('st_id');
                        $w->where('st_id', '=', $st_id);
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
            'st_id' => $request->input('st_id'),
            'i_name' => $request->input('i_name'),
            'i_username' => $request->input('i_username'),
            'i_email' => $request->input('i_email'),
            'i_phone' => $request->input('i_phone'),
            'i_address' => $request->input('i_address'),
        ];
        $created = [
            'created_at' => date('Y-m-d H:i:s')
        ];
        $updated = [
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if (!empty($request->input('password'))) {
            $password = [
                'password' => Hash::make($request->input('password'))
            ];
            $data = array_merge($data, $password);
        }
        if ($mode == 'add') {
            $data = array_merge($data, $created, $updated);
            $save = DB::table('investors')->insert($data);
        } else {
            $data = array_merge($data, $updated);
            $save = DB::table('investors')
            ->where('id', '=', $id)->update($data);
        }
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('Menambah data investor '.$request->input('i_name'));
            } else {
                $this->UserActivity('Mengubah data investor '.$request->input('i_name'));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $id = $request->input('_id');
        $item_name = $this->getLabel('investors', 'i_name', $id);
        $delete = DB::table('investors')->where('id', '=', $id)->delete();
        if ($delete) {
            $this->UserActivity('Menghapus data investor '.$item_name);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkUsername(Request $request)
    {
        $username = $request->input('i_username');
        $check = DB::table('investors')->where('i_username', '=', $username)->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function indexUpdated()
    {
        $this->validateAccess('investor');
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'investor')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'st_id' => DB::table('stores')->orderBy('st_name')->pluck('st_name', 'id'),
        ];
        return view('app.updated_investor.investor', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');
            $st_id = $request->get('st_id', '');

            $query = DB::table('investors')
                ->select('investors.id as id', 'st_id', 'st_name', 'i_name', 'i_username', 'i_email', 'i_phone', 'i_address')
                ->leftJoin('stores', 'stores.id', '=', 'investors.st_id');

            if ($search) {
                $query->where(function($w) use($search){
                    $w->orWhere('i_phone', 'LIKE', "%$search%")
                        ->orWhere('i_name', 'LIKE', "%$search%");
                });
            }

            if ($st_id) {
                $query->where('investors.st_id', '=', $st_id);
            }

            // Get all data first to handle pagination
            $allData = $query->orderByDesc('investors.id')->get();

            $total = $allData->count();
            $totalPages = ceil($total / $perPage);

            $data = $allData->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'st_id' => $row->st_id ?? null,
                        'st_name' => $row->st_name ?? '-',
                        'i_name' => $row->i_name ?? '-',
                        'i_username' => $row->i_username ?? '-',
                        'i_phone' => $row->i_phone ?? '-',
                        'i_email' => $row->i_email ?? '-',
                        'i_address' => $row->i_address ?? '-',
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
