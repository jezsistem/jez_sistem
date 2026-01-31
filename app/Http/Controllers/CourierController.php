<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Courier;
use App\Models\UserActivity;

class CourierController extends Controller
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
        ];
        return view('app.courier.courier', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Courier::select('id', 'cr_name', 'cr_description')
            ->where('cr_delete', '!=', '1'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('cr_name', 'LIKE', "%$search%")
                        ->orWhere('cr_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $courier = new Courier;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'cr_name' => ltrim($request->input('cr_name')),
            'cr_description' => $request->input('cr_description'),
            'cr_delete' => '0',
        ];

        $save = $courier->storeData($mode, $id, $data);
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data kurir '.strtoupper($request->input('cr_name')));
            } else {
                $this->UserActivity('mengubah data kurir '.strtoupper($request->input('cr_name')));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $courier = new Courier;
        $id = $request->input('_id');
        $item_name = Courier::select('cr_name')->where('id', $id)->get()->first()->cr_name;
        $save = $courier->deleteData($id);
        if ($save) {
            $this->UserActivity('menghapus data kurir '.$item_name);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function indexUpdated()
    {
        $this->validateAccess('kurir_pengiriman');
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'kurir_pengiriman')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.updated_courier.courier', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = Courier::select('id', 'cr_name', 'cr_description')
                ->where('cr_delete', '!=', '1');

            if (!empty($search)) {
                $query->where(function ($w) use ($search) {
                    $w->where('cr_name', 'LIKE', "%$search%")
                      ->orWhere('cr_description', 'LIKE', "%$search%");
                });
            }

            $total = $query->count();
            $totalPages = ceil($total / $perPage);

            $query->orderBy('id', 'desc');
            $query->skip(($page - 1) * $perPage)->take($perPage);

            $results = $query->get();

            $data = [];
            $no = ($page - 1) * $perPage + 1;
            foreach ($results as $row) {
                $data[] = [
                    'no' => $no++,
                    'id' => $row->id,
                    'cr_name' => $row->cr_name ?? '-',
                    'cr_description' => $row->cr_description ?? '-',
                ];
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
