<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;

class StoreAgingController extends Controller
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

    protected function countDeleted($table,$deleted)
    {
        return DB::table($table)->where($deleted, '=', '1')->count('id');
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
            'st_id' => DB::table('stores')->where('st_delete', '!=', '1')->orderByDesc('st_name')->pluck('st_name', 'id'),
        ];
        return view('app.store_aging.store_aging', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('store_agings')
            ->select('store_agings.id as id', 'sa_name', 'sa_age', DB::raw("count(ts_store_aging_details.st_id) as store"))
            ->leftJoin('store_aging_details', 'store_aging_details.sa_id', '=', 'store_agings.id')
            ->groupBy('store_agings.id'))
            ->editColumn('store', function($d) {
                return "<a class='badge badge-sm badge-primary' data-id='".$d->id."' id='store_detail_btn'>".$d->store."</a>";
            })
            ->rawColumns(['store'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('sa_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getOCADatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('online_cross_agings')
            ->select("online_cross_agings.id", "st_name", "oca_age")
            ->leftJoin('stores', 'stores.id', '=', 'online_cross_agings.st_id'))
            ->editColumn('action', function($d) {
                return "<a class='badge badge-sm badge-danger' data-id='".$d->id."' id='delete_oca_btn'><i class='fa fa-trash text-white'></i></a>";
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getDetailDatatables(Request $request)
    {
        $sa_id = $request->get('sa_id');
        if(request()->ajax()) {
            return datatables()->of(DB::table('store_aging_details')
            ->select('store_aging_details.id as id', 'st_name', 'show')
            ->leftJoin('stores', 'stores.id', '=', 'store_aging_details.st_id')
            ->where('sa_id', '=', $sa_id))
            ->editColumn('show', function($d) {
                if ($d->show == 1) {
                    $y = "Ya <input type='checkbox' disabled checked/>";
                    $n = "Tidak <input type='checkbox' data-id='".$d->id."' id='n_check' />";
                }  else {
                    $y = "Ya <input type='checkbox' data-id='".$d->id."' id='y_check' />";
                    $n = "Tidak <input type='checkbox' disabled checked/>";
                }
                return "
                    <div class='row'>
                        <div class='col-3'>
                            ".$y."
                        </div>
                        <div class='col-3'>
                            ".$n."
                        </div>
                    </div>
                ";
            })
            ->editColumn('action', function($d) {
                return "<a class='badge badge-sm badge-danger' data-id='".$d->id."' id='delete_detail_btn'><i class='fa fa-trash text-white'></i></a>";
            })
            ->rawColumns(['action', 'show'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function updateChecked(Request $request) {
        $id = $request->post('id');
        $type = $request->post('type');
        $update = DB::table('store_aging_details')->where('id','=', $id)->update([
            'show' => $type,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        if ($update) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeData(Request $request)
    {
        $mode = $request->post('_mode');
        $id = $request->post('_id');

        $data = [
            'sa_name' => $request->post('sa_name'),
            'sa_age' => $request->post('sa_age'),
        ];

        $created = [
            'created_at' => date('Y-m-d H:i:s')
        ];

        $updated = [
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($mode == 'add') {
            $data = array_merge($data, $created, $updated);
            $save = DB::table('store_agings')->insert($data);
        } else {
            $data = array_merge($data, $updated);
            $save = DB::table('store_agings')->where('id', '=', $id)->update($data);
        }

        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('Menambah data '.$request->post('sa_name'));
            } else {
                $this->UserActivity('Mengubah data '.$request->post('sa_name'));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $id = $request->post('id');
        $delete = DB::table('store_aging_details')->where('sa_id', '=', $id)->delete();
        $delete = DB::table('store_agings')->where('id', '=', $id)->delete();
        if ($delete) {
            $this->UserActivity('Menghapus data setup store aging dengan backend id '.$id);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeDetailData(Request $request)
    {
        $sa_id = $request->post('sa_id');
        $st_id = $request->post('st_id');

        $data = [
            'sa_id' => $sa_id,
            'st_id' => $st_id,
            'show' => '0',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $save = DB::table('store_aging_details')->insert($data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteDetailData(Request $request)
    {
        $id = $request->post('id');
        $delete = DB::table('store_aging_details')->where('id', '=', $id)->delete();
        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeOCAData(Request $request)
    {
        $st_id = $request->post('st_id');
        $oca_age = $request->post('oca_age');

        $data = [
            'st_id' => $st_id,
            'oca_age' => $oca_age,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $save = DB::table('online_cross_agings')->insert($data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteOCAData(Request $request)
    {
        $id = $request->post('id');
        $delete = DB::table('online_cross_agings')->where('id', '=', $id)->delete();
        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
