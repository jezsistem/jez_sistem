<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductSubCategory;
use Image;
use File;

class WebSubCategoryController extends Controller
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
        return view('app.web_sub_category.web_sub_category', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(ProductSubCategory::select('id', 'psc_name', 'psc_banner', 'psc_slug')
            ->where('psc_delete', '!=', '1'))
            ->editColumn('psc_banner_show', function($data){
                if (!empty($data->psc_banner)) {
                    $image = "<img style='width:100px;' src='".asset('api/sub_category/banner')."/".$data->psc_banner."' alt='banner_image'/>";
                } else {
                    $image = "<img src='".asset('api/noimage.png')."' alt='banner_image'/>";
                }
                return $image;
            })
            ->rawColumns(['psc_banner_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(psc_name) LIKE ?', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $sub_category = new ProductSubCategory;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'psc_slug' => strtolower($request->input('psc_slug'))
        ];

        if ($request->hasFile('psc_banner')) {
            $request->validate([
                'psc_banner' => 'required|file|mimes:jpg,jpeg,tmp',
            ]);
            $image = $request->file('psc_banner');
            $input['fileName'] = time().'.'.$image->extension();

            $destinationPath = public_path('/api/sub_category/banner');
            $img = Image::make($image->path());
            $img->save($destinationPath.'/'.$input['fileName']);

            if ($mode=='edit') {
                if(File::exists(public_path('api/sub_category/banner/'. $request->_banner))){
                    File::delete(public_path('api/sub_category/banner/'. $request->_banner));
                }
            }

            $data = array_merge($data,['psc_banner' => $input['fileName']]);
        }

        $save = $sub_category->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
