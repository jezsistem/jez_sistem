<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;

class InstockApprovalController extends Controller
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
            'st_id' => DB::table('stores')->where('st_delete', '!=', '1')
            ->orderBy('st_name')->pluck('st_name', 'id'),
            'u_id' => DB::table('users')->where('u_delete', '!=', '1')
            ->selectRaw("CONCAT(u_name,' [',st_name,']') as u_name, ts_users.id as id")
            ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
            ->orderBy('u_name')->pluck('u_name', 'id'),
        ];
        return view('app.instock_approval.instock_approval', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('instock_exception_approvals')
            ->select('instock_exception_approvals.id as id', 'st_id', 'st_name', 'instock_u_id_1', 'instock_u_id_2', 'exception_u_id_1', 'exception_u_id_2')
            ->leftJoin('stores', 'stores.id', '=', 'instock_exception_approvals.st_id'))
            ->editColumn('instock_u_name_1', function($d) {
                $name = '';
                if (!empty($d->instock_u_id_1)) {
                    $name = DB::table('users')->select('u_name')->where('id', '=', $d->instock_u_id_1)->first()->u_name;
                }
                return $name;
            })
            ->editColumn('instock_u_name_2', function($d) {
                $name = '';
                if (!empty($d->instock_u_id_2)) {
                    $name = DB::table('users')->select('u_name')->where('id', '=', $d->instock_u_id_2)->first()->u_name;
                }
                return $name;
            })
            ->editColumn('exception_u_name_1', function($d) {
                $name = '';
                if (!empty($d->exception_u_id_1)) {
                    $name = DB::table('users')->select('u_name')->where('id', '=', $d->exception_u_id_1)->first()->u_name;
                }
                return $name;
            })
            ->editColumn('exception_u_name_2', function($d) {
                $name = '';
                if (!empty($d->exception_u_id_2)) {
                    $name = DB::table('users')->select('u_name')->where('id', '=', $d->exception_u_id_2)->first()->u_name;
                }
                return $name;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('st_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $mode = $request->input('mode');
        $id = $request->input('id');

        $data = [
            'st_id' => $request->post('st_id'),
            'instock_u_id_1' => $request->post('instock_u_id_1'),
            'instock_u_id_2' => $request->post('instock_u_id_2'),
            'exception_u_id_1' => $request->post('exception_u_id_1'),
            'exception_u_id_2' => $request->post('exception_u_id_2'),
        ];

        $created = [
            'created_at' => date('Y-m-d H:i:s')
        ];

        $updated = [
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($mode == 'add') {
            $data = array_merge($data, $created, $updated);
            $save = DB::table('instock_exception_approvals')->insert($data);
        } else {
            $data = array_merge($data, $updated);
            $save = DB::table('instock_exception_approvals')->where('id', '=', $id)->update($data);
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
        $id = $request->post('id');
        $delete = DB::table('instock_exception_approvals')->where('id', '=', $id)->delete();
        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
