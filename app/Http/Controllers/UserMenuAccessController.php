<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;

class UserMenuAccessController extends Controller
{
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

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('user_menu_accesses')->select('user_menu_accesses.id as id', 'ma_id', 'ma_title', 'uma_default', 'ma_sort')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')
            ->where('u_id', '=', $request->get('u_id'))
            ->orderBy('ma_sort'))
            ->editColumn('uma_default', function($data){
                if ($data->uma_default == '1') {
                    return "<a class='btn btn-sm btn-primary' data-id='".$data->id."'><i class='fa fa-check'></i></a>";
                }
            })
            ->editColumn('action', function($data){
                return "<a class='btn btn-sm btn-danger' id='delete_menu_access_btn' data-id='".$data->id."'>X</a>";
            })
            ->rawColumns(['uma_default', 'action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('ma_title', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $req)
    {
        $ma_id = $req->post('ma_id');
        $u_id = $req->post('u_id');

        $data = array();
        for ($i = 0; $i < count($ma_id); $i++) {
            $data[] = [
                'ma_id' => $ma_id[$i],
                'u_id' => $u_id
            ];
        }
        $save = DB::table('user_menu_accesses')->insert($data);
        if (!empty($save)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $req)
    {
        $id = $req->post('id');
        $delete = DB::table('user_menu_accesses')
        ->where('id', '=', $id)->delete();
        if (!empty($delete)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function setDefault(Request $req)
    {
        $ma_id = $req->post('ma_id');
        $u_id = $req->post('u_id');

        $update = DB::table('user_menu_accesses')
        ->where('u_id', '=', $u_id)->update([
            'uma_default' => '0'
        ]);

        $update = DB::table('user_menu_accesses')
        ->where('u_id', '=', $u_id)
        ->where('ma_id', '=', $ma_id)->update([
            'uma_default' => '1'
        ]);

        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
