<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;

class MenuAccessController extends Controller
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
            'mt_id' => DB::table('menu_titles')->orderBy('mt_sort')->pluck('mt_title', 'id'),
            'sidebar' => $this->sidebar()
        ];
        return view('app.menu_access.menu_access', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('menu_accesses')->select('menu_accesses.id as id', 'mt_id', 'mt_title', 'ma_title', 'ma_slug', 'ma_sort')
            ->leftJoin('menu_titles', 'menu_titles.id', '=', 'menu_accesses.mt_id')
            ->orderBy('ma_sort'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('mt_title', 'LIKE', "%$search%")
                        ->orWhere('ma_title', 'LIKE', "%$search%");
                    });
                }
                if (!empty($request->get('mt_id'))) {
                    $instance->where(function($w) use($request){
                        $mt_id = $request->get('mt_id');
                        $w->where('mt_id', '=', $mt_id);
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
            'mt_id' => $request->input('mt_id'),
            'ma_title' => $request->input('ma_title'),
            'ma_slug' => $request->input('ma_slug'),
            'ma_sort' => $request->input('ma_sort'),
        ];
        $created = [
            'created_at' => date('Y-m-d H:i:s')
        ];
        $updated = [
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($mode == 'add') {
            $data = array_merge($data, $created, $updated);
            $save = DB::table('menu_accesses')->insert($data);
        } else {
            $data = array_merge($data, $updated);
            $save = DB::table('menu_accesses')
            ->where('id', '=', $id)->update($data);
        }
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('Menambah data menu access '.$request->input('ma_title'));
            } else {
                $this->UserActivity('Mengubah data menu access '.$request->input('ma_title'));
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
        $item_name = $this->getLabel('menu_accesses', 'ma_title', $id);
        $delete = DB::table('menu_accesses')->where('id', '=', $id)->delete();
        if ($delete) {
            $this->UserActivity('Menghapus data menu access '.$item_name);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
