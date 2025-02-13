<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\DataPerusahaan;
use App\Models\UserActivity;use App\Exports\DataPerusahaanExport;
use Maatwebsite\Excel\Facades\Excel;


class DataPerusahaanController extends Controller
// {
//     protected function validateAccess()
//     {
//         $validate = DB::table('user_menu_accesses')
//         ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
//             'u_id' => Auth::user()->id,
//             'ma_slug' => request()->segment(1)
//         ])->exists();
//         if (!$validate) {
//             dd("Anda tidak memiliki akses ke menu ini, hubungi Administrator");
//         }
//     }

//     protected function sidebar()
//     {
//         $ma_id = DB::table('user_menu_accesses')->select('ma_id')
//         ->where('u_id', Auth::user()->id)->get();
//         $ma_id_arr = array();
//         if (!empty($ma_id)) {
//             foreach ($ma_id as $row) {
//                 array_push($ma_id_arr, $row->ma_id);
//             }
//         }

//         $sidebar = array();
//         $mt = DB::table('menu_titles')->orderBy('mt_sort')->get();
//         if (!empty($mt->first())) {
//             foreach ($mt as $row) {
//                 $ma = DB::table('menu_accesses')
//                 ->where('mt_id', '=', $row->id)
//                 ->whereIn('id', $ma_id_arr)
//                 ->orderBy('ma_sort')->get();
//                 if (!empty($ma->first())) {
//                     $row->ma = $ma;
//                     array_push($sidebar, $row);
//                 }
//             }
//         }
//         return $sidebar;
//     }
    
//     public function index() 
//     {
//         $this->validateAccess();
//         $user = new User;
//         $select = ['*'];
//         $where = [
//             'users.id' => Auth::user()->id
//         ];
//         $user_data = $user->checkJoinData($select, $where)->first();
//         $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
//         $data = [
//             'title' => $title,
//             'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
//             'sidebar' => $this->sidebar(),
//             'user' => $user_data,
//             'segment' => request()->segment(1),
//         ];
//         return view('app.data_perusahaan.data_perusahaan', compact('data'));
//     }

//     public function getDatatables(Request $request)
//     {
//         if(request()->ajax()) {
//             return datatables()->of(DataPerusahaan::select('id', 'dp_name', 'dp_description')
//             )
//             ->filter(function ($instance) use ($request) {
//                 if (!empty($request->get('search'))) {
//                     $instance->where(function($w) use($request){
//                         $search = $request->get('search');
//                         $w->orWhere('dp_name', 'LIKE', "%$search%")
//                         ->orWhere('dp_description', 'LIKE', "%$search%");
//                     });
//                 }
//             })
//             ->addIndexColumn()
//             ->make(true);
//         }
//     }

//     public function storeData(Request $request)
//     {
//         $data_perusahaan = new DataPerusahaan;
//         $mode = $request->input('_mode');
//         $id = $request->input('_id');

//         $data = [
//             'dp_name' => ltrim($request->input('dp_name')),
//             'dp_description' => $request->input('dp_description')
//         ];

//         $save = $data_perusahaan->storeData($mode, $id, $data);
//         if ($save) {
//             $r['status'] = '200';
//         } else {
//             $r['status'] = '400';
//         }
//         $item = [
//             'item' => $request->input('dp_name'),
//             'old_item' => $request->input('_old_item')
//         ];
//         $this->UserActivity($mode, $item);
//         return json_encode($r);
//     }

//     public function deleteData(Request $request)
//     {
//         $data_perusahaan = new DataPerusahaan;
//         $id = $request->input('_id');
//         $save = $data_perusahaan->deleteData($id);
//         if ($save) {
//             $r['status'] = '200';
//         } else {
//             $r['status'] = '400';
//         }
//         return json_encode($r);
//     }

//     protected function UserActivity($mode, $item)
//     {
//         $user_activity = new UserActivity();
//         if ($mode == 'edit') {
//             $activity = 'Mengubah data Perusahaan dari '.$item['old_item'].' menjadi '.$item['item'];
//         } else if ( $mode == 'add' ) {
//             $activity = 'Menambah data Perusahaan '.$item['item'];
//         } else {
//             $activity = 'Menghapus data Perusahaan '.$item;
//         }
//         $data = [
//             'user_id' => Auth::user()->id,
//             'ua_description' => $activity
//         ];
//         $user_activity->storeData($data);
//     }
    
//     public function checkExistsDataPerusahaan(Request $request)
//     {
//         $check = DataPerusahaan::where(['dp_name' => strtoupper($request->_dp_name)])->exists();
//         if ($check) {
//             $r['status'] = '200';
//         } else {
//             $r['status'] = '400';
//         }
//         return json_encode($r);
//     }
// }

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
        return view('app.data_perusahaan.data_perusahaan', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DataPerusahaan::select('id', 'dp_name', 'dp_npwp', 'dp_description'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('dp_name', 'LIKE', "%$search%")
                        ->orWhere('dp_npwp', 'LIKE', "%$search%")
                        ->orWhere('dp_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    
    public function storeData(Request $request)
    {
        $data_perusahaan = new DataPerusahaan;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'dp_name' => ltrim($request->input('dp_name')),
            'dp_npwp' => $request->input('dp_npwp'),
            'dp_description' => $request->input('dp_description'),
        ];

        $save = $data_perusahaan->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $data_perusahaan = new DataPerusahaan;
        $id = $request->input('_id');
        $save = $data_perusahaan->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsDataPerusahaan(Request $request)
    {
        $check = DataPerusahaan::where(['dp_name' => strtoupper($request->_dp_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }


    // public function exportdata(Request $request)
    // {
    //     $data = DataPerusahaan::select('dp_name', 'dp_npwp', 'dp_description')->get();
    //     $data = $data->map(function ($item) {
    //         $item->dp_npwp = preg_replace('/[.-]/', '', $item->dp_npwp);
    //         return $item;
    //     });
    //     {
    //         return Excel::download(new DataPerusahaanExport, 'data_perusahaan.xlsx');
    //     }
    // }
    public function exportData(Request $request)
    {
        try {
            $type = $request->get('type');

            $fileName = 'Export_Data_Perusahaan_' . date('Y-m-d') . '.xlsx';

            return Excel::download(new DataPerusahaanExport($type), $fileName);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
