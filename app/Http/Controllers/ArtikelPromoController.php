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
use App\Exports\ArtikelPromoExport;
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

    protected function UserActivity($activity)
    {
        UserActivity::create([
            'user_id' => Auth::user()->id,
            'ua_description' => $activity,
            'created_at' => date('Y-m-d H:i:s')
        ]);
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
            'stores' => Store::where('st_delete', '!=', '1')->orderBy('st_name')->get(),
        ];
        return view('app.artikel_promo.artikel_promo', compact('data'));
        //        return 'aaaa';
    }

    public function getDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(ArtikelPromo::select('articles_promo.id as a_id', 'article_id', 'p_name','st_id','stores.st_code as st_code', 'promo_name', 'date_start', 'date_end', 'promo_disc', 'p_price_tag', 'promo_note')
                ->join('stores', 'stores.id', '=', 'articles_promo.st_id')
                ->join('products', 'products.id', '=', 'articles_promo.p_id'))
                ->filter(function ($instance) use ($request) {
                    $search = $request->get('search');
                    $dateRange = $request->get('date_start');
                    if (!empty($search)) {
                        $instance->where(function ($query) use ($search) {
                            $query->orWhere('p_id', 'LIKE', "%$search%")
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
                    if (!empty($dateRange)) {
                        $dates = explode('|', $dateRange);
                        if (count($dates) === 2) {
                            $instance->whereBetween('date_start', [$dates[0], $dates[1]]);
                        } else {
                            $instance->whereDate('date_start', $dates[0]);
                        }
                    }
                    if (!empty($request->get('artikel_promo_store'))) {
                        $instance->where('st_id', $request->get('artikel_promo_store'));
                    }
                })
                ->addColumn('article_id', function ($row) {
                    return $row->article_id;
                })
                ->addColumn('p_name', function ($row) {
                    return $row->p_name;
                })
                ->addColumn('st_code', function ($row) {
                    return $row->st_code;
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

    public function getArtikelPromoDetails(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $artikelPromo = ArtikelPromo::select('articles_promo.id as a_id', 'articles_promo.article_id', 'articles_promo.st_id', 'articles_promo.promo_name', 'articles_promo.date_start', 'articles_promo.date_end', 'articles_promo.promo_disc', 'articles_promo.promo_note')
                ->join('stores', 'stores.id', '=', 'articles_promo.st_id')
                ->join('products', 'products.id', '=', 'articles_promo.p_id')
                ->where('articles_promo.id', $id)
                ->first();

            if ($artikelPromo) {
                return response()->json([
                    'status' => '200',
                    'data' => [
                        'id' => $artikelPromo->a_id,
                        'article_id' => $artikelPromo->article_id,
                        'st_id' => $artikelPromo->st_id,
                        'promo_name' => $artikelPromo->promo_name,
                        'start_date' => $artikelPromo->date_start,
                        'end_date' => $artikelPromo->date_end,
                        'promo_disc' => $artikelPromo->promo_disc,
                        'promo_note' => $artikelPromo->promo_note,
                    ]
                ]);
            } else {
                return response()->json(['status' => '404', 'message' => 'Data not found']);
            }
        }
    }


    public function storeData(Request $request)
    {
        $mode = $request->input('_mode'); // 'add' or 'edit'
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

        $article_id = $request->input('article_id');
        $product = DB::table('products')->where('article_id', $article_id)->first();

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found for article_id: ' . $article_id
            ]);
        }

        $data['p_id'] = $product->id;

        if ($mode === 'add') {
            $artikelPromo = new ArtikelPromo();
            $artikelPromo->timestamps = false; // Disable timestamps
            $artikelPromo->fill($data);
            if ($artikelPromo->save()) {
                $this->UserActivity('menambah artikel promo ' . strtoupper($request->input('promo_name')) . ' ' . $request->input('promo_disc'));
                return response()->json(['status' => '200', 'message' => 'Data Successfully Added JEZ']);
            } else {
                return response()->json(['status' => '400', 'message' => 'Failed to add data JEZ']);
            }
        } elseif ($mode === 'edit') {
            $artikelPromo = ArtikelPromo::find($id);
            if ($artikelPromo) {
                $artikelPromo->timestamps = false; // Disable timestamps
                $artikelPromo->fill($data);
                if ($artikelPromo->save()) {
                    $this->UserActivity('mengubah data diskon ' . strtoupper($request->input('promo_name')) . ' ' . $request->input('promo_disc'));
                    return response()->json(['status' => '200', 'message' => 'Data successfully updated JEZ']);
                } else {
                    return response()->json(['status' => '400', 'message' => 'Failed to update data JEZ']);
                }
            } else {
                return response()->json(['status' => '404', 'message' => 'Data tidak ditemukan']);
            }
        } else {
            return response()->json(['status' => '400', 'message' => 'Mode tidak valid']);
        }
    }


    public function deleteData(Request $request)
    {
        $id = $request->input('_id');
        $artikelPromo = ArtikelPromo::find($id);

        if ($artikelPromo) {
            $delete = $artikelPromo->delete();
            if ($delete) {
                // $this->UserActivity('menghapus artikel promo dengan ID ' . $id);
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
            $r['status'] = '404';
            $r['message'] = 'Data tidak ditemukan';
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
