<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\CategorySlug;
use App\Models\ProductSubCategory;
use Image;
use File;

class WebCategoryController extends Controller
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
            'psc_id' => ProductSubCategory::where('psc_delete', '!=', '1')->orderByDesc('id')->pluck('psc_name', 'id'),
        ];
        return view('app.web_category.web_category', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(CategorySlug::selectRaw('ts_category_slugs.id as id, psc_id, cs_slug, cs_title, psc_name, cs_image, cs_banner, cs_sub_category')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'category_slugs.psc_id')
            ->where('psc_delete', '!=', '1'))
            ->editColumn('cs_image_show', function($data){
                if (!empty($data->cs_image)) {
                    $image = "<img style='width:100px;' src='".asset('api/category_slug/100')."/".$data->cs_image."' alt='main_image'/>";
                } else {
                    $image = "<img src='".asset('api/noimage.png')."' alt='main_image'/>";
                }
                return $image;
            })
            ->editColumn('cs_banner_show', function($data){
                if (!empty($data->cs_banner)) {
                    $image = "<img style='width:100px;' src='".asset('api/category_slug/banner')."/".$data->cs_banner."' alt='banner_image'/>";
                } else {
                    $image = "<img src='".asset('api/noimage.png')."' alt='banner_image'/>";
                }
                return $image;
            })
            ->rawColumns(['cs_image_show', 'cs_banner_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(cs_title) LIKE ?', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $category_slug = new CategorySlug;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'psc_id' => $request->input('psc_id'),
            'cs_title' => $request->input('cs_title'),
            'cs_slug' => $request->input('cs_slug'),
            'cs_sub_category' => $request->input('cs_sub_category'),
        ];

        if ($request->hasFile('cs_image')) {
            $request->validate([
                'cs_image' => 'required|file|mimes:jpg,jpeg,png',
            ]);
            $image = $request->file('cs_image');
            $input['fileName'] = time().'.'.$image->extension();

            $destinationPath = public_path('/api/category_slug/100');
            $img = Image::make($image->path());
            $img->resize(100, 100, function ($constraint) {
            })->save($destinationPath.'/'.$input['fileName']);

            if ($mode=='edit') {
                if(File::exists(public_path('api/category_slug/100/'. $request->_image))){
                    File::delete(public_path('api/category_slug/100/'. $request->_image));
                }
            }

            $data = array_merge($data,['cs_image' => $input['fileName']]);
        }

        if ($request->hasFile('cs_banner')) {
            $request->validate([
                'cs_banner' => 'required|file|mimes:jpg,jpeg,png',
            ]);
            $image = $request->file('cs_banner');
            $input['fileName'] = time().'.'.$image->extension();

            $destinationPath = public_path('/api/category_slug/banner');
            $img = Image::make($image->path());
            $img->save($destinationPath.'/'.$input['fileName']);

            if ($mode=='edit') {
                if(File::exists(public_path('api/category_slug/banner/'. $request->_banner))){
                    File::delete(public_path('api/category_slug/banner/'. $request->_banner));
                }
            }

            $data = array_merge($data,['cs_banner' => $input['fileName']]);
        }

        $save = $category_slug->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $id = $request->input('_id');
        $delete = CategorySlug::where('id', '=', $id)->delete();
        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
