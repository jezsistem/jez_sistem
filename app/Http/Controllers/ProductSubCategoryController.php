<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;

class ProductSubCategoryController extends Controller
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
        ];
        return view('app.product_sub_category.product_sub_category', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(ProductSubCategory::select('product_sub_categories.id as id', 'pc_name', 'psc_name', 'psc_slug', 'psc_description')
            ->join('product_categories', 'product_categories.id', '=', 'product_sub_categories.pc_id')
            ->where('psc_delete', '!=', '1')
            ->where('pc_id', '=', $request->pc_id))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('psc_name', 'LIKE', "%$search%")
                        ->orWhere('psc_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $product_sub_category = new ProductSubCategory;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'pc_id' => $request->input('pc_id'),
            'psc_name' => ltrim($request->input('psc_name')),
            'psc_slug' => strtolower($request->input('psc_slug')),
            'psc_description' => $request->input('psc_description'),
            'psc_delete' => '0',
        ];

        $save = $product_sub_category->storeData($mode, $id, $data);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $product_sub_category = new ProductSubCategory;
        $id = $request->input('_id');
        $save = $product_sub_category->deleteData($id);
        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function reloadProductSubCategory(Request $request)
    {
        $data = [
            'psc_id' => ProductSubCategory::where('psc_delete', '!=', '1')->where('pc_id', '=', $request->_pc_id)->orderByDesc('id')->pluck('psc_name', 'id'),
		];
        return view('app.product_sub_sub_category._reload_product_sub_category', compact('data'));
    }

    public function reloadPsc(Request $request)
    {
        $data = [
            'psc_id' => ProductSubCategory::where('psc_delete', '!=', '1')->orderByDesc('id')->pluck('psc_name', 'id'),
		];
        return view('app.product._reload_psc', compact('data'));
    }

    public function checkExistsProductSubCategory(Request $request)
    {
        $check = ProductSubCategory::where(['psc_name' => strtoupper($request->_psc_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
