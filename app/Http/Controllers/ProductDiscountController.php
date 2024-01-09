<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ProductDiscount;
use App\Models\ProductDiscountDetail;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StoreTypeDivision;
use App\Models\UserActivity;
use App\Models\Store;
use App\Imports\DiscountImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductDiscountController extends Controller
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
        ];
        return view('app.product_discount.product_discount', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(ProductDiscount::select('product_discounts.id as pd_id', 'store_type_divisions.id as std_id', 'stores.id as st_id', 'st_name', 'dv_name', 'pd_date', 'pd_name', 'pd_type', 'pd_value')
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'product_discounts.std_id')
            ->leftJoin('stores', 'stores.id', '=', 'product_discounts.st_id')
            ->orderByDesc('product_discounts.pd_date'))
            ->editColumn('pd_type_show', function($data) {
                if ($data->pd_type == 'percent') {
                    return '<span class="btn-sm btn-info" style="font-weight:bold;">%</span>';
                } else if ($data->pd_type == 'b1g1') {
                    return '<span class="btn-sm btn-success" style="font-weight:bold; color:white;">B1G1</span>';
                } else {
                    return '<span class="btn-sm btn-warning" style="font-weight:bold; color:white;">Rp</span>';
                }
            })
            ->editColumn('st_id_show', function($data) {
                if (!empty($data->st_id)) {
                    $st_name = $data->st_name;
                } else {
                    $st_name = 'Semua';
                }
                return $st_name;
            })
            ->editColumn('pd_type_show', function($data) {
                if ($data->pd_type == 'percent') {
                    return '<span class="btn-sm btn-info" style="font-weight:bold;">%</span>';
                } else if ($data->pd_type == 'b1g1') {
                    return '<span class="btn-sm btn-success" style="font-weight:bold; color:white;">B1G1</span>';
                } else {
                    return '<span class="btn-sm btn-warning" style="font-weight:bold; color:white;">Rp</span>';
                }
            })
            ->editColumn('article', function($data) {
                if ($data->pd_type == 'percent') {
                    $disc = '%';
                } else if ($data->pd_type == 'b1g1') {
                    $disc = 'B1G1';
                } else {
                    $disc = 'Rp.';
                }
                $item = ProductDiscountDetail::select('pst_id')->where('pd_id', $data->pd_id)->count('pst_id');
                return '<span class="btn-sm btn-primary" data-pd_name="'.$data->pd_name.' ['.$disc.' '.$data->pd_value.'] - '.$data->pd_id.'" data-pd_id="'.$data->pd_id.'" id="discount_item_btn">'.$item.'</span>';
            })
            ->editColumn('pd_date_show', function($data) {
                return date('d-m-Y', strtotime($data->pd_date));
            })
            ->rawColumns(['pd_type_show', 'article'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance
                    ->leftJoin('product_discount_details', 'product_discount_details.pd_id', '=', 'product_discounts.id')
                    ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_discount_details.pst_id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                    ->groupBy('product_discounts.id')
                    ->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw("CONCAT(br_name,' ',p_name,' ',p_color,' ',sz_name) LIKE ?", "%$search%")
                        ->orWhere('pd_name', 'LIKE', "%$search%");
                    });
                }
                if (!empty($request->get('st_id'))) {
                    $instance->where(function($w) use($request){
                        $st_id = $request->get('st_id');
                        if ($st_id != 'all') {
                            $w->where('product_discounts.st_id', '=', $st_id);
                        } else {
                            $w->whereNull('product_discounts.st_id');
                        }
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getDetailDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(ProductDiscountDetail::select('product_discount_details.id as pdd_id',
                'product_stocks.id as pst_id', 'pd_type', 'pd_value', 'p_price_tag', 'ps_price_tag', 'p_sell_price',
                'ps_sell_price', 'p_name', 'p_color', 'br_name', 'sz_name')
            ->leftJoin('product_discounts', 'product_discounts.id', '=', 'product_discount_details.pd_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_discount_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('product_discount_details.pd_id', '=', $request->pd_id))
            ->editColumn('article', function($data) {
                return '['.$data->br_name.'] '.$data->p_name;
            })
            ->editColumn('sell_price', function($data) {
                if (!empty($data->ps_price_tag)) {
                    return number_format($data->ps_price_tag);
                } else {
                    return number_format($data->p_price_tag);
                }
            })
            ->editColumn('sell_price_discount', function($data) {
                $disc_price = 0;
                if (!empty($data->ps_price_tag)) {
                    if ($data->pd_type == 'percent') {
                        $disc_price = $data->ps_price_tag - ($data->ps_price_tag/100 * $data->pd_value);
                    } else if ($data->pd_type == 'amount') {
                        $disc_price = $data->ps_price_tag - $data->pd_value;
                    } else {
                        $disc_price = $data->ps_price_tag;
                    }
                } else {
                    if ($data->pd_type == 'percent') {
                        $disc_price = $data->p_price_tag - ($data->p_price_tag/100 * $data->pd_value);
                    } else if ($data->pd_type == 'amount') {
                        $disc_price = $data->p_price_tag - $data->pd_value;
                    } else {
                        $disc_price = $data->p_price_tag;
                    }
                }
                return number_format($disc_price);
            })
            ->editColumn('action', function($data) {
                return '<span class="btn-sm btn-danger" data-pst_id="'.$data->pst_id.'" id="delete_discount_item"><i class="fa fa-trash text-white"></i></span>';
            })
            ->rawColumns(['action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pd_name', 'LIKE', "%$search%")
                        ->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
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
            return datatables()->of(Product::select('products.id as p_id', 'p_name', 'p_color', 'br_name', 'p_delete')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('p_delete', '!=', '1'))
            ->editColumn('sz_name_show', function($data) {
                $size = ProductStock::select('product_stocks.id as pst_id', 'sz_name')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->where('product_stocks.p_id', '=', $data->p_id)->get();
                $sz = '';
                if (!empty($size)) {
                    foreach ($size as $row) {
                        $sz .= '<input type="checkbox" data-pst_id="'.$row->pst_id.'" id="add_article_to_list"/> '.$row->sz_name.'<br/>';
                    }
                }
                return $sz;
            })
            ->rawColumns(['sz_name_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('p_name', 'LIKE', "%$search%")
                        ->orWhere('br_name', 'LIKE', "%$search%")
                        ->orWhere('p_color', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function addItemToDiscount(Request $request)
    {
        $pst_id = $request->_pst_id;
        $pd_id = $request->_pd_id;
        $check = ProductDiscountDetail::where('pst_id', '=', $pst_id)
        ->where('pd_id', '=', $pd_id)->exists();
        if (!$check) {
            $insert = ProductDiscountDetail::create([
                'pst_id' => $pst_id,
                'pd_id' => $pd_id,
                'pdd_type' => '1',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            if (!empty($insert)) {
                $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('product_stocks.id', $pst_id)
                ->get()->first();
                $this->UserActivity('menambah artikel ['.$item->br_name.'] '.$item->p_name.' '.$item->p_color.' '.$item->sz_name.' ke dalam diskon');
                $r['status'] = '200';
            } else {
                $r['status'] = '419';
            }
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteItemDiscount(Request $request)
    {
        $pst_id = $request->_pst_id;
        $pd_id = $request->_pd_id;
        $check = ProductDiscountDetail::where('pst_id', '=', $pst_id)
        ->where('pd_id', $pd_id)->delete();
        if (!empty($check)) {
            $item = ProductStock::select('p_name', 'br_name', 'sz_name', 'p_color')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('product_stocks.id', $pst_id)
            ->get()->first();
            $this->UserActivity('menghapus artikel ['.$item->br_name.'] '.$item->p_name.' '.$item->p_color.' '.$item->sz_name.' dari daftar diskon');
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeData(Request $request)
    {
        $product_discount = new ProductDiscount;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'pd_name' => $request->input('pd_name'),
            'st_id' => $request->input('st_id'),
            'std_id' => $request->input('std_id'),
            'pd_type' => $request->input('pd_type'),
            'pd_value' => $request->input('pd_value'),
            'pd_date' => $request->input('pd_date'),
        ];

        $save = $product_discount->storeData($mode, $id, $data);
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data diskon '.strtoupper($request->input('pd_name')).' '.$request->input('pd_value'));
            } else {
                $this->UserActivity('mengubah data diskon '.strtoupper($request->input('pd_name')).' '.$request->input('pd_value'));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $id = $request->input('_id');
        $check = ProductDiscountDetail::select('pst_id')->where('pd_id', '=', $id)->get();
        if (!empty($check)) {
            $pst_id = array();
            $delete_pdd = ProductDiscountDetail::where('pd_id', '=', $id)->delete();
            $delete_pd = ProductDiscount::where('id', '=', $id)->delete();
            if (!empty($delete_pd)) {
                $this->UserActivity('menghapus data diskon');
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
            $delete_pd = ProductDiscount::where('id', '=', $id)->delete();
            if (!empty($delete_pd)) {
                $this->UserActivity('menghapus data diskon');
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        }
        return json_encode($r);
    }

    public function checkExistsProductDiscount(Request $request)
    {
        $check = ProductDiscount::where(['pc_name' => strtoupper($request->_pc_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function importData(Request $request)
    {
        if (request()->hasFile('pd_template')) {
            $import = new DiscountImport;
            Excel::import($import, request()->file('pd_template'));
            if ($import->getRowCount() >= 0) {
                $r['status'] = '200';
            } else {
                $r['status'] = '419';
            }
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}