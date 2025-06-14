<?php

namespace App\Http\Controllers;

use App\Exports\PromoRecommendationDetailExport;
use App\Exports\PromoRecommendationExport;
use App\Imports\PromoRecommendationImport;
use App\Models\PromoRecommendation;
use App\Models\PromoRecommendationDetail;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PromoRecommendationController extends Controller
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
            // 'st_id' => Store::selectRaw('ts_stores.id as sid, CONCAT(st_name) as store')
            //     ->where('st_delete', '!=', '1')
            //     ->orderByDesc('sid')->pluck('store', 'sid'),
            // 'std_id' => StoreTypeDivision::where('dv_delete', '!=', '1')->orderByDesc('id')->pluck('dv_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.promo_recommendation.promo_recommendation', compact('data'));
        //        return 'aaaa';
    }

    public function getDatatables(Request $request)
    {
        if (request()->ajax()) {
            return datatables()->of(PromoRecommendation::select('promo_recommendations.id as pr_id', 'pr_code', 'channel', 'created_at', 'updated_at')->orderBy('created_at', 'DESC'))
                ->filter(function ($instance) use ($request) {
                    $search = $request->get('search');
                    if (!empty($search)) {
                        $instance->where(function ($query) use ($search) {
                            $query->orWhere('pr_code', 'LIKE', "%$search%");
                        });
                    }

                    if ($request->has('channel') && $request->get('channel') != '') {
                        $instance->where('channel', $request->get('channel'));
                    }

                    if (
                        $request->has('date_start') && $request->has('date_end') &&
                        $request->get('date_start') != '' && $request->get('date_end') != ''
                    ) {
                        $instance->whereBetween(
                            DB::raw('DATE(created_at)'),
                            [$request->get('date_start'), $request->get('date_end')]
                        );
                    } elseif ($request->has('date_start') && $request->get('date_start') != '') {
                        $instance->whereDate('created_at', '>=', $request->get('date_start'));
                    } elseif ($request->has('date_end') && $request->get('date_end') != '') {
                        $instance->whereDate('created_at', '<=', $request->get('date_end'));
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getPromoRecommendationDetails(Request $request)
    {
        $id = $request->input('pr_id');
        if (request()->ajax()) {
            return datatables()->of(PromoRecommendationDetail::query()
                ->select('promo_recommendation_details.id as prd_id', 'products.article_id', 'products.p_name', 'promo_recommendation_details.discount', 'promo_recommendation_details.notes', 'products.p_price_tag')
                ->join('products', 'products.id', '=', 'promo_recommendation_details.p_id')
                ->where('promo_recommendation_details.pr_id', $id))
                ->addColumn('promo_disc', function ($row) {
                    return $row->discount . '%';
                })
                ->addColumn('p_price_tag', function ($row) {
                    return number_format($row->p_price_tag);
                })
                ->addColumn('price_discount', function ($row) {
                    $originalPrice = $row->p_price_tag;
                    $discount = $row->discount;

                    $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));

                    return number_format($discountedPrice);
                })
                ->filter(function ($instance) use ($request) {
                    $search = $request->get('search');
                    if (!empty($search)) {
                        $instance->where(function ($query) use ($search) {
                            $query->orWhere('article_id', 'LIKE', "%$search%");
                            $query->orWhere('p_name', 'LIKE', "%$search%");
                            $query->orWhere('notes', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    // public function storeData(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $mode = $request->input('_mode'); // 'add' or 'edit'
    //         $id = $request->input('_id');

    //         // Validate required fields
    //         $request->validate([
    //             'p_id' => 'nullable|exists:products,id',
    //             'channel' => 'required|in:ONLINE,OFFLINE',
    //             'discount' => 'required|numeric|min:0|max:100',
    //             'notes' => 'nullable|string|max:500',
    //             'article_id' => 'required|string|max:255',
    //         ]);

    //         $data = [
    //             'p_id' => $request->input('p_id'),
    //             'channel' => $request->input('channel'),
    //             'discount' => $request->input('discount'),
    //             'notes' => $request->input('notes'),
    //             ''
    //         ];

    //         $article_id = $request->input('article_id');
    //         $product = DB::table('products')->where('article_id', $article_id)->first();

    //         if (!$product) {
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 404,
    //                 'message' => 'Product not found for article_id: ' . $article_id
    //             ]);
    //         }

    //         $data['p_id'] = $product->id;

    //         if ($mode === 'add') {
    //             $checkExists = PromoRecommendation::query()->join('products', 'products.id', 'p_id')->where('products.article_id', $article_id)->exists();
    //             if ($checkExists) {
    //                 DB::rollBack();
    //                 return response()->json(['status' => '403', 'message' => 'Rekomendasi Promo dengan Artikel ' . $article_id . ' sudah ada']);
    //             }

    //             $promoRecommendation = new PromoRecommendation();
    //             $data['created_at'] = now();
    //             $data['updated_at'] = now();
    //             $promoRecommendation->fill($data);

    //             if ($promoRecommendation->save()) {
    //                 $this->UserActivity('menambah artikel promo ' . strtoupper($request->input('promo_name')) . ' ' . $request->input('promo_disc'));
    //                 DB::commit();
    //                 return response()->json(['status' => '200', 'message' => 'Data Successfully Added JEZ']);
    //             } else {
    //                 DB::rollBack();
    //                 return response()->json(['status' => '400', 'message' => 'Failed to add data JEZ']);
    //             }
    //         } elseif ($mode === 'edit') {
    //             $promoRecommendation = PromoRecommendation::find($id);
    //             if ($promoRecommendation) {
    //                 $promoRecommendation->fill($data);

    //                 if ($promoRecommendation->save()) {
    //                     $this->UserActivity('mengubah data diskon ' . strtoupper($request->input('promo_name')) . ' ' . $request->input('promo_disc'));
    //                     DB::commit();
    //                     return response()->json(['status' => '200', 'message' => 'Data successfully updated JEZ']);
    //                 } else {
    //                     DB::rollBack();
    //                     return response()->json(['status' => '400', 'message' => 'Failed to update data JEZ']);
    //                 }
    //             } else {
    //                 DB::rollBack();
    //                 return response()->json(['status' => '404', 'message' => 'Data tidak ditemukan']);
    //             }
    //         } else {
    //             DB::rollBack();
    //             return response()->json(['status' => '400', 'message' => 'Mode tidak valid']);
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['status' => '500', 'message' => 'An error occurred: ' . $e->getMessage()]);
    //     }
    // }


    public function deleteData(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('_id');
            $artikelPromo = PromoRecommendation::find($id);

            if ($artikelPromo) {
                $delete = $artikelPromo->delete();
                if ($delete) {
                    // $this->UserActivity('menghapus artikel promo dengan ID ' . $id);
                    DB::commit();
                    $r['status'] = '200';
                } else {
                    DB::rollBack();
                    $r['status'] = '400';
                }
            } else {
                DB::rollBack();
                $r['status'] = '404';
                $r['message'] = 'Data tidak ditemukan';
            }

            return json_encode($r);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => '500', 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function savePromoRecommendationImport(Request $request)
    {
        try {
            if ($request->hasFile('promo_recommendation_template')) {
                $file = $request->file('promo_recommendation_template');
                $nama_file = time() . '_' . $file->getClientOriginalName();
                $file->move('excel', $nama_file);

                $file_path = public_path('/excel/' . $nama_file);
                Excel::import(new PromoRecommendationImport, $file_path);

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
            $search = isset($request->search) ? $request->search : '';
            $channel = isset($request->channel) ? $request->channel : '';
            $date_start = isset($request->date_start) ? $request->date_start : '';
            $date_end = isset($request->date_end) && $request->date_end !== 'undefined' ? $request->date_end : '';

            $fileName = 'Export_Rekomendasi_Promo';
            if (!empty($search)) {
                $fileName .= '_' . str_replace(' ', '_', $search);
            }
            if (!empty($channel)) {
                $fileName .= '_' . $channel;
            }
            if (!empty($date_start)) {
                $fileName .= '_' . $date_start;
            }
            if (!empty($date_end)) {
                $fileName .= '_' . $date_end;
            }
            $fileName .= '_' . date('Y-m-d') . '.xlsx';

            return Excel::download(new PromoRecommendationExport($search, $channel, $date_start, $date_end), $fileName);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function exportDetailData(Request $request)
    {
        $id = $request->input('pr_id');
        if (!$id) {
            return response()->json(['status' => '400', 'message' => 'ID tidak ditemukan']);
        }

        $pr_code = PromoRecommendation::find($id);
        try {

            $fileName = 'Export_'. $pr_code->pr_code .'_' . date('Y-m-d') . '.xlsx';

            return Excel::download(new PromoRecommendationDetailExport($id), $fileName);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
