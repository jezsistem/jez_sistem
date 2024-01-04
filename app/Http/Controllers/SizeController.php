<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\Size;

class SizeController extends Controller
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
            'size_id' => Size::where('sz_delete', '!=', '1')->distinct()->pluck('sz_description'),

        ];
//        dd($data['size_id']);
        return view('app.size.size', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        $sz_id = $request->sz_id;
        if(request()->ajax()) {
            return datatables()->of(Size::select('sizes.id as sid', 'sz_name', 'sz_schema' ,'sz_description')
            ->where('sz_delete', '!=', '1')
            ->where(function ($query) use ($sz_id) {
                if (!empty($sz_id)) {
                    $query->where('sz_description', $sz_id);
                }
            })
            ->where('sz_schema','!=', '')
            ->orderBy('sz_schema'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('sz_name', 'LIKE', "%$search%")
                        ->orWhere('sz_name', 'LIKE', "%$search%")
                        ->orWhere('sz_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $size = new Size;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
//            'psc_id' => $request->input('psc_id'),
            'sz_name' => $request->input('sz_name'),
            'sz_schema' => strtoupper($request->input('sz_schema')),
            'sz_description' => $request->input('sz_description'),
            'sz_delete' => '0',
        ];

        $save = $size->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $size = new Size;
        $id = $request->input('_id');
        $save = $size->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function reloadSize(Request $request)
    {
        $size = new Size;
        $select = ['id', 'sz_name', 'sz_description'];
        $where = [
            'psc_id' => $request->_psc_id
        ];
        $size_data = $size->getAllData($select, $where);
        $data = [
            'size' => $size_data
		];
        return view('app.product._reload_size', compact('data'));
    }

    public function checkExistsSize(Request $request)
    {
        $check = Size::where(['sz_name' => strtoupper($request->_sz_name), 'psc_id' => $request->_psc_id])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
