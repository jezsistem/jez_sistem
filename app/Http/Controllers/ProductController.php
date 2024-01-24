<?php

namespace App\Http\Controllers;

use App\Exports\ProductArticleExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Product;
use App\Models\Size;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;
use App\Models\ProductLocationSetup;
use App\Models\ProductUnit;
use App\Models\ProductSupplier;
use App\Models\ProductStock;
use App\Models\Brand;
use App\Models\MainColor;
use App\Models\Gender;
use App\Models\Season;
use App\Models\UserActivity;
use App\Imports\ProductImport;
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
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
            'segment' => request()->segment(1),
            'total_product' => Product::where('p_delete', '!=', '1')->get()->count(),
            'total_footwear' => Product::where('p_delete', '!=', '1')->where('pc_id', '=', '1')->get()->count(),
            'total_apparel' => Product::where('p_delete', '!=', '1')->where('pc_id', '=', '2')->get()->count(),
            'total_accessories' => Product::where('p_delete', '!=', '1')->where('pc_id', '=', '3')->get()->count(),
            'total_others' => Product::where('p_delete', '!=', '1')->where('pc_id', '=', '4')->get()->count(),
            'pc_id' => ProductCategory::where('pc_delete', '!=', '1')->orderByDesc('id')->pluck('pc_name', 'id'),
            'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'pu_id' => ProductUnit::where('pu_delete', '!=', '1')->orderByDesc('id')->pluck('pu_name', 'id'),
            'mc_id' => MainColor::where('mc_delete', '!=', '1')->orderByDesc('id')->pluck('mc_name', 'id'),
            'ps_id' => ProductSupplier::where('ps_delete', '!=', '1')->orderByDesc('id')->pluck('ps_name', 'id'),
            'gn_id' => Gender::where('gn_delete', '!=', '1')->orderByDesc('id')->pluck('gn_name', 'id'),
            'ss_id' => Season::where('ss_delete', '!=', '1')->orderByDesc('id')->pluck('ss_name', 'id'),
            'sz_id' => Size::where('sz_delete', '!=', '1')->orderByDesc('id')->pluck('sz_name', 'id'),
            'sz_schema_id' => Size::where('sz_delete', '!=', '1')->whereNotNull('sz_schema')->orderByDesc('id')->distinct()->pluck('sz_schema'),
            'psc_id' => ProductSubCategory::where('psc_delete', '!=', '1')->orderByDesc('id')->pluck('psc_name', 'id'),
            'pssc_id' => ProductSubSubCategory::where('pssc_delete', '!=', '1')->orderByDesc('id')->pluck('pssc_name', 'id'),
        ];

        return view('app.product.product', compact('data'));
    }

//    public function getDatatables(Request $request)
//    {
//        if(request()->ajax()) {
//            if ($request->pc_id == 'all') {
//                return datatables()->of(Product::select(
//                'products.id as pid',
//                'br_id', 'pc_id',
//                'psc_id',
//                'pssc_id',
//                'mc_id', 'ps_id', 'pu_id', 'gn_id', 'ss_id', 'p_code', 'p_name', 'p_description', 'p_aging', 'p_color', 'mc_name', 'br_name', 'ps_name', 'p_price_tag', 'p_purchase_price', 'p_sell_price', 'p_weight', 'p_active')
//                ->join('brands', 'brands.id', '=', 'products.br_id')
//                ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
//                ->join('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
//                ->where('p_delete', '!=', '1'))
//                ->editColumn('p_name_show', function($data){
//                    return '<span style="white-space: nowrap;">'.$data->p_name.'</span>';
//                })
//                ->editColumn('p_color_show', function($data){
//                    return '<span style="white-space: nowrap;">'.$data->p_color.' ('.$data->mc_name.')</span>';
//                })
//                ->editColumn('ps_name_show', function($data){
//                    return '<span style="white-space: nowrap;">'.$data->ps_name.'</span>';
//                })
//                ->editColumn('p_price_tag_show', function($data){
//                    return '<span class="float-right">'.number_format($data->p_price_tag,2,",",".").'</span>';
//                })
//                ->editColumn('p_purchase_price_show', function($data){
//                    return '<span class="float-right">'.number_format($data->p_purchase_price,2,",",".").'</span>';
//                })
//                ->editColumn('p_sell_price_show', function($data){
//                    return '<span class="float-right">'.number_format($data->p_sell_price,2,",",".").'</span>';
//                })
//                ->editColumn('p_detail', function($data){
//                    return '<a id="product_detail_btn" data-id="'.$data->pid.'" style="white-space: nowrap;" class="btn btn-sm btn-primary" style>Detail</a>';
//                })
//                ->rawColumns(['p_name_show', 'ps_name_show', 'p_color_show', 'p_price_tag_show', 'p_purchase_price_show', 'p_sell_price_show', 'p_detail', 'p_description'])
//                ->filter(function ($instance) use ($request) {
//                    if (!empty($request->get('br_id_filter'))) {
//                        $instance->where(function($w) use($request){
//                            $br_id = $request->get('br_id_filter');
//                            $w->orWhere('br_id', '=', $br_id);
//                        });
//                    }
//                    if (!empty($request->get('ps_id_filter'))) {
//                        $instance->where(function($w) use($request){
//                            $ps_id = $request->get('ps_id_filter');
//                            $w->orWhere('ps_id', '=', $ps_id);
//                        });
//                    }
//                    if (!empty($request->get('mc_id_filter'))) {
//                        $instance->where(function($w) use($request){
//                            $mc_id = $request->get('mc_id_filter');
//                            $w->orWhere('mc_id', '=', $mc_id);
//                        });
//                    }
//                    if (!empty($request->get('sz_id_filter'))) {
//                        $instance->join('product_stocks', 'product_stocks.p_id', '=', 'products.id')
//                        ->where(function($w) use($request){
//                            $sz_id = $request->get('sz_id_filter');
//                            $w->orWhere('sz_id', '=', $sz_id);
//                        });
//                    }
//                    if(!empty($request->get('p_active_filter'))) {
//                        if($request->get('p_active_filter') == '1') {
//                            $instance->where('p_active', '=', '1');
//                        }
//
//                        if ($request->get('p_active_filter') == '0') {
//                            $instance->where('p_active', '=', '0');
//                        }
//                    }
//                    if (!empty($request->get('search'))) {
//                        $instance->where(function($w) use($request){
//                            $search = $request->get('search');
//                            $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
//                            ->orWhere('p_name', 'LIKE', "%$search%")
//                            ->orWhere('mc_name', 'LIKE', "%$search%")
//                            ->orWhere('br_name', 'LIKE', "%$search%")
//                            ->orWhere('ps_name', 'LIKE', "%$search%");
//                        });
//                    }
//                })
//                ->addIndexColumn()
//                ->make(true);
//            } else {
//                return datatables()->of(Product::select('products.id as pid', 'br_id', 'pc_id', 'psc_id', 'pssc_id', 'mc_id', 'ps_id', 'pu_id', 'gn_id', 'ss_id', 'p_name', 'p_aging', 'p_color', 'mc_name', 'br_name', 'ps_name', 'p_price_tag', 'p_purchase_price', 'p_sell_price', 'p_weight', 'p_active')
//                ->join('brands', 'brands.id', '=', 'products.br_id')
//                ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
//                ->join('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
//                ->where('p_delete', '!=', '1')
//                ->where('pc_id', '=', $request->pc_id)
//                ->where('psc_id', '=', $request->psc_id)
//                ->where('pssc_id', '=', $request->pssc_id))
//                ->editColumn('p_name_show', function($data){
//                    return '<span style="white-space: nowrap;">'.$data->p_name.'</span>';
//                })
//                ->editColumn('p_color_show', function($data){
//                    return '<span style="white-space: nowrap;">'.$data->p_color.' ('.$data->mc_name.')</span>';
//                })
//                ->editColumn('ps_name_show', function($data){
//                    return '<span style="white-space: nowrap;">'.$data->ps_name.'</span>';
//                })
//                ->editColumn('p_price_tag_show', function($data){
//                    return '<span class="float-right">'.number_format($data->p_price_tag,2,",",".").'</span>';
//                })
//                ->editColumn('p_purchase_price_show', function($data){
//                    return '<span class="float-right">'.number_format($data->p_purchase_price,2,",",".").'</span>';
//                })
//                ->editColumn('p_sell_price_show', function($data){
//                    return '<span class="float-right">'.number_format($data->p_sell_price,2,",",".").'</span>';
//                })
//                ->editColumn('p_detail', function($data){
//                    return '<a id="product_detail_btn" data-id="'.$data->pid.'" style="white-space: nowrap;" class="btn btn-sm btn-primary" style>Detail</a>';
//                })
//                ->rawColumns(['p_name_show', 'ps_name_show', 'p_color_show', 'p_price_tag_show', 'p_purchase_price_show', 'p_sell_price_show', 'p_detail', 'p_description'])
//                ->filter(function ($instance) use ($request) {
//                    if (!empty($request->get('br_id_filter'))) {
//                        $instance->where(function($w) use($request){
//                            $br_id = $request->get('br_id_filter');
//                            $w->orWhere('br_id', '=', $br_id);
//                        });
//                    }
//                    if (!empty($request->get('ps_id_filter'))) {
//                        $instance->where(function($w) use($request){
//                            $ps_id = $request->get('ps_id_filter');
//                            $w->orWhere('ps_id', '=', $ps_id);
//                        });
//                    }
//                    if (!empty($request->get('mc_id_filter'))) {
//                        $instance->where(function($w) use($request){
//                            $mc_id = $request->get('mc_id_filter');
//                            $w->orWhere('mc_id', '=', $mc_id);
//                        });
//                    }
//                    if (!empty($request->get('sz_id_filter'))) {
//                        $instance->join('product_stocks', 'product_stocks.p_id', '=', 'products.id')
//                        ->where(function($w) use($request){
//                            $sz_id = $request->get('sz_id_filter');
//                            $w->orWhere('sz_id', '=', $sz_id);
//                        });
//                    }
//                    if(!empty($request->get('p_active_filter'))) {
//                        if($request->get('p_active_filter') == '1') {
//                            $instance->where('p_active', '=', '1');
//                        } elseif($request->get('p_active_filter') == '0') {
//                            $instance->where('p_active', '=', '0');
//                        } else {
//                            $instance->where('p_active', '!=', '1');
//                            $instance->where('p_active', '!=', '0');
//                        }
//                    }
//                    if (!empty($request->get('search'))) {
//                        $instance->where(function($w) use($request){
//                            $search = $request->get('search');
//                            $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
//                            ->orWhere('p_name', 'LIKE', "%$search%")
//                            ->orWhere('mc_name', 'LIKE', "%$search%")
//                            ->orWhere('br_name', 'LIKE', "%$search%")
//                            ->orWhere('ps_name', 'LIKE', "%$search%");
//                        });
//                    }
//                })
//                ->addIndexColumn()
//                ->make(true);
//            }
//        }
//    }

    public function getDatatables(Request $request)
    {
        try{
            if (request()->ajax()) {

                $query =  datatables()->of(Product::select(
                    'products.id as pid',
                    'article_id',
                    'br_id',
                    'pc_id',
                    'psc_id',
                    'pssc_id',
                    'mc_id', 'ps_id', 'pu_id', 'gn_id', 'ss_id', 'p_code', 'p_name', 'p_description', 'p_aging', 'p_color', 'mc_name', 'br_name', 'ps_name', 'p_price_tag', 'p_purchase_price', 'p_sell_price', 'p_weight', 'p_active')
                    ->join('brands', 'brands.id', '=', 'products.br_id')
                    ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
                    ->join('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
                    ->where('p_delete', '!=', '1'))
                    ->editColumn('p_name_show', function($data){
                        return '<span style="white-space: nowrap;">'.$data->p_name.'</span>';
                    })
                    ->editColumn('p_color_show', function($data){
                        return '<span style="white-space: nowrap;">'.$data->p_color.' ('.$data->mc_name.')</span>';
                    })
                    ->editColumn('ps_name_show', function($data){
                        return '<span style="white-space: nowrap;">'.$data->ps_name.'</span>';
                    })
                    ->editColumn('p_price_tag_show', function($data){
                        return '<span class="float-right">'.number_format($data->p_price_tag).'</span>';
                    })
                    ->editColumn('p_purchase_price_show', function($data){
                        return '<span class="float-right">'.number_format($data->p_purchase_price,).'</span>';
                    })
                    ->editColumn('p_sell_price_show', function($data){
                        return '<span class="float-right">'.number_format($data->p_sell_price).'</span>';
                    })
                    ->editColumn('p_detail', function($data){
                        return '<a id="product_detail_btn" data-id="'.$data->pid.'" style="white-space: nowrap;" class="btn btn-sm btn-primary" style>Detail</a>';
                    })
                    ->rawColumns(['p_name_show', 'ps_name_show', 'p_color_show', 'p_price_tag_show', 'p_purchase_price_show', 'p_sell_price_show', 'p_detail', 'p_description'])
                    ->filter(function ($instance) use ($request) {

                        if (!empty($request->get('pc_id'))) {
                            $instance->where(function($w) use($request){
                                $pc_id = $request->get('pc_id');
                                $w->orWhere('pc_id', '=', $pc_id);
                            });
                        }

                        if (!empty($request->get('psc_id'))) {
                            $instance->where(function($w) use($request){
                                $psc_id = $request->get('psc_id');
                                $w->orWhere('psc_id', '=', $psc_id);
                            });
                        }

                        if (!empty($request->get('pssc_id'))) {
                            $instance->where(function($w) use($request){
                                $pssc_id = $request->get('pssc_id');
                                $w->orWhere('pssc_id', '=', $pssc_id);
                            });
                        }

                        if (!empty($request->get('br_id_filter'))) {
                            $instance->where(function($w) use($request){
                                $br_id = $request->get('br_id_filter');
                                $w->orWhere('br_id', '=', $br_id);
                            });
                        }
                        if (!empty($request->get('ps_id_filter'))) {
                            $instance->where(function($w) use($request){
                                $ps_id = $request->get('ps_id_filter');
                                $w->orWhere('ps_id', '=', $ps_id);
                            });
                        }
                        if (!empty($request->get('mc_id_filter'))) {
                            $instance->where(function($w) use($request){
                                $mc_id = $request->get('mc_id_filter');
                                $w->orWhere('mc_id', '=', $mc_id);
                            });
                        }
                        if (!empty($request->get('sz_id_filter'))) {
                            $instance->join('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                                ->where(function($w) use($request){
                                    $sz_id = $request->get('sz_id_filter');
                                    $w->orWhere('sz_id', '=', $sz_id);
                                });
                        }
                        if(!empty($request->get('p_active_filter'))) {
                            if($request->get('p_active_filter') == '1') {
                                $instance->where('p_active', '=', '1');
                            }

                            if ($request->get('p_active_filter') == '0') {
                                $instance->where('p_active', '=', '0');
                            }
                        }
                        if (!empty($request->get('search'))) {
                            $instance->where(function($w) use($request){
                                $search = $request->get('search');
                                $w->orWhereRaw('CONCAT(p_name," ", p_color) LIKE ?', "%$search%")
                                    ->orWhere('p_name', 'LIKE', "%$search%")
                                    ->orWhere('mc_name', 'LIKE', "%$search%")
                                    ->orWhere('br_name', 'LIKE', "%$search%")
                                    ->orWhere('ps_name', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->addIndexColumn()
                    ->make(true);

                try {
                    return $query;
                } catch (\Exception $e) {
                    return $e;
                }
            }
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDatatablesItem(Request $request)
    {
        if(request()->ajax()) {
                $query = Product::select(
                    'products.id as pid', 'br_id', 'pc_id', 'psc_id', 'pssc_id', 'mc_id', 'ps_id', 'pu_id',
                    'gn_id', 'ss_id', 'p_name', 'p_description', 'p_aging', 'p_color', 'mc_name', 'br_name',
                    'ps_name', 'p_price_tag', 'p_purchase_price', 'p_sell_price', 'p_weight', 'article_id')
            ->join('brands', 'brands.id', '=', 'products.br_id')
            ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
            ->join('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
            ->where('p_delete', '!=', '1');

            if(!empty($request->ps_id)){
                $query->where('ps_id', '=', $request->ps_id);
            }

            return datatables()->of($query)
            ->editColumn('p_name_show', function($data){
                return '<span style="white-space: nowrap;">'.$data->p_name.'</span>';
            })
            ->editColumn('article_id', function($data){
                return '<span style="white-space: nowrap;">'.$data->article_id.'</span>';
            })
            ->editColumn('p_color_show', function($data){
                return '<span style="white-space: nowrap;">'.$data->p_color.' ('.$data->mc_name.')</span>';
            })
            ->editColumn('ps_name_show', function($data){
                return '<span style="white-space: nowrap;">'.$data->ps_name.'</span>';
            })
            ->editColumn('p_size', function($data){

            })
            ->editColumn('p_action', function($data){
                $product_stock = new ProductStock;
                $select = ['product_stocks.id as psid', 'p_id', 'sz_id', 'sz_name', 'ps_qty', 'ps_barcode', 'ps_running_code'];
                $where = [
                    'p_id' => $data->pid
                ];
                $check_data = $product_stock->getAllData($select, $where);
                if (!empty($check_data->first()->sz_id)) {
                    $check_list = '';
                    $i = 0;
                    foreach ($check_data as $row) {
                        $check_list .= '<span style="white-space: nowrap;"><input type="checkbox" data-index="'.$i.'" class="checkbox_add_item'.$data->pid.'_'.$i.'" id="checkbox_add_item" data-pid="'.$data->pid.'" data-psid="'.$row->psid.'"/> '.$row->sz_name.' (Sisa : '.$row->ps_qty.')</span><br/>';
                        $i++;
                    }
                    return $check_list;
                } else {
                    return 'Size belum disetting untuk produk ini';
                }
            })
            ->rawColumns(['p_name_show', 'article_id', 'p_color_show', 'p_action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('br_id_filter'))) {
                    $instance->where(function($w) use($request){
                        $br_id = $request->get('br_id_filter');
                        $w->orWhere('br_id', '=', $br_id);
                    });
                }
                if (!empty($request->get('mc_id_filter'))) {
                    $instance->where(function($w) use($request){
                        $mc_id = $request->get('mc_id_filter');
                        $w->orWhere('mc_id', '=', $mc_id);
                    });
                }
                if (!empty($request->get('sz_id_filter'))) {
                    $instance->join('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                    ->where(function($w) use($request){
                        $sz_id = $request->get('sz_id_filter');
                        $w->orWhere('sz_id', '=', $sz_id);
                    });
                }
                if(!empty($request->get('psc_id_filter'))) {
                    $instance->where(function($w) use($request){
                        $psc_id = $request->get('psc_id_filter');
                        $w->orWhere('psc_id', '=', $psc_id);
                    });
                }

                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('p_name', 'LIKE', "%$search%")
                        ->orWhere('mc_name', 'LIKE', "%$search%")
                        ->orWhere('br_name', 'LIKE', "%$search%")
                        ->orWhere('ps_name', 'LIKE', "%$search%")
                        ->orWhere('article_id', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getDatatablesLocation(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Product::select('products.id as pid', 'br_id', 'pc_id', 'psc_id', 'pssc_id', 'mc_id', 'ps_id', 'pu_id', 'gn_id', 'ss_id', 'p_name', 'p_description', 'p_aging', 'p_color', 'mc_name', 'br_name', 'ps_name', 'p_price_tag', 'p_purchase_price', 'p_sell_price', 'p_weight')
            ->join('brands', 'brands.id', '=', 'products.br_id')
            ->join('main_colors', 'main_colors.id', '=', 'products.mc_id')
            ->join('product_suppliers', 'product_suppliers.id', '=', 'products.ps_id')
            ->where('p_delete', '!=', '1'))
            ->editColumn('p_article', function($data){
                return '<span style="white-space: nowrap;">'.$data->p_name.' ['.$data->mc_name.' '.$data->p_color.'] ['.$data->br_name.']</span>';
            })
            ->editColumn('p_action', function($data){
                $product_stock = new ProductStock;
                $select = ['product_stocks.id as psid', 'p_id', 'sz_id', 'sz_name', 'ps_qty', 'ps_barcode', 'ps_running_code'];
                $where = [
                    'p_id' => $data->pid
                ];
                $check_data = $product_stock->getAllData($select, $where);
                if (!empty($check_data->first()->sz_id)) {
                    $check_list = '';
                    foreach ($check_data as $row) {
                        $setup_qty = 0;
                        $check_setup = ProductLocationSetup::select('pls_qty')->where('pst_id', $row->psid)->exists();
                        if ($check_setup) {
                            $get_setup = ProductLocationSetup::select('pls_qty')->where('pst_id', $row->psid)->get();
                            foreach ($get_setup as $gs_row) {
                                $setup_qty += $gs_row->pls_qty;
                            }
                        }
                        $unset_qty = $row->ps_qty - $setup_qty;
                        $check_list .= '<span style="white-space: nowrap;"> <a class="btn btn-sm btn-primary col-2" onclick="return addProduct('.$row->psid.', \''.$data->p_name.'\', \''.$row->sz_name.'\', '.$unset_qty.')">'.$row->sz_name.'</a> <a class="btn btn-sm btn-primary col-6" onclick="return addProduct('.$row->psid.', \''.$data->p_name.'\', \''.$row->sz_name.'\', '.$unset_qty.')">(Unset : '.$unset_qty.')</a></span><br/>';
                    }
                    return $check_list;
                } else {
                    return 'Size belum disetting untuk produk ini';
                }
            })
            ->rawColumns(['p_article', 'p_action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('br_id_filter'))) {
                    $instance->where(function($w) use($request){
                        $br_id = $request->get('br_id_filter');
                        $w->orWhere('br_id', '=', $br_id);
                    });
                }
                if (!empty($request->get('mc_id_filter'))) {
                    $instance->where(function($w) use($request){
                        $mc_id = $request->get('mc_id_filter');
                        $w->orWhere('mc_id', '=', $mc_id);
                    });
                }
                if (!empty($request->get('sz_id_filter'))) {
                    $instance->join('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                    ->where(function($w) use($request){
                        $sz_id = $request->get('sz_id_filter');
                        $w->orWhere('sz_id', '=', $sz_id);
                    });
                }
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('p_name', 'LIKE', "%$search%")
                        ->orWhere('mc_name', 'LIKE', "%$search%")
                        ->orWhere('br_name', 'LIKE', "%$search%")
                        ->orWhere('ps_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function generateRunningCode()
    {
        $check = ProductStock::select('ps_running_code')->orderByDesc('ps_running_code')->limit(1)->get()->first();
        if (!empty($check)) {
            $current_running_code = $check->ps_running_code;
            $next_running_code = $current_running_code + 1;
            $running_length = strlen($next_running_code);
            $new_running_code = '';
            if ($running_length == 1) {
                $new_running_code = '000000000000'.$next_running_code;
            } else if ($running_length == 2) {
                $new_running_code = '00000000000'.$next_running_code;
            } else if ($running_length == 3) {
                $new_running_code = '0000000000'.$next_running_code;
            } else if ($running_length == 4) {
                $new_running_code = '000000000'.$next_running_code;
            } else if ($running_length == 5) {
                $new_running_code = '00000000'.$next_running_code;
            } else if ($running_length == 6) {
                $new_running_code = '0000000'.$next_running_code;
            } else if ($running_length == 7) {
                $new_running_code = '000000'.$next_running_code;
            } else if ($running_length == 8) {
                $new_running_code = '00000'.$next_running_code;
            } else if ($running_length == 9) {
                $new_running_code = '0000'.$next_running_code;
            } else if ($running_length == 10) {
                $new_running_code = '000'.$next_running_code;
            } else if ($running_length == 11) {
                $new_running_code = '00'.$next_running_code;
            } else if ($running_length == 12) {
                $new_running_code = '0'.$next_running_code;
            } else if ($running_length == 13) {
                $new_running_code = $next_running_code;
            }
        } else {
            $new_running_code = '0000000000001';
        }

        if ($this->runningCodeExists($new_running_code)) {
            return generateRunningCode();
        }
        return $new_running_code;
    }

    public function runningCodeExists($number) {
        return ProductStock::where(['ps_running_code' => $number])->exists();
    }

    public function checkExistsBarcode(Request $request)
    {
        $check = ProductStock::where(['ps_barcode' => $request->_barcode])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsArticleID(Request $request)
    {
        $check = Product::where(['p_code' => $request->_article_id])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeData(Request $request)
    {
//        return json_encode($request->all());

        try {
            $product = new Product;
            $product_stock = new ProductStock;
            $mode = $request->input('_mode');
            $id = $request->input('_id');
            $sz_barcode = $request->input('_sz_barcode');
            $sz_sell_price = $request->input('_sz_sell_price');

            $data = [
                'br_id' => $request->input('br_id'),
                'pc_id' => $request->input('pc_id'),
                'psc_id' => $request->input('psc_id'),
                'pssc_id' => $request->input('pssc_id'),
                'mc_id' => $request->input('mc_id'),
                'ps_id' => $request->input('ps_id'),
                'pu_id' => $request->input('pu_id'),
                'gn_id' => $request->input('gn_id'),
                'ss_id' => $request->input('ss_id'),
                'p_color' => ltrim($request->input('p_color')),
                'p_code' => ltrim($request->input('p_code')),
                'p_name' => ltrim($request->input('p_name')),
                'p_description' => $request->input('p_description'),
                'p_aging' => $request->input('p_aging'),
                'p_price_tag' => $request->input('p_price_tag'),
                'p_purchase_price' => $request->input('p_purchase_price'),
                'p_sell_price' => $request->input('p_sell_price'),
                'p_weight' => $request->input('p_weight'),
                'article_id' => $request->input('article_id'),
                'p_delete' => '0'
            ];
            $save = $product->storeData($mode, $id, $data);

            if (!empty($save)) {
                if ($request->input('pc_id') !== $request->input('_current_pc_id')) {
                    DB::table('product_stocks')->where(['p_id' => $id])->delete();
                }

                $exp = explode('|', $request->_sz_id);
                $count = (Integer)count($exp);
                $barcodeArray = explode('|', rtrim($request->input('_sz_barcode'), '|'));
                for ($i=0; $i<=$count; $i++) {
                    if (empty($exp[$i])) {
                        continue;
                    }

                    $barcodeItem = $barcodeArray[$i];

                    // Extracting ID and Barcode from the current element
                    list($size_id, $barcode) = explode('-', $barcodeItem);

                    if ($mode == 'add') {
                        ProductStock::create([
                            'p_id' => $save,
                            'sz_id' => $size_id,
                            'ps_barcode' => $barcode,
                            'ps_qty' => '0',
                            'ps_running_code' => $this->generateRunningCode()
                        ]);
                    } else {
                        $check_current_size = ProductStock::where(['p_id' => $id, 'sz_id' => $exp[$i]])->exists();
                        if ($check_current_size) {
                            ProductStock::where(['p_id' => $id, 'sz_id' => $exp[$i]])->update(['ps_running_code' => $this->generateRunningCode()]);
                        } else {
                            ProductStock::create([
                                'p_id' => $id,
                                'sz_id' => $size_id,
                                'ps_qty' => '0',
                                'ps_running_code' => $this->generateRunningCode()
                            ]);
                        }
                    }
                }
                if ($mode == 'add') {
                    $this->UserActivity('menambah data produk '.strtoupper($request->input('p_name')).' '.strtoupper($request->input('p_color')));
                } else {
                    $this->UserActivity('mengubah data produk '.strtoupper($request->input('p_name')).' '.strtoupper($request->input('p_color')));
                }
                $r['status'] = '200';
            } else {
                $exp = explode('|', $request->_sz_id);
                $count = (Integer)count($exp);
                for ($i=0; $i<=$count; $i++) {
                    if (empty($exp[$i])) {
                        continue;
                    }
                    if ($mode == 'add') {
                        ProductStock::create([
                            'p_id' => $id,
                            'sz_id' => $exp[$i],
                            'ps_qty' => '0',
                            'ps_running_code' => $this->generateRunningCode()
                        ]);
                    } else {
                        $check_current_size = ProductStock::where(['p_id' => $id, 'sz_id' => $exp[$i]])->exists();
                        if ($check_current_size) {
                            ProductStock::where(['p_id' => $id, 'sz_id' => $exp[$i]])->update(['ps_running_code' => $this->generateRunningCode()]);
                        } else {
                            ProductStock::create([
                                'p_id' => $id,
                                'sz_id' => $exp[$i],
                                'ps_qty' => '0',
                                'ps_running_code' => $this->generateRunningCode()
                            ]);
                        }
                    }
                }

                $r['status'] = '200';
            }
            return json_encode($r);
        }catch (\Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    public function updateBarcode(Request $request)
    {
        $running = $request->_running;
        $barcode = $request->_barcode;
        $ps = ProductStock::where(['ps_running_code' => $running])->update(['ps_barcode' => $barcode]);
        if (!empty($ps)) {
            $this->UserActivity('mengubah barcode produk '.$barcode.' dengan running code '.$running);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $product = new Product;
        $product_stock = new ProductStock;
        $id = $request->input('_id');
        $save = DB::table('product_stocks')->where('p_id', $id)->delete();
        if ($save) {
            $item_name = Product::select('p_name', 'p_color')->where('id', $id)->get()->first();
            $save_product = $product->deleteData($id);
            if ($save_product) {
                $this->UserActivity('menghapus data produk '.$item_name->p_name.' '.$item_name->p_color);
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function productDetail(Request $request)
    {
        $product = new Product;
        $id = $request->_id;
        $select = ['br_name', 'pc_name', 'psc_name', 'pssc_name', 'mc_name', 'ps_name', 'pu_name', 'gn_name', 'ss_name', 'p_name', 'p_description', 'p_color', 'p_price_tag', 'p_purchase_price', 'p_sell_price'];
        $where = [
            'products.id' => $id
        ];
        $get_product = $product->getJoinData($select, $where);
        $data = [
            'product' => $get_product,
        ];
        return view('app.product.product_detail', compact('data'));
    }

    public function importData(Request $request)
    {
        if (request()->hasFile('p_template')) {
            $import = new ProductImport;
            Excel::import($import, request()->file('p_template'));
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

    public function exportData()
  	{
          set_time_limit(300);
          return Excel::download(new ProductArticleExport, 'product_data.xlsx');
//  		  return Excel::download(new ProductExport, 'product_data.xlsx');
  	}

      public function exportDataBarcode()
      {
          set_time_limit(300);
          return Excel::download(new ProductExport, 'product_data.xlsx');
      }
}
