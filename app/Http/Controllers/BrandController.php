<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Brand;
use App\Models\UserActivity;
use App\Imports\BrandImport;
use Maatwebsite\Excel\Facades\Excel;
use Image;
use File;

class BrandController extends Controller
{
    protected function validateAccess()
    {
        $segment = request()->segment(1);
        
        // Handle V2 routes - remove _v2 suffix for validation
        $slugToCheck = str_replace('_v2', '', $segment);
        
        $validate = DB::table('user_menu_accesses')
            ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
                'u_id' => Auth::user()->id,
                'ma_slug' => $slugToCheck
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
        $segment = request()->segment(1);
        $slugToCheck = str_replace('_v2', '', $segment);
        
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', $slugToCheck)->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => $segment,
        ];
        return view('app.brand.brand', compact('data'));
    }

    /**
     * Display brand management page (V2 - New Layout)
     */
    public function indexV2()
    {
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        
        $segment = request()->segment(1);
        $slugToCheck = str_replace('_v2', '', $segment);
        
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', $slugToCheck)->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => $segment,
        ];
        return view('app.updated_brand.brand', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');

            $query = Brand::select('id', 'br_image', 'br_banner', 'br_name', 'br_slug', 'br_description', 'is_local')
                ->where('br_delete', '!=', '1');

            if (!empty($search)) {
                $query->where(function ($w) use ($search) {
                    $w->where('br_name', 'LIKE', "%$search%")
                        ->orWhere('br_description', 'LIKE', "%$search%");
                });
            }

            $total = $query->count();
            $totalPages = ceil($total / $perPage);

            $query->orderBy('id', 'desc');
            $query->skip(($page - 1) * $perPage)->take($perPage);

            $results = $query->get();

            $data = [];
            $no = ($page - 1) * $perPage + 1;
            foreach ($results as $row) {
                $br_image_url = !empty($row->br_image) ? asset('api/brand/thumbs/' . $row->br_image) : asset('upload/image/no_image.png');
                $br_banner_url = !empty($row->br_banner) ? asset('api/brand/banner/' . $row->br_banner) : asset('upload/image/no_image.png');
                
                $data[] = [
                    'no' => $no++,
                    'id' => $row->id,
                    'br_image' => $row->br_image ?? null,
                    'br_image_url' => $br_image_url,
                    'br_banner' => $row->br_banner ?? null,
                    'br_banner_url' => $br_banner_url,
                    'br_name' => $row->br_name ?? '-',
                    'br_slug' => $row->br_slug ?? '-',
                    'br_description' => $row->br_description ?? '-',
                    'is_local' => $row->is_local ?? '0',
                    'is_local_label' => ($row->is_local ?? '0') == '1' ? 'Ya' : 'Tidak',
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
            Log::error('Error in BrandController::getDatatablesForSimple', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
        if (request()->ajax()) {
            return datatables()->of(Brand::select('id', 'br_image', 'br_banner', 'br_name', 'br_slug', 'br_description', 'is_local')
                ->where('br_delete', '!=', '1'))
                ->editColumn('br_image_show', function ($data) {
                    if (empty($data->br_image)) {
                        return '<img src="' . asset('upload/image/no_image.png') . '"/>';
                    } else {
                        return '<a href="' . asset('api/brand') . '/' . $data->br_image . '" target="_blank"><img src="' . asset('api/brand/thumbs') . '/' . $data->br_image . '" /></a>';
                    }
                })
                ->editColumn('br_banner_show', function ($data) {
                    if (empty($data->br_banner)) {
                        return '<img src="' . asset('upload/image/no_image.png') . '"/>';
                    } else {
                        return '<a href="' . asset('api/brand/banner') . '/' . $data->br_banner . '" target="_blank"><img style="width:100px;" src="' . asset('api/brand/banner') . '/' . $data->br_banner . '" /></a>';
                    }
                })
                ->editColumn('is_local_show', function ($data) {
                    if ($data->is_local == '1') {
                        $is_local = 'Ya';
                    } else {
                        $is_local = 'Tidak';
                    }
                    return $is_local;
                })
                ->rawColumns(['br_image_show', 'br_banner_show', 'is_local_show'])
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('br_name', 'LIKE', "%$search%")
                                ->orWhere('br_description', 'LIKE', "%$search%");
                        });
                    }
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $brand = new Brand;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'br_name' => strtoupper($request->input('br_name')),
            'br_slug' => strtolower($request->input('br_slug')),
            'br_description' => $request->input('br_description'),
            'is_local' => $request->input('is_local'),
            'br_delete' => '0',
        ];

        if ($request->hasFile('br_image')) {
            $request->validate([
                'br_image' => 'required|file|mimes:jpg,jpeg,png',
            ]);
            $image = $request->file('br_image');
            $input['fileName'] = time() . '.' . $image->extension();

            $destinationPath = public_path('/api/brand/thumbs');
            $img = Image::make($image->path());
            $img->resize(100, 100, function ($constraint) {
            })->save($destinationPath . '/' . $input['fileName']);
            $destinationPath = public_path('/api/brand');
            $image->move($destinationPath, $input['fileName']);

            if ($mode == 'edit') {
                if (File::exists(public_path('api/brand/' . $request->_image))) {
                    File::delete(public_path('api/brand/' . $request->_image));
                }
            }

            $data = array_merge($data, ['br_image' => $input['fileName']]);
        }

        if ($request->hasFile('br_banner')) {
            $request->validate([
                'br_banner' => 'required|file|mimes:jpg,jpeg,png',
            ]);
            $image = $request->file('br_banner');
            $input['fileName'] = (time() + 1) . '.' . $image->extension();

            $destinationPath = public_path('/api/brand/banner');
            $image->move($destinationPath, $input['fileName']);

            if ($mode == 'edit') {
                if (File::exists(public_path('api/brand/banner/' . $request->_banner))) {
                    File::delete(public_path('api/brand/banner/' . $request->_banner));
                }
            }

            $data = array_merge($data, ['br_banner' => $input['fileName']]);
        }

        $save = $brand->storeData($mode, $id, $data);
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data brand ' . strtoupper($request->input('br_name')));
            } else {
                $this->UserActivity('mengubah data brand ' . strtoupper($request->input('br_name')));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteBrandImage(Request $request)
    {
        $id = $request->post('id');
        $image = $request->post('image');
        if (File::exists(public_path('api/brand/' . $image))) {
            File::delete(public_path('api/brand/' . $image));
        }
        if (File::exists(public_path('api/brand/' . $image))) {
            File::delete(public_path('api/brand/' . $image));
        }
        $brand = Brand::where('id', '=', $id)->update([
            'br_image' => null
        ]);
        if (!empty($brand)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteBannerImage(Request $request)
    {
        $id = $request->post('id');
        $image = $request->post('image');

        if (File::exists(public_path('api/brand/banner/' . $image))) {
            File::delete(public_path('api/brand/banner/' . $image));
        }
        if (File::exists(public_path('api/brand/banner/' . $image))) {
            File::delete(public_path('api/brand/banner/' . $image));
        }
        $brand = Brand::where('id', '=', $id)->update([
            'br_banner' => null
        ]);
        if (!empty($brand)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $brand = new Brand;
        $id = $request->input('_id');
        $item_name = Brand::select('br_name')->where('id', $id)->get()->first()->br_name;
        $save = $brand->deleteData($id);
        if ($save) {
            $this->UserActivity('menghapus data brand ' . $item_name);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function importData()
    {
        if (request()->hasFile('ps_template')) {
            Excel::import(new BrandImport, request()->file('br_template'));
            $this->UserActivity('mengimport data brand ');
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsBrand(Request $request)
    {
        $check = Brand::where(['br_name' => strtoupper($request->_br_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
