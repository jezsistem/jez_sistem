<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Support\Facades\DB;

class UserActivityController extends Controller
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
        $user = new User();
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
        $user_activities = UserActivity::select('user_activities.id as uaid', 'st_name', 'u_name', 'ua_description', 'user_activities.created_at as ua_created_at')
            ->leftJoin('users', 'users.id', '=', 'user_activities.user_id')
            ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
            ->orderByDesc('uaid')
            ->paginate(50);
        return view('app.user_activity_log.user_activity_log', compact('data', 'user_activities'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(UserActivity::select('user_activities.id as uaid', 'st_name', 'u_name', 'ua_description', 'user_activities.created_at as ua_created_at')
            ->leftJoin('users', 'users.id', '=', 'user_activities.user_id')
            ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
            ->orderByDesc('uaid'))
            ->editColumn('ua_created_at_show', function($data){ 
                return date('d-m-Y H:i:s', strtotime($data->ua_created_at));
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('u_name', 'LIKE', "%$search%")
                        ->orWhere('ua_description', 'LIKE', "%$search%")
                        ->orWhere('st_name', 'LIKE', "%$search%");
                    });
                }
                if (!empty($request->get('st_id'))) {
                    $instance->where(function($w) use($request){
                        $st_id = $request->get('st_id');
                        if (!empty($st_id)) {
                            $w->whereIn('users.st_id', $st_id);
                        }
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }
}
