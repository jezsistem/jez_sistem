<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\BannerBrand;
use App\Models\BannerBrandDetail;
use App\Models\ProductSubSubCategory;
use App\Models\UserActivity;
use Maatwebsite\Excel\Facades\Excel;
use Image;
use File;

class WebBannerController extends Controller
{
    protected function validateAccess($slug = null)
    {
        $ma_slug = $slug ?? request()->segment(1);
        $validate = DB::table('user_menu_accesses')
        ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
            'u_id' => Auth::user()->id,
            'ma_slug' => $ma_slug
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
            'segment' => request()->segment(1),
            'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'pssc_id' => ProductSubSubCategory::selectRaw('id, CONCAT(pssc_name) as name')
            ->where('pssc_delete', '!=', '1')
            ->orderBy('pssc_name')->pluck('name', 'id')
        ];
        return view('app.web_banner.web_banner', compact('data'));
    }

    public function indexUpdated()
    {
        $this->validateAccess('web_banner');
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'web_banner')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'pssc_id' => ProductSubSubCategory::selectRaw('id, CONCAT(pssc_name) as name')
            ->where('pssc_delete', '!=', '1')
            ->orderBy('pssc_name')->pluck('name', 'id')
        ];
        return view('app.updated_web_banner.web_banner', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = Banner::select('id', 'bn_image', 'bn_name', 'bn_slug', 'bn_sort', 'bn_filter', 'is_child');

            if (!empty($search)) {
                $query->where('bn_name', 'LIKE', "%$search%");
            }

            $total = $query->count();
            $totalPages = ceil($total / $perPage);

            $query->orderBy('id', 'desc');
            $query->skip(($page - 1) * $perPage)->take($perPage);

            $results = $query->get();

            $data = [];
            $no = ($page - 1) * $perPage + 1;
            foreach ($results as $row) {
                // Count brand
                $brandCount = DB::table('banner_brands')
                    ->where('bn_id', '=', $row->id)
                    ->count();

                // Filter label
                $filterLabels = [
                    '0' => 'Terbaru',
                    '1' => 'Terlaris',
                    '2' => 'Termurah',
                    '3' => 'Termahal',
                    '4' => 'Brand Lokal',
                    '5' => 'Topdeals'
                ];
                $filterLabel = $filterLabels[$row->bn_filter] ?? '-';

                $data[] = [
                    'no' => $no++,
                    'id' => $row->id,
                    'bn_image' => $row->bn_image,
                    'bn_image_url' => $row->bn_image ? asset('api/banner/1905x914') . '/' . $row->bn_image : asset('upload/image/no_image.png'),
                    'bn_name' => $row->bn_name ?? '-',
                    'bn_slug' => $row->bn_slug ?? '-',
                    'is_child' => $row->is_child,
                    'is_child_label' => $row->is_child == '1' ? 'Ya' : 'Tidak',
                    'bn_sort' => $row->bn_sort ?? 0,
                    'bn_filter' => $row->bn_filter,
                    'bn_filter_label' => $filterLabel,
                    'brand_count' => $brandCount,
                ];
            }

            return response()->json([
                'data' => $data,
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
                'per_page' => (int) $perPage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage(),
                'data' => [],
                'total' => 0,
                'total_pages' => 0,
                'current_page' => 1,
                'per_page' => 25
            ], 500);
        }
    }

    public function getBrandDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');
            $bn_id = $request->get('bn_id');

            $query = BannerBrand::select('banner_brands.id as id', 'br_id', 'br_name')
                ->leftJoin('brands', 'brands.id', '=', 'banner_brands.br_id')
                ->where('banner_brands.bn_id', '=', $bn_id);

            if (!empty($search)) {
                $query->where('br_name', 'LIKE', "%$search%");
            }

            $total = $query->count();
            $totalPages = ceil($total / $perPage);

            $query->orderBy('banner_brands.id', 'desc');
            $query->skip(($page - 1) * $perPage)->take($perPage);

            $results = $query->get();

            $data = [];
            $no = ($page - 1) * $perPage + 1;
            foreach ($results as $row) {
                // Count article
                $articleCount = DB::table('banner_brand_details')
                    ->where('bnb_id', '=', $row->id)
                    ->count();

                $data[] = [
                    'no' => $no++,
                    'id' => $row->id,
                    'br_id' => $row->br_id,
                    'br_name' => $row->br_name ?? '-',
                    'article_count' => $articleCount,
                ];
            }

            return response()->json([
                'data' => $data,
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
                'per_page' => (int) $perPage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage(),
                'data' => [],
                'total' => 0,
                'total_pages' => 0,
                'current_page' => 1,
                'per_page' => 25
            ], 500);
        }
    }

    public function getArticleDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');
            $bnb_id = $request->get('bnb_id');

            $query = BannerBrandDetail::select('banner_brand_details.id as id', 'pssc_id', 'pssc_name')
                ->leftJoin('product_sub_sub_categories', 'product_sub_sub_categories.id', '=', 'banner_brand_details.pssc_id')
                ->where('banner_brand_details.bnb_id', '=', $bnb_id);

            if (!empty($search)) {
                $query->where('pssc_name', 'LIKE', "%$search%");
            }

            $total = $query->count();
            $totalPages = ceil($total / $perPage);

            $query->orderBy('banner_brand_details.id', 'desc');
            $query->skip(($page - 1) * $perPage)->take($perPage);

            $results = $query->get();

            $data = [];
            $no = ($page - 1) * $perPage + 1;
            foreach ($results as $row) {
                $data[] = [
                    'no' => $no++,
                    'id' => $row->id,
                    'pssc_id' => $row->pssc_id,
                    'pssc_name' => $row->pssc_name ?? '-',
                ];
            }

            return response()->json([
                'data' => $data,
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
                'per_page' => (int) $perPage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage(),
                'data' => [],
                'total' => 0,
                'total_pages' => 0,
                'current_page' => 1,
                'per_page' => 25
            ], 500);
        }
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Banner::select('id', 'bn_image', 'bn_name', 'bn_slug', 'bn_sort', 'bn_filter', 'is_child'))
            ->editColumn('bn_image_show', function($data){
                if (empty($data->bn_image)) {
                    return '<img src="'.asset('upload/image/no_image.png').'"/>';
                } else {
                    return '<a href="'.asset('api/banner/1905x914').'/'.$data->bn_image.'" target="_blank"><img src="'.asset('api/banner/1905x914').'/'.$data->bn_image.'" style="width:100px;" /></a>';
                }
            })
            ->editColumn('brand', function($data){
                $brand = DB::table('banner_brands')->select('banner_brands.id as id')
                ->leftJoin('banners', 'banners.id', '=', 'banner_brands.bn_id')
                ->where('banner_brands.bn_id', '=', $data->id)
                ->count('banner_brands.id');
                return "<a class='btn btn-primary'data-id='".$data->id."' id='bb_btn'>".$brand."</a>";
            })
            ->editColumn('is_child_show', function($data){
                if ($data->is_child == '1') {
                    $child = 'Ya';
                } else {
                    $child = 'Tidak';
                }
                return $child;
            })
            ->editColumn('bn_filter_show', function($data){
                if ($data->bn_filter == '0') {
                  return 'Terbaru';
                } else if ($data->bn_filter == '1') {
                  return 'Terlaris';
                } else if ($data->bn_filter == '2') {
                  return 'Termurah';
                } else if ($data->bn_filter == '3') {
                  return 'Termahal';
                } else if ($data->bn_filter == '4') {
                  return 'Brand Lokal';
                } else {
                  return 'Topdeals';
                }
            })
            ->rawColumns(['bn_image_show', 'brand', 'is_child_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('bn_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getBrandDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(BannerBrand::select('banner_brands.id as id', 'br_id', 'br_name')
            ->leftJoin('brands', 'brands.id', '=', 'banner_brands.br_id')
            ->where('banner_brands.bn_id', '=', $request->bn_id))
            ->editColumn('article', function($data){
                $article = DB::table('banner_brand_details')->select('banner_brand_details.id as id')
                ->leftJoin('banner_brands', 'banner_brands.id', '=', 'banner_brand_details.bnb_id')
                ->where('banner_brand_details.bnb_id', '=', $data->id)
                ->count('banner_brand_details.id');
                return "<a class='btn btn-primary' data-id='".$data->id."' data-br_id='".$data->br_id."' id='bnb_btn'>".$article."</a>";
            })
            ->rawColumns(['article'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('br_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getArticleDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(BannerBrandDetail::select('banner_brand_details.id as id', 'pssc_id', 'pssc_name')
            ->leftJoin('product_sub_sub_categories', 'product_sub_sub_categories.id', '=', 'banner_brand_details.pssc_id')
            ->where('banner_brand_details.bnb_id', '=', $request->bnb_id))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pssc_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $banner = new Banner;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'bn_name' => $request->input('bn_name'),
            'bn_slug' => $request->input('bn_slug'),
            'bn_sort' => $request->input('bn_sort'),
            'bn_filter' => $request->input('bn_filter'),
            'is_child' => $request->input('is_child'),
        ];

        if ($request->hasFile('bn_image')) {
            $request->validate([
                'bn_image' => 'required|file|mimes:jpg,jpeg,png',
            ]);
            $image = $request->file('bn_image');
            $input['fileName'] = time().'.'.$image->extension();

            $destinationPath = public_path('/api/banner/1905x914');
            $img = Image::make($image->path());
            $img->resize(1905, 914, function ($constraint) {
            })->save($destinationPath.'/'.$input['fileName']);

            if ($mode=='edit') {
                if(File::exists(public_path('api/banner/1905x914/'. $request->_image))){
                    File::delete(public_path('api/banner/1905x914/'. $request->_image));
                }
            }

            $data = array_merge($data,['bn_image' => $input['fileName']]);
        }

        $save = $banner->storeData($mode, $id, $data);
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data banner '.strtoupper($request->input('bn_name')));
            } else {
                $this->UserActivity('mengubah data banner '.strtoupper($request->input('bn_name')));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $banner = new Banner;
        $id = $request->input('_id');
        $item_name = Banner::select('bn_name')->where('id', $id)->get()->first()->bn_name;
        $check = BannerBrand::select('id')->where('bn_id', '=', $id)->get();
        if (!empty($check)) {
          foreach ($check as $row) {
            $check = BannerBrandDetail::where('bnb_id', '=', $row->id)->exists();
            if ($check) {
              $delete = BannerBrandDetail::where('bnb_id', '=', $row->id)->delete();
            }
          }
          $delete = BannerBrand::where('bn_id', '=', $id)->delete();
        }
        $delete = Banner::where('id', '=', $id)->delete();
        if ($delete) {
            $this->UserActivity('menghapus data banner '.$item_name);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeDataBrand(Request $request)
    {
        $banner_brand = new BannerBrand;
        $mode = $request->input('_bb_mode');
        $id = $request->input('_bb_id');

        $data = [
            'br_id' => $request->input('br_id'),
            'bn_id' => $request->input('bn_id'),
        ];

        $save = $banner_brand->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteDataBrand(Request $request)
    {
        $id = $request->input('_id');
        $delete = BannerBrandDetail::where('bnb_id', '=', $id)->delete();
        $delete = BannerBrand::where('id', '=', $id)->delete();
        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeDataArticle(Request $request)
    {
        $banner_brand_detail = new BannerBrandDetail;
        $mode = $request->input('_bbd_mode');
        $id = $request->input('_bbd_id');

        $data = [
            'pssc_id' => $request->input('pssc_id'),
            'bnb_id' => $request->input('bnb_id'),
        ];

        $save = $banner_brand_detail->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteDataArticle(Request $request)
    {
        $id = $request->input('_id');
        $delete = BannerBrandDetail::where('id', '=', $id)->delete();
        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function reloadArticle(Request $request)
    {
      $br_id = $request->br_id;
      $data = [
          'pssc_id' => ProductSubSubCategory::selectRaw('id, CONCAT(pssc_name) as name')
          ->where('pssc_delete', '!=', '1')
          ->orderBy('pssc_name')->pluck('name', 'id')
      ];
      return view('app.web_banner._reload_article', compact('data'));
    }
}
