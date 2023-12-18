<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;
use App\Models\User;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductSubSubCategoryTestController extends Controller
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
            'pc_id' => ProductCategory::where('pc_delete', '!=', '1')->orderByDesc('id')->pluck('pc_name', 'id'),
            'psc_id' => ProductSubCategory::where('psc_delete', '!=', '1')->orderByDesc('id')->pluck('psc_name', 'id'),
        ];
        return view('app.product_sub_sub_category_test.product_sub_sub_category', compact('data'));
    }

    public function getDatatables(Request $request)
    {
//        if(request()->ajax()) {
//            return datatables()->of(ProductSubSubCategory::select('product_sub_sub_categories.id as id', 'psc_name', 'pssc_name', 'pssc_weight', 'pssc_description')
//            ->join('product_sub_categories', 'product_sub_categories.id', '=', 'product_sub_sub_categories.psc_id')
//            ->where('pssc_delete', '!=', '1')
//            ->where('psc_id', '=', $request->psc_id))
//            ->filter(function ($instance) use ($request) {
//                if (!empty($request->get('search'))) {
//                    $instance->where(function($w) use($request){
//                        $search = $request->get('search');
//                        $w->orWhere('pssc_name', 'LIKE', "%$search%")
//                        ->orWhere('pssc_description', 'LIKE', "%$search%");
//                    });
//                }
//            })
//            ->addIndexColumn()
//            ->make(true);
//        }
        if(request()->ajax()) {
            return datatables()->of(ProductSubSubCategory::select('product_sub_sub_categories.id as id', 'pssc_name', 'pssc_weight', 'pssc_description')
            ->where('pssc_delete', '!=', '1'))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pssc_name', 'LIKE', "%$search%")
                        ->orWhere('pssc_description', 'LIKE', "%$search%");
                    });
                }

                if (!empty($request->get('psc_id'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('psc_id');
                        $w->orWhere('psc_id', '=', $search);
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $product_sub_sub_category = new ProductSubSubCategory;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'psc_id' => $request->input('psc_id'),
            'pssc_name' => ltrim($request->input('pssc_name')),
            'pssc_weight' => $request->input('pssc_weight'),
            'pssc_description' => $request->input('pssc_description'),
            'pssc_delete' => '0',
        ];

        $save = $product_sub_sub_category->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $product_sub_sub_category = new ProductSubSubCategory;
        $id = $request->input('_id');
        $save = $product_sub_sub_category->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function reloadProductSubSubCategory(Request $request)
    {
        $data = [
            'pssc_id' => ProductSubSubCategory::where('pssc_delete', '!=', '1')->where('psc_id', '=', $request->_psc_id)->orderByDesc('id')->pluck('pssc_name', 'id'),
		];
        return view('app.product_sub_sub_category._reload_product_sub_sub_category', compact('data'));
    }

    public function reloadPssc(Request $request)
    {
        $data = [
            'pssc_id' => ProductSubSubCategory::where('pssc_delete', '!=', '1')->orderByDesc('id')->pluck('pssc_name', 'id'),
		];
        return view('app.product._reload_pssc', compact('data'));
    }

    public function  getProductSubSubCategory(Request $request)
    {
        $pssc_id = $request->_pssc_id;

        if(request()->ajax()) {
            $query = Product::where('p_delete', '!=', '1')
                ->where('pssc_id', '=', $pssc_id)
                ->orderByDesc('id');

            return datatables()->of($query)
                ->addIndexColumn()
                ->make(true);
        }
    }
}
