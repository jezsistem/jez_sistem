<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Season;
use App\Models\UserActivity;
use DateTime;

class SeasonController extends Controller
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
        ];
        return view('app.season.season', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Season::select('id', 'ss_name', 'ss_start', 'ss_end', 'created_at', 'updated_at', 'created_by', 'updated_by')
            ->where('ss_delete', '!=', '1'))
            ->editColumn('ss_description', function($data){ 
                $start = DateTime::createFromFormat('!m', $data->ss_start)->format('F');
                $end = DateTime::createFromFormat('!m', $data->ss_end)->format('F');
                return $start.' - '.$end;
            })
            ->rawColumns(['ss_description'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('ss_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $season = new Season;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'ss_name' => $request->input('ss_name'),
            'ss_start' => $request->input('ss_start'),
            'ss_end' => $request->input('ss_end'),
            'ss_delete' => '0',
        ];

        $save = $season->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $season = new Season;
        $id = $request->input('_id');
        $save = $season->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    protected function UserActivity($mode, $item)
    {
        $user_activity = new UserActivity;
        if ($mode == 'edit') {
            $activity = 'Mengubah data Gender dari '.$item['old_item'].' menjadi '.$item['item'];
        } else if ( $mode == 'add' ) {
            $activity = 'Menambah data Gender '.$item['item'];
        } else {
            $activity = 'Menghapus data Gender '.$item;
        }
        $data = [
            'user_id' => Auth::user()->id,
            'ua_description' => $activity
        ];
        $user_activity->storeData($data);
    }

    public function checkExistsSeason(Request $request)
    {
        $check = Season::where(['ss_name' => strtoupper($request->_ss_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
