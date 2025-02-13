<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ArtikelPromo;
use App\Models\UserActivity;
//use App\Exports\ArtikelPromoExport;
use Maatwebsite\Excel\Facades\Excel;

class ArtikelPromoController extends Controller
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
         return view('app.artikel_promo.artikel_promo', compact('data'));
//        return 'aaaa';
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(ArtikelPromo::select('id', 'p_id', 'st_id', 'date_start','date_end','promo_cat','promo_price','promo_note'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('p_id', 'LIKE', "%$search%")
                        ->orWhere('st_id', 'LIKE', "%$search%")
                        ->orWhere('date_start', 'LIKE', "%$search%")
                        ->orWhere('date_end', 'LIKE', "%$search%")
                        ->orWhere('promo_cat', 'LIKE', "%$search%")
                        ->orWhere('promo_price', 'LIKE', "%$search%")
                        ->orWhere('promo_note', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    
    public function storeData(Request $request)
    {
        $artikel_promo = new ArtikelPromo;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'p_id' => $request->input('p_id'),
            'st_id' => $request->input('st_id'),
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'promo_cat' => $request->input('promo_cat'),
            'promo_price' => $request->input('promo_price'),
            'promo_note' => $request->input('promo_note'),
        ];

        $save = $artikel_promo->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $artikel_promo = new DataPerusahaan;
        $id = $request->input('_id');
        $save = $artikel_promo->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    // public function checkExistsArtikelPromo(Request $request)
    // {
    //     $check = ArtikelPromo::where(['dp_name' => strtoupper($request->_dp_name)])->exists();
    //     if ($check) {
    //         $r['status'] = '200';
    //     } else {
    //         $r['status'] = '400';
    //     }
    //     return json_encode($r);
    // }

    public function exportData(Request $request)
    {
        try {
            $type = $request->get('type');

            $fileName = 'Export_Artikel_Promo_' . date('Y-m-d') . '.xlsx';

            return Excel::download(new ArtikelPromoExport($type), $fileName);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
