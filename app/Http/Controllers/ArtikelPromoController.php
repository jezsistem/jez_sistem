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
use App\Models\StoreTypeDivision;
use App\Models\Store;
use App\Imports\ArtikelPromoImport;

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
            'st_id' => Store::selectRaw('ts_stores.id as sid, CONCAT(st_name) as store')
            ->where('st_delete', '!=', '1')
            ->orderByDesc('sid')->pluck('store', 'sid'),
            'std_id' => StoreTypeDivision::where('dv_delete', '!=', '1')->orderByDesc('id')->pluck('dv_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.artikel_promo.artikel_promo', compact('data'));
//        return 'aaaa';
    }

    public function getDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(ArtikelPromo::select('articles_promo.id as a_id','article_id','p_name','st_code','promo_name','date_start','date_end','promo_disc','p_price_tag','promo_note')
                ->join('stores', 'stores.id', '=', 'articles_promo.st_id')
                ->join('products', 'products.id', '=', 'articles_promo.p_id'))
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('p_id', 'LIKE', "%$search%")
                                ->orWhere('article_id', 'LIKE', "%$search%")
                                ->orWhere('p_name', 'LIKE', "%$search%")
                                ->orWhere('st_code', 'LIKE', "%$search%")
                                ->orWhere('promo_name', 'LIKE', "%$search%")
                                ->orWhere('date_start', 'LIKE', "%$search%")
                                ->orWhere('date_end', 'LIKE', "%$search%")
                                ->orWhere('promo_disc', 'LIKE', "%$search%")
                                ->orWhere('promo_note', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addColumn('article_id', function ($row) {
                    return $row->article_id;
                })
                ->addColumn('p_name', function ($row) {
                    return $row->p_name;
                })
                ->addColumn('promo_disc', function ($row) {
                    return $row->promo_disc . '%';
                })
                ->addColumn('p_price_tag', function ($row) {
                    return number_format($row->p_price_tag);
                })
                ->addColumn('price_discount', function ($row) {
                    $originalPrice = $row->p_price_tag;
                    $discount = $row->promo_disc;

                    $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));

                    return number_format($discountedPrice);
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
            'promo_name' => $request->input('promo_name'),
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'promo_disc' => $request->input('promo_disc'),
            'promo_note' => $request->input('promo_note'),
        ];

        $save = $artikel_promo->storeData($mode, $id, $data);
        if ($save) {
            if ($save) {
                if ($mode == 'add') {
                    $this->UserActivity('menambah artikel promo '.strtoupper($request->input('promo_name')).' '.$request->input('promo_disc'));
                } else {
                    $this->UserActivity('mengubah data diskon '.strtoupper($request->input('promo_name')).' '.$request->input('promo_disc'));
                }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }}

    public function deleteData(Request $request)
    {
        $artikel_promo = new ArtikelPromo;
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

    public function saveArtikelPromoImport(Request $request)
    {
        try {
            if ($request->hasFile('artikel_promo_template')) {
                $file = $request->file('artikel_promo_template');
                $nama_file = time() . '_' . $file->getClientOriginalName();
                $file->move('excel', $nama_file);

                $file_path = public_path('/excel/' . $nama_file);
                Excel::import(new ArtikelPromoImport, $file_path);

                unlink($file_path);

                return response()->json(['status' => '200', 'message' => 'Import berhasil']);
            } else {
                return response()->json(['status' => '400', 'message' => 'File tidak ditemukan']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => '500', 'message' => $e->getMessage()]);
        }
    }


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
