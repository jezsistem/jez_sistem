<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use App\Models\MainColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Brand;
use App\Models\Size;
use App\Models\Store;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductLocationSetup;
use App\Models\ProductLocationSetupTransaction;
use App\Models\ProductLocationSetupTransactionStatus;
use App\Models\PurchaseOrderArticleDetailStatus;
use App\Models\ExceptionLocation;
use App\Models\BuyOneGetOne;

class StockDataController extends Controller
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
            'br_id' => Brand::where('br_delete', '!=', '1')->orderByDesc('id')->pluck('br_name', 'id'),
            'sz_id' => Size::selectRaw('ts_sizes.id as sz_id, CONCAT(sz_name," (",psc_name,")") as sz')
            ->join('product_sub_categories', 'product_sub_categories.id', '=', 'sizes.psc_id')
            ->where('sz_delete', '!=', '1')
            ->orderBy('sz_name')->pluck('sz', 'sz_id'),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'pc_id' => ProductCategory::where('pc_delete', '!=', '1')->orderByDesc('id')->pluck('pc_name', 'id'),
            'psc_id' => ProductSubCategory::where('psc_delete', '!=', '1')->orderByDesc('id')->pluck('psc_name', 'id'),
            'pssc_id' => ProductSubSubCategory::where('pssc_delete', '!=', '1')->orderByDesc('id')->pluck('pssc_name', 'id'),
            'gender_id' => Gender::where('gn_delete', '!=', '1')->orderByDesc('id')->pluck('gn_name', 'id'),
            'main_color_id' => MainColor::where('mc_delete', '!=', '1')->orderByDesc('id')->pluck('mc_name', 'id'),
            'segment' => request()->segment(1),
        ];

        return view('app.stock_data.stock_data', compact('data'));
    }
    
    public function getDatatables(Request $request)
    {
//        return $request->all();
        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

        $b1g1_setup = BuyOneGetOne::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'buy_one_get_ones.pl_id')->get()->toArray();
        if (!empty($request->st_id)) {
            $st_id = $request->st_id;
        } else {
            $st_id = Auth::user()->st_id;
        }
        
        if(request()->ajax()) {
            return datatables()->of(Product::selectRaw("ts_products.id as pid, CONCAT(p_name,' (',br_name,')') as p_name_brand, p_name, article_id, br_name, ps_qty, p_price_tag, p_sell_price, ps_price_tag, ps_sell_price")
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
//            ->where('pls_qty', '>', 0)
            ->whereNotIn('pl_code', $exception)
            ->where('product_locations.st_id', '=', $st_id)
            ->groupBy('p_name_brand')
            ->orderByDesc('products.updated_at'))
            ->editColumn('article_name', function($data){
                $price_tag = 0;
                $sell_price = 0;
                if (!empty($data->p_price_tag)) {
                    $price_tag = $data->p_price_tag;
                } else {
                    $price_tag = $data->ps_price_tag;
                }
                if (!empty($data->p_sell_price)) {
                    $sell_price = $data->p_sell_price;
                } else {
                    $sell_price = $data->ps_sell_price;
                }
                $hb = '<span style="white-space: nowrap; font-weight:bold; background: rgb(212,18,21);
                background: linear-gradient(171deg, rgba(212,18,21,1) 50%, rgba(209,122,0,1) 100%);" class="badge badge-sm badge-primary">B: '.number_format($price_tag).' | J: '.number_format($sell_price).'</span>';
                return '<span style="white-space: nowrap; font-weight:bold;" class="btn btn-sm btn-primary">['.$data->br_name.'] '.$data->p_name.' '.$hb.'</span>';
            })
            ->editColumn('article_age', function($data){
                
            })
            ->editColumn('article_stock', function($data) use ($request, $exception, $b1g1_setup, $st_id) {
                $sz_id = $request->get('sz_id');
                $gender_id = $request->get('gender_id');
                $item = Product::selectRaw("ts_products.id as pid, pst_id, CONCAT(p_name,' (',br_name,')') as p_name_brand, p_name, p_color, br_name, ps_qty, pls_qty, p_price_tag, ps_price_tag, p_sell_price, ps_sell_price, article_id")
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
//                ->where('pls_qty', '>', 0)
                ->where('product_locations.st_id', '=', $st_id)
                ->whereNotIn('pl_code', $exception)
                ->where('p_name', $data->p_name)
                ->where('br_name', $data->br_name)
                ->where(function($w) use ($sz_id) {
                    if (!empty($sz_id)) {
                        if (count($sz_id) > 0) {
                            $w->whereIn('sz_id', $sz_id);
                        } else {
                            $w->where('sz_id', $sz_id);
                        }
                    }
                })
                ->groupBy('p_color')->get();
                if (!empty($item)) {
                    $item_list = '';
                    $item_list .= '<table>';
                    foreach ($item as $row) {
                        $check_poad = PurchaseOrderArticleDetailStatus::select(DB::raw('max(ts_purchase_order_article_detail_statuses.created_at) as poads_created'), 'po_invoice')
                        ->leftJoin('purchase_order_article_details', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                        ->leftJoin('purchase_order_articles', 'purchase_order_article_details.poa_id', '=', 'purchase_order_articles.id')
                        ->leftJoin('purchase_orders', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
                        ->where('pst_id', '=', $row->pst_id)
                        ->where('purchase_orders.st_id', '=', $st_id)
                        ->orderByDesc('purchase_order_article_detail_statuses.id')
                        ->get()->first();
                        
                        $stf = DB::table('stock_transfer_detail_statuses')->select('stf_code', DB::raw('max(ts_stock_transfer_detail_statuses.created_at) as created_at'))
                        ->leftJoin('stock_transfer_details', 'stock_transfer_details.id', '=', 'stock_transfer_detail_statuses.stfd_id')
                        ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
                        ->where('stock_transfers.st_id_end', '=', $st_id)
                        ->where('product_stocks.id', '=', $row->pst_id)
                        ->orderByDesc('stock_transfer_detail_statuses.id')
                        ->whereNotNull('stock_transfer_detail_statuses.created_at')
                        ->get()->first();
                        $title_po = '-';
                        $title_tf = '-';
                        $days_remain_po = 99999;
                        $days_remain_tf = 99999;

                        if (!empty($check_poad)) {
                            $title_po = '['.$row->br_name.'] '.$row->p_name.' '.$row->p_color.' terakhir diterima tanggal '.date('d/m/Y H:i:s', strtotime($check_poad->poads_created)).' pada PO '.$check_poad->po_invoice;
                            $date1_remain_po = $check_poad->poads_created;
                            $date2_remain_po = date('Y-m-d H:i:s');
                            $diff_remain_po = abs(strtotime($date1_remain_po) - strtotime($date2_remain_po));
                            if ($date1_remain_po>$date2_remain_po) {
                                $diff_remain_po = -($diff_remain_po);
                            }
                            $days_remain_po = round($diff_remain_po/86400);
                        }
                        if (!empty ($stf)) {
                            $created_at = $stf->created_at;
                            $code = 'kode transfer '.$stf->stf_code;
                            $title_tf = '['.$row->br_name.'] '.$row->p_name.' '.$row->p_color.' terakhir diterima tanggal '.date('d/m/Y H:i:s', strtotime($created_at)).' pada TF '.$code;
                            $date1_remain_tf = $created_at;
                            $date2_remain_tf = date('Y-m-d H:i:s');
                            $diff_remain_tf = abs(strtotime($date1_remain_tf) - strtotime($date2_remain_tf));
                            if ($date1_remain_tf>$date2_remain_tf) {
                                $diff_remain_tf = -($diff_remain_tf);
                            }
                            $days_remain_tf = round($diff_remain_tf/86400);
                        }

                        $item = '';
                        if ($days_remain_po <= $days_remain_tf) {
                            if ($days_remain_po > 1000) {
                                $days_remain_po = '-';
                            }
                            $item = '<span class="btn-sm-custom btn-primary" id="aging_detail" title="'.$title_po.' | '.$row->pst_id.'">'.$days_remain_po.' H</span>';
                        } else {
                            if ($days_remain_tf > 1000) {
                                $days_remain_tf = '-';
                            }
                            $item = '<span class="btn-sm-custom btn-primary" id="aging_detail" title="'.$title_tf.'">'.$days_remain_tf.' H</span>';
                        }

                        $item_list .= '<tr style="border:0px;">';
                        $item_list .= '<td style="white-space: nowrap; border:0px; font-weight:bold;">'.$item.' <span class="btn-sm-custom btn-primary">'.$row->article_id .' '.$row->p_color.'</span></td>';
                        $item_size_stock = ProductLocationSetup::select('product_location_setups.id as pls_id', 'pl_code', 'pls_qty', 'pl_id', 'product_stocks.id as pst_id', 'sz_name', 'ps_qty')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->whereNotIn('pl_code', $exception)
//                        ->where('pls_qty', '>', 0)
                        ->where('product_locations.st_id', '=', $st_id)
                        ->where('p_id', $row->pid)
                        ->where(function($w) use ($sz_id) {
                            if (!empty($sz_id)) {
                                if (count($sz_id) > 0) {
                                    $w->whereIn('sz_id', $sz_id);
                                } else {
                                    $w->where('sz_id', $sz_id);
                                }
                            }
                        })
                        ->orderBy('sizes.id', 'asc')
                        ->groupBy('sz_name')->get();
                        if (!empty($item_size_stock)) {
                            $item_list .= '<td style="white-space: nowrap; font-weight:bold; border:0px;">';
                            foreach ($item_size_stock as $srow) {
                                if (count($item_size_stock) > 1) {
                                    $br = '<br/><br/>';
                                } else {
                                    $br = '<br/>';
                                }
                                $item_location = ProductLocationSetup::select('product_location_setups.id as pls_id', 'pl_code', 'pl_name', 'pls_qty', 'pl_id')
                                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                                ->whereNotIn('pl_code', $exception)
//                                ->where('pls_qty', '>=', '0')
                                ->where('product_locations.st_id', '=', $st_id)
                                ->where('pst_id', $srow->pst_id)->get();
                                $bin = '';
                                if (!empty($item_location)) {
                                    foreach ($item_location as $lrow) {
                                        if ($lrow->pl_code == 'TOKO') {
                                            $bin .= '<span class="btn-sm-custom btn-info" title="['.$lrow->pl_code.'] '.$lrow->pl_name.'">'.$lrow->pls_qty.'</span> ';
                                        } else if (in_array(['pl_code' => $lrow->pl_code], $b1g1_setup)) {
                                            $bin .= '<span class="btn-sm-custom btn-warning" title="['.$lrow->pl_code.'] '.$lrow->pl_name.'">['.$lrow->pl_code.'] ('.$lrow->pls_qty.')</span> ';
                                        } else {
                                            $bin .= '<span title="['.$lrow->pl_code.'] '.$lrow->pl_name.'" class="btn-sm-custom btn-success" data-p_name="'.$row->p_name.' '.$row->p_color.' '.$srow->sz_name.'" data-pl_code="'.$lrow->pl_code.'" data-bin="'.$lrow->pl_code.' '.$lrow->pl_name.'" data-qty="'.$lrow->pls_qty.'" data-pst_id="'.$srow->pst_id.'" data-pl_id="'.$lrow->pl_id.'" data-pls_id="'.$lrow->pls_id.'" id="pickup_item">'.$lrow->pls_qty.'</span> ';
                                        }
                                    }
                                }
                                $item_list .= '<span class="btn-sm-custom btn-primary">'.$srow->sz_name.'</span> '.$bin.' ';
                            }
                            $item_list .= '</td>';
                        }
                        $item_list .= '</tr>';
                    }
                    $item_list .= '</table>';
                    return $item_list;
                } else {
                    return '-';
                }
            })
            ->rawColumns(['article_name', 'article_age', 'article_stock'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('br_id'))) {
                    $instance->where(function($w) use($request){
                        $br_id = $request->get('br_id');
                        $count = (Integer)count($br_id);
                        $where = array();
                        if ($count > 0) {
                            for ($i = 0; $i < $count; $i++) {
                                $where[] = $br_id[$i];
                            }
                            $w->orWhereIn('br_id', $where);
                        } else {
                            $w->orWhere('br_id', '=', $br_id[0]);
                        }
                    });
                }
                if (!empty($request->get('pc_id'))) {
                    $instance->where(function($w) use($request){
                        $pc_id = $request->get('pc_id');
                        $count = (Integer)count($pc_id);
                        $where = array();
                        if ($count > 0) {
                            for ($i = 0; $i < $count; $i++) {
                                $where[] = $pc_id[$i];
                            }
                            $w->orWhereIn('products.pc_id', $where);
                        } else {
                            $w->orWhere('products.pc_id', '=', $pc_id[0]);
                        }
                    });
                }
                if (!empty($request->get('psc_id'))) {
                    $instance->where(function($w) use($request){
                        $psc_id = $request->get('psc_id');
                        $count = (Integer)count($psc_id);
                        $where = array();
                        if ($count > 0) {
                            for ($i = 0; $i < $count; $i++) {
                                $where[] = $psc_id[$i];
                            }
                            $w->orWhereIn('products.psc_id', $where);
                        } else {
                            $w->orWhere('products.psc_id', '=', $psc_id[0]);
                        }
                    });
                }
                if (!empty($request->get('pssc_id'))) {
                    $instance->where(function($w) use($request){
                        $pssc_id = $request->get('pssc_id');
                        $count = (Integer)count($pssc_id);
                        $where = array();
                        if ($count > 0) {
                            for ($i = 0; $i < $count; $i++) {
                                $where[] = $pssc_id[$i];
                            }
                            $w->orWhereIn('products.pssc_id', $where);
                        } else {
                            $w->orWhere('products.pssc_id', '=', $pssc_id[0]);
                        }
                    });
                }
                if (!empty($request->get('sz_id'))) {
                    $instance->where(function($w) use($request){
                        $sz_id = $request->get('sz_id');
                        $count = (Integer)count($sz_id);
                        $where = array();
                        if ($count > 0) {
                            for ($i = 0; $i < $count; $i++) {
                                $where[] = $sz_id[$i];
                            }
                            $w->orWhereIn('sz_id', $where);
                        } else {
                            $w->orWhere('sz_id', '=', $sz_id[0]);
                        }
                    });
                }

                if (!empty($request->get('gender_id'))) {
                    $instance->where(function($w) use($request){
                        $gender_id = $request->get('gender_id');
                        $count = (Integer)count($gender_id);
                        $where = array();
                        if ($count > 0) {
                            for ($i = 0; $i < $count; $i++) {
                                $where[] = $gender_id[$i];
                            }
                            $w->orWhereIn('products.gn_id', $where);
                        } else {
                            $w->orWhere('products.gn_id', '=', $gender_id[0]);
                        }
                    });
                }
                if (!empty($request->get('main_color_id'))) {
                    $instance->where(function($w) use($request){
                        $mc_id = $request->get('main_color_id');
                        $count = (Integer)count($mc_id);
                        $where = array();
                        if ($count > 0) {
                            for ($i = 0; $i < $count; $i++) {
                                $where[] = $mc_id[$i];
                            }
                            $w->orWhereIn('products.mc_id', $where);
                        } else {
                            $w->orWhere('products.mc_id', '=', $mc_id[0]);
                        }
                    });
                }
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(br_name," ",p_name," ",p_color," ",sz_name) LIKE ?', "%$search%")
                        ->orWhereRaw('ts_product_stocks.ps_barcode LIKE ?', "%$search%")
                        ->orWhereRaw('ts_products.article_id LIKE ?', "%$search%");
                    });
                }
                if (!empty($request->get('search_scan'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search_scan');
                        $w->orWhereRaw('CONCAT(br_name," ",p_name," ",p_color," ",sz_name) LIKE ?', "%$search%")
                            ->orWhereRaw('ts_products.article_id LIKE ?', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
        
    }

    public function getHelperDatatables(Request $request)
    {
        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();
        
        $st_id = Auth::user()->st_id;
        $st_id_filter = $request->get('st_id');
        if(request()->ajax()) {
            return datatables()->of(Product::selectRaw("ts_products.id as pid, CONCAT(p_name,' (',br_name,')') as p_name_brand, p_name, br_name, ps_qty")
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->where('pls_qty', '>', 0)
            ->where(function($w) use ($st_id, $st_id_filter){
              if (empty($st_id_filter)) {
                $w->where('product_locations.st_id', '=', $st_id);
              } else {
                $w->where('product_locations.st_id', '=', $st_id_filter);
              }
            })
            ->whereNotIn('pl_code', $exception)
            ->groupBy('p_name_brand'))
            ->editColumn('article_name', function($data){
                return '<span style="white-space: nowrap;" class="btn btn-sm btn-primary">'.$data->p_name.' ['.$data->br_name.']</span> ';
            })
            ->editColumn('article_stock', function($data) use ($request, $exception, $st_id, $st_id_filter) {
                $sz_id = $request->get('sz_id');
                if (!empty($sz_id)) {
                    $count_sz_id = (Integer)count($sz_id);
                    $where_sz_id = array();
                    if ($count_sz_id > 0) {
                        for ($i = 0; $i < $count_sz_id; $i++) {
                            $where_sz_id[] = $sz_id[$i];
                        }
                        $item = Product::selectRaw("ts_products.id as pid, CONCAT(p_name,' (',br_name,')') as p_name_brand, p_name, p_color, br_name, ps_qty, pls_qty")
                        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                        ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                        ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('pls_qty', '>', 0)
                        ->where(function($w) use ($st_id, $st_id_filter){
                          if (empty($st_id_filter)) {
                            $w->where('product_locations.st_id', '=', $st_id);
                          } else {
                            $w->where('product_locations.st_id', '=', $st_id_filter);
                          }
                        })
                        ->whereIn('sz_id', $sz_id)
                        ->whereNotIn('pl_code', $exception)
                        ->where('p_name', $data->p_name)
                        ->where('br_name', $data->br_name)
                        ->groupBy('p_color')->get();
                    } else {
                        $item = Product::selectRaw("ts_products.id as pid, CONCAT(p_name,' (',br_name,')') as p_name_brand, p_name, p_color, br_name, ps_qty, pls_qty")
                        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                        ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                        ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('pls_qty', '>', 0)
                        ->where(function($w) use ($st_id, $st_id_filter){
                          if (empty($st_id_filter)) {
                            $w->where('product_locations.st_id', '=', $st_id);
                          } else {
                            $w->where('product_locations.st_id', '=', $st_id_filter);
                          }
                        })
                        ->where('sz_id', $sz_id)
                        ->whereNotIn('pl_code', $exception)
                        ->where('p_name', $data->p_name)
                        ->where('br_name', $data->br_name)
                        ->groupBy('p_color')->get();
                    }
                } else {
                    $item = Product::selectRaw("ts_products.id as pid, CONCAT(p_name,' (',br_name,')') as p_name_brand, p_name, p_color, br_name, ps_qty, pls_qty")
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                    ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                    ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->where('pls_qty', '>', 0)
                    ->where(function($w) use ($st_id, $st_id_filter){
                      if (empty($st_id_filter)) {
                        $w->where('product_locations.st_id', '=', $st_id);
                      } else {
                        $w->where('product_locations.st_id', '=', $st_id_filter);
                      }
                    })
                    ->whereNotIn('pl_code', $exception)
                    ->where('p_name', $data->p_name)
                    ->where('br_name', $data->br_name)
                    ->groupBy('p_color')->get();
                }
                if (!empty($item)) {
                    $item_list = '';
                    $item_list .= '<table>';
                    foreach ($item as $row) {
                        $item_list .= '<tr>';
                        $item_list .= '<td style="white-space: nowrap;"><span class="btn-sm-custom btn-primary">'.$row->p_color.'</span></td>';
                        if (!empty($sz_id)) {
                            $count = (Integer)count($sz_id);
                            $where = array();
                            if ($count > 0) {
                                for ($i = 0; $i < $count; $i++) {
                                    $where[] = $sz_id[$i];
                                }
                                $item_size_stock = ProductLocationSetup::select('product_location_setups.id as pls_id', 'pl_code', 'pls_qty', 'pl_id', 'product_stocks.id as pst_id', 'sz_name', 'ps_qty')
                                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                                ->where('p_id', $row->pid)
                                ->where('pls_qty', '>', 0)
                                ->where(function($w) use ($st_id, $st_id_filter){
                                  if (empty($st_id_filter)) {
                                    $w->where('product_locations.st_id', '=', $st_id);
                                  } else {
                                    $w->where('product_locations.st_id', '=', $st_id_filter);
                                  }
                                })
                                ->whereNotIn('pl_code', $exception)
                                ->whereIn('sz_id', $sz_id)
                                ->groupBy('sz_name')->get();
                            } else {
                                $item_size_stock = ProductLocationSetup::select('product_location_setups.id as pls_id', 'pl_code', 'pls_qty', 'pl_id', 'product_stocks.id as pst_id', 'sz_name', 'ps_qty')
                                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                                ->where('p_id', $row->pid)
                                ->where('pls_qty', '>', 0)
                                ->where(function($w) use ($st_id, $st_id_filter){
                                  if (empty($st_id_filter)) {
                                    $w->where('product_locations.st_id', '=', $st_id);
                                  } else {
                                    $w->where('product_locations.st_id', '=', $st_id_filter);
                                  }
                                })
                                ->whereNotIn('pl_code', $exception)
                                ->where('sz_id', $sz_id)
                                ->groupBy('sz_name')->get();
                            }
                        } else {
                            $item_size_stock = ProductLocationSetup::select('product_location_setups.id as pls_id', 'pl_code', 'pls_qty', 'pl_id', 'product_stocks.id as pst_id', 'sz_name', 'ps_qty')
                            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                            ->whereNotIn('pl_code', $exception)
                            ->where('pls_qty', '>', 0)
                            ->where(function($w) use ($st_id, $st_id_filter){
                              if (empty($st_id_filter)) {
                                $w->where('product_locations.st_id', '=', $st_id);
                              } else {
                                $w->where('product_locations.st_id', '=', $st_id_filter);
                              }
                            })
                            ->where('p_id', $row->pid)
                            ->groupBy('sz_name')->get();
                        }
                        if (!empty($item_size_stock)) {
                            $item_list .= '<td style="white-space: nowrap;">';
                            foreach ($item_size_stock as $srow) {
                                if (count($item_size_stock) > 1) {
                                    $br = '<br/><br/>';
                                } else {
                                    $br = '<br/>';
                                }
                                $item_location = ProductLocationSetup::select('product_location_setups.id as pls_id', 'pl_code', 'pl_name', 'pls_qty', 'pl_id')
                                ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                                ->whereNotIn('pl_code', $exception)
                                ->where('pls_qty', '>', '0')
                                ->where(function($w) use ($st_id, $st_id_filter){
                                  if (empty($st_id_filter)) {
                                    $w->where('product_locations.st_id', '=', $st_id);
                                  } else {
                                    $w->where('product_locations.st_id', '=', $st_id_filter);
                                  }
                                })
                                ->where('pst_id', $srow->pst_id)->get();
                                $bin = '';
                                if (!empty($item_location)) {
                                    foreach ($item_location as $lrow) {
                                        if ($lrow->pl_code == 'TRIAL' || $lrow->pl_code == 'TO01' || $lrow->pl_code == 'PJ01' || $lrow->pl_code == 'RJK') {
                                            $bin .= '<span class="btn-sm-custom btn-danger">'.$lrow->pls_qty.'</span> ';
                                        } else if ($lrow->pl_code == 'TOKO') {
                                            $bin .= '<span class="btn-sm-custom btn-info" title="['.$lrow->pl_code.'] '.$lrow->pl_name.'">['.$lrow->pl_code.'] ['.$lrow->pls_qty.']</span> ';
                                        } else {
                                            $bin .= '<span title="['.$lrow->pl_code.'] '.$lrow->pl_name.'" class="btn-sm-custom btn-inventory" data-p_name="'.$row->p_name.' '.$row->p_color.' '.$srow->sz_name.'" data-pl_code="'.$lrow->pl_code.'" data-bin="'.$lrow->pl_code.' '.$lrow->pl_name.'" data-qty="'.$lrow->pls_qty.'" data-pst_id="'.$srow->pst_id.'" data-pl_id="'.$lrow->pl_id.'" data-pls_id="'.$lrow->pls_id.'" id="pickup_item">['.$lrow->pl_code.'] ['.$lrow->pls_qty.']</span> ';
                                        }
                                    }
                                }
                                $item_list .= '<span class="btn-sm-custom btn-primary">'.$srow->sz_name.'</span> '.$bin.' ';
                            }
                            $item_list .= '</td>';
                        }
                        $item_list .= '</tr>';
                    }
                    $item_list .= '</table>';
                    return $item_list;
                } else {
                    return '-';
                }
            })
            ->rawColumns(['article_name', 'article_age', 'article_stock'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(p_name," ", p_color," ", sz_name) LIKE ?', "%$search%")
                        ->orWhere('p_name', 'LIKE', "%$search%")
                        ->orWhere('p_color', 'LIKE', "%$search%")
                        ->orWhere('br_name', 'LIKE', "%$search%")
                        ->orWhere('sz_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function reloadCategory(Request $request)
    {
        $type = $request->_type;
        $id = $request->_id;
        if (!empty($id)) {
            if ($type == 'brand') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $pc_id = ProductCategory::select('product_categories.id as pc_id', 'pc_name')->leftJoin('products', 'products.pc_id', '=', 'product_categories.id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->where('pc_delete', '!=', '1')
                    ->whereIn('br_id', $where)->orderBy('pc_name')->pluck('pc_name', 'pc_id');
                } else {
                    $pc_id = ProductCategory::select('product_categories.id as pc_id', 'pc_name')->leftJoin('products', 'products.pc_id', '=', 'product_categories.id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->where('pc_delete', '!=', '1')
                    ->where('br_id', '=', $id[0])->orderBy('pc_name')->pluck('pc_name', 'pc_id');
                }
            } else if ($type == 'size') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $pc_id = ProductCategory::select('product_categories.id as pc_id', 'pc_name')->leftJoin('products', 'products.pc_id', '=', 'product_categories.id')
                    ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                    ->where('pc_delete', '!=', '1')
                    ->whereIn('sz_id', $where)->orderBy('pc_name')->pluck('pc_name', 'pc_id');
                } else {
                    $pc_id = ProductCategory::select('product_categories.id as pc_id', 'pc_name')->leftJoin('products', 'products.pc_id', '=', 'product_categories.id')
                    ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                    ->where('pc_delete', '!=', '1')
                    ->where('sz_id', '=', $id[0])->orderBy('pc_name')->pluck('pc_name', 'pc_id');
                }
            } else {
                $pc_id = ProductCategory::where('pc_delete', '!=', '1')->orderBy('pc_name')->pluck('pc_name', 'id');
            }
        } else {
            $pc_id = ProductCategory::where('pc_delete', '!=', '1')->orderBy('pc_name')->pluck('pc_name', 'id');
        }
        $data = [
            'pc_id' => $pc_id
        ];
        return view('app.stock_data._reload_category', compact('data'));
    }

    public function reloadSubCategory(Request $request)
    {
        $type = $request->_type;
        $id = $request->_id;
        if (!empty($id)) {
            if ($type == 'product_category') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $psc_id = ProductSubCategory::where('psc_delete', '!=', '1')->whereIn('pc_id', $where)->orderBy('psc_name')->pluck('psc_name', 'id');
                } else {
                    $psc_id = ProductSubCategory::where('psc_delete', '!=', '1')->where('pc_id', '=', $id[0])->orderBy('psc_name')->pluck('psc_name', 'id');
                }
            } else if ($type == 'brand') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $psc_id = ProductSubCategory::select('product_sub_categories.id as psc_id', 'psc_name')->
                    leftJoin('products', 'products.psc_id', '=', 'product_sub_categories.id')
                    ->where('psc_delete', '!=', '1')
                    ->whereIn('br_id', $where)->orderBy('psc_name')->pluck('psc_name', 'psc_id');
                } else {
                    $psc_id = ProductSubCategory::select('product_sub_categories.id as psc_id', 'psc_name')->
                    leftJoin('products', 'products.psc_id', '=', 'product_sub_categories.id')
                    ->where('psc_delete', '!=', '1')
                    ->where('br_id', '=', $id[0])->orderBy('psc_name')->pluck('psc_name', 'psc_id');
                }
            } else if ($type == 'size') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $psc_id = ProductSubCategory::select('product_sub_categories.id as psc_id', 'psc_name')->
                    leftJoin('products', 'products.psc_id', '=', 'product_sub_categories.id')
                    ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                    ->where('psc_delete', '!=', '1')
                    ->whereIn('sz_id', $where)->orderBy('psc_name')->pluck('psc_name', 'psc_id');
                } else {
                    $psc_id = ProductSubCategory::select('product_sub_categories.id as psc_id', 'psc_name')->
                    leftJoin('products', 'products.psc_id', '=', 'product_sub_categories.id')
                    ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                    ->where('psc_delete', '!=', '1')
                    ->where('sz_id', '=', $id[0])->orderBy('psc_name')->pluck('psc_name', 'psc_id');
                }
            } else {
                $psc_id = ProductSubCategory::where('psc_delete', '!=', '1')->orderBy('psc_name')->pluck('psc_name', 'id');
            }
        } else {
            $psc_id = ProductSubCategory::where('psc_delete', '!=', '1')->orderBy('psc_name')->pluck('psc_name', 'id');
        }
        $data = [
            'psc_id' => $psc_id
        ];
        return view('app.stock_data._reload_sub_category', compact('data'));
    }

    public function reloadSubSubCategory(Request $request)
    {
        $type = $request->_type;
        $id = $request->_id;
        if (!empty($id)) {
            if ($type == 'product_sub_category') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $pssc_id = ProductSubSubCategory::where('pssc_delete', '!=', '1')->whereIn('psc_id', $where)->orderBy('pssc_name')->pluck('pssc_name', 'id');
                } else {
                    $pssc_id = ProductSubSubCategory::where('pssc_delete', '!=', '1')->where('psc_id', '=', $id[0])->orderBy('pssc_name')->pluck('pssc_name', 'id');
                }
            } else {
                $pssc_id = ProductSubSubCategory::where('pssc_delete', '!=', '1')->orderBy('pssc_name')->pluck('pssc_name', 'id');
            }
        } else {
            $pssc_id = ProductSubSubCategory::where('pssc_delete', '!=', '1')->orderBy('pssc_name')->pluck('pssc_name', 'id');
        }
        $data = [
            'pssc_id' => $pssc_id
        ];
        return view('app.stock_data._reload_sub_sub_category', compact('data'));
    }

    public function reloadBrand(Request $request)
    {
        $type = $request->_type;
        $id = $request->_id;
        if (!empty($id)) {
            if ($type == 'product_category') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $br_id = Brand::select('brands.id as br_id', 'br_name')->
                    leftJoin('products', 'products.br_id', '=', 'brands.id')
                    ->where('br_delete', '!=', '1')
                    ->whereIn('pc_id', $where)->orderBy('br_name')->pluck('br_name', 'br_id');
                } else {
                    $br_id = Brand::select('brands.id as br_id', 'br_name')->
                    leftJoin('products', 'products.br_id', '=', 'brands.id')
                    ->where('br_delete', '!=', '1')
                    ->where('pc_id', '=', $id[0])->orderBy('br_name')->pluck('br_name', 'br_id');
                }
            } else if ($type == 'product_sub_category') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $br_id = Brand::select('brands.id as br_id', 'br_name')->
                    leftJoin('products', 'products.br_id', '=', 'brands.id')
                    ->where('br_delete', '!=', '1')
                    ->whereIn('psc_id', $where)->orderBy('br_name')->pluck('br_name', 'br_id');
                } else {
                    $br_id = Brand::select('brands.id as br_id', 'br_name')->
                    leftJoin('products', 'products.br_id', '=', 'brands.id')
                    ->where('br_delete', '!=', '1')
                    ->where('psc_id', '=', $id[0])->orderBy('br_name')->pluck('br_name', 'br_id');
                }
            } else if ($type == 'product_sub_sub_category') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $br_id = Brand::select('brands.id as br_id', 'br_name')->
                    leftJoin('products', 'products.br_id', '=', 'brands.id')
                    ->where('br_delete', '!=', '1')
                    ->whereIn('pssc_id', $where)->orderBy('br_name')->pluck('br_name', 'br_id');
                } else {
                    $br_id = Brand::select('brands.id as br_id', 'br_name')->
                    leftJoin('products', 'products.br_id', '=', 'brands.id')
                    ->where('br_delete', '!=', '1')
                    ->where('pssc_id', '=', $id[0])->orderBy('br_name')->pluck('br_name', 'br_id');
                }
            } else if ($type == 'size') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $br_id = Brand::select('brands.id as br_id', 'br_name')->
                    leftJoin('products', 'products.br_id', '=', 'brands.id')
                    ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                    ->where('br_delete', '!=', '1')
                    ->whereIn('sz_id', $where)->orderBy('br_name')->pluck('br_name', 'br_id');
                } else {
                    $br_id = Brand::select('brands.id as br_id', 'br_name')->
                    leftJoin('products', 'products.br_id', '=', 'brands.id')
                    ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                    ->where('br_delete', '!=', '1')
                    ->where('sz_id', '=', $id[0])->orderBy('br_name')->pluck('br_name', 'br_id');
                }
            } else {
                $br_id = Brand::where('br_delete', '!=', '1')->orderBy('br_name')->pluck('br_name', 'id');
            }
        } else {
            $br_id = Brand::where('br_delete', '!=', '1')->orderBy('br_name')->pluck('br_name', 'id');
        }
        $data = [
            'br_id' => $br_id
        ];
        return view('app.stock_data._reload_brand', compact('data'));
    }

    public function reloadSize(Request $request)
    {
        $type = $request->_type;
        $id = $request->_id;
        if (!empty($id)) {
            if ($type == 'product_category') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $sz_id = Size::selectRaw('ts_sizes.id as sz_id, CONCAT(sz_name," (",psc_name,")") as sz_name')
                    ->leftJoin('product_stocks', 'product_stocks.sz_id', '=', 'sizes.id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                    ->where('sz_delete', '!=', '1')
                    ->whereIn('products.pc_id', $where)->orderBy('sz_name')->pluck('sz_name', 'sz_id');
                } else {
                    $sz_id = Size::selectRaw('ts_sizes.id as sz_id, CONCAT(sz_name," (",psc_name,")") as sz_name')
                    ->leftJoin('product_stocks', 'product_stocks.sz_id', '=', 'sizes.id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                    ->where('sz_delete', '!=', '1')
                    ->where('products.pc_id', '=', $id[0])->orderBy('sz_name')->pluck('sz_name', 'sz_id');
                }
            } else if ($type == 'product_sub_category') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $sz_id = Size::selectRaw('ts_sizes.id as sz_id, CONCAT(sz_name," (",psc_name,")") as sz_name')
                    ->leftJoin('product_stocks', 'product_stocks.sz_id', '=', 'sizes.id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                    ->where('sz_delete', '!=', '1')
                    ->whereIn('products.psc_id', $where)->orderBy('sz_name')->pluck('sz_name', 'sz_id');
                } else {
                    $sz_id = Size::selectRaw('ts_sizes.id as sz_id, CONCAT(sz_name," (",psc_name,")") as sz_name')
                    ->leftJoin('product_stocks', 'product_stocks.sz_id', '=', 'sizes.id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                    ->where('sz_delete', '!=', '1')
                    ->where('products.psc_id', '=', $id[0])->orderBy('sz_name')->pluck('sz_name', 'sz_id');
                }
            } else if ($type == 'product_sub_sub_category') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $sz_id = Size::selectRaw('ts_sizes.id as sz_id, CONCAT(sz_name," (",psc_name,")") as sz_name')
                    ->leftJoin('product_stocks', 'product_stocks.sz_id', '=', 'sizes.id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                    ->where('sz_delete', '!=', '1')
                    ->whereIn('products.pssc_id', $where)->orderBy('sz_name')->pluck('sz_name', 'sz_id');
                } else {
                    $sz_id = Size::selectRaw('ts_sizes.id as sz_id, CONCAT(sz_name," (",psc_name,")") as sz_name')
                    ->leftJoin('product_stocks', 'product_stocks.sz_id', '=', 'sizes.id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                    ->where('sz_delete', '!=', '1')
                    ->where('products.pssc_id', '=', $id[0])->orderBy('sz_name')->pluck('sz_name', 'sz_id');
                }
            } else if ($type == 'brand') {
                $count = (Integer)count($id);
                $where = array();
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $where[] = $id[$i];
                    }
                    $sz_id = Size::selectRaw('ts_sizes.id as sz_id, CONCAT(sz_name," (",psc_name,")") as sz_name')
                    ->leftJoin('product_stocks', 'product_stocks.sz_id', '=', 'sizes.id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                    ->where('sz_delete', '!=', '1')
                    ->whereIn('products.br_id', $where)->orderBy('sz_name')->pluck('sz_name', 'sz_id');
                } else {
                    $sz_id = Size::selectRaw('ts_sizes.id as sz_id, CONCAT(sz_name," (",psc_name,")") as sz_name')
                    ->leftJoin('product_stocks', 'product_stocks.sz_id', '=', 'sizes.id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                    ->where('sz_delete', '!=', '1')
                    ->where('products.br_id', '=', $id[0])->orderBy('sz_name')->pluck('sz_name', 'sz_id');
                }
            } else {
                $sz_id = Size::selectRaw('ts_sizes.id as sz_id, CONCAT(sz_name," (",psc_name,")") as sz_name')
                ->leftJoin('product_stocks', 'product_stocks.sz_id', '=', 'sizes.id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
                ->where('sz_delete', '!=', '1')->orderBy('sz_name')->pluck('sz_name', 'sz_id');
            }
        } else {
            $sz_id = Size::selectRaw('ts_sizes.id as sz_id, CONCAT(sz_name," (",psc_name,")") as sz_name')
            ->leftJoin('product_stocks', 'product_stocks.sz_id', '=', 'sizes.id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
            ->where('sz_delete', '!=', '1')->orderBy('sz_name')->pluck('sz_name', 'sz_id');
        }
        $data = [
            'sz_id' => $sz_id
        ];
        return view('app.stock_data._reload_size', compact('data'));
    }
    
    public function getAgingDatatables(Request $request)
    {
        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();
        
        if(request()->ajax()) {
            return datatables()->of(ProductStock::selectRaw("ts_product_stocks.id as pst_id, pl_code, st_name, br_name, p_name, pc_name, psc_name, pssc_name, p_color, sz_name")
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.pc_id')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
            ->leftJoin('product_sub_sub_categories', 'product_sub_sub_categories.id', '=', 'products.pssc_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->whereNotIn('product_locations.pl_code', $exception)
            ->where('product_location_setups.pls_qty', '>', '0')
            ->where('product_locations.st_id', '=', $request->get('st_id'))
            ->groupBy('product_stocks.id'))
            ->editColumn('stock', function($data) use ($request, $exception) {
              $stock = ProductLocationSetup::select('pst_id', 'st_id', 'pls_qty')
              ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
              ->where('pst_id', '=', $data->pst_id)->where('st_id', '=', $request->get('st_id'))
              ->whereNotIn('product_locations.pl_code', $exception)
              ->sum('pls_qty');
              return $stock;
            })
            ->editColumn('aging_po', function($data) use ($request, $exception) {
                $check_poad = PurchaseOrderArticleDetailStatus::select(DB::raw('max(ts_purchase_order_article_detail_statuses.created_at) as poads_created'), 'po_invoice')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                ->leftJoin('purchase_order_articles', 'purchase_order_article_details.poa_id', '=', 'purchase_order_articles.id')
                ->leftJoin('purchase_orders', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
                ->where('purchase_order_article_details.pst_id', '=', $data->pst_id)
                ->where('purchase_orders.st_id', '=', $request->get('st_id'))
                ->orderByDesc('purchase_order_article_detail_statuses.id')
                ->get()->first();
                $days_remain_po = 99999;

                if (!empty($check_poad)) {
                    $date1_remain_po = $check_poad->poads_created;
                    $date2_remain_po = date('Y-m-d H:i:s');
                    $diff_remain_po = abs(strtotime($date1_remain_po) - strtotime($date2_remain_po));
                    if ($date1_remain_po>$date2_remain_po) {
                        $diff_remain_po = -($diff_remain_po);
                    }
                    $days_remain_po = round($diff_remain_po/86400);
                }
                if ($days_remain_po > 1000) {
                    $days_remain_po = '-';
                }
                return $days_remain_po;
            })
            ->editColumn('aging_tf', function($data) use ($request, $exception) {
                $stf = DB::table('stock_transfer_detail_statuses')->select('stf_code', DB::raw('max(ts_stock_transfer_detail_statuses.created_at) as created_at'))
                ->leftJoin('stock_transfer_details', 'stock_transfer_details.id', '=', 'stock_transfer_detail_statuses.stfd_id')
                ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
                ->where('stock_transfers.st_id_end', '=', $request->get('st_id'))
                ->where('product_stocks.id', '=', $data->pst_id)
                ->orderByDesc('stock_transfer_detail_statuses.id')
                ->whereNotNull('stock_transfer_detail_statuses.created_at')
                ->get()->first();
                $days_remain_tf = 99999;
                if (!empty ($stf)) {
                    $date1_remain_tf = $stf->created_at;
                    $date2_remain_tf = date('Y-m-d H:i:s');
                    $diff_remain_tf = abs(strtotime($date1_remain_tf) - strtotime($date2_remain_tf));
                    if ($date1_remain_tf>$date2_remain_tf) {
                        $diff_remain_tf = -($diff_remain_tf);
                    }
                    $days_remain_tf = round($diff_remain_tf/86400);
                }
                if ($days_remain_tf > 1000) {
                    $days_remain_tf = '-';
                }
                if ($days_remain_tf == '-') {
                    $bin = DB::table('bin_adjustments')
                    ->select(DB::raw('max(ts_bin_adjustments.created_at) as created_at'), 'ba_code')
                    ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'bin_adjustments.pls_id')
                    ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                    ->where('product_locations.st_id', '=', $request->get('st_id'))
                    ->where('product_location_setups.pst_id', '=', $data->pst_id)
                    ->whereNotNull('bin_adjustments.created_at')
                    ->get()->first();
                    if (!empty($bin)) {
                        $date1_remain = $bin->created_at;
                        $date2_remain = date('Y-m-d H:i:s');
                        $diff_remain = abs(strtotime($date1_remain) - strtotime($date2_remain));
                        if ($date1_remain>$date2_remain) {
                            $diff_remain = -($diff_remain);
                        }
                        $days_remain_tf = round($diff_remain/86400);
                    }
                }
                if ($days_remain_tf > 1000) {
                    $days_remain_tf = '-';
                }
                return $days_remain_tf;
            })
            ->filter(function ($instance) use ($request) {
                $br_id = $request->get('br_id');
                $pc_id = $request->get('pc_id');
                $psc_id = $request->get('psc_id');
                $pssc_id = $request->get('pssc_id');
                $sz_id = $request->get('sz_id');
                $st_id = $request->get('st_id');
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                    });
                }
                if (!empty($br_id)) {
                  $count = (Integer)count($br_id);
                  $where = array();
                  if ($count > 0) {
                      for ($i = 0; $i < $count; $i++) {
                          $where[] = $br_id[$i];
                      }
                      $instance->where(function($w) use($where){
                          $w->whereIn('products.br_id', $where);
                      });
                  } else {
                      $instance->where(function($w) use($br_id){
                          $w->where('products.br_id', '=', $br_id[0]);
                      });
                  }
                }
                if (!empty($pc_id)) {
                  $count = (Integer)count($pc_id);
                  $where = array();
                  if ($count > 0) {
                      for ($i = 0; $i < $count; $i++) {
                          $where[] = $pc_id[$i];
                      }
                      $instance->where(function($w) use($where){
                          $w->whereIn('products.pc_id', $where);
                      });
                  } else {
                      $instance->where(function($w) use($pc_id){
                          $w->where('products.pc_id', '=', $pc_id[0]);
                      });
                  }
                }
                if (!empty($psc_id)) {
                  $count = (Integer)count($psc_id);
                  $where = array();
                  if ($count > 0) {
                      for ($i = 0; $i < $count; $i++) {
                          $where[] = $psc_id[$i];
                      }
                      $instance->where(function($w) use($where){
                          $w->whereIn('products.psc_id', $where);
                      });
                  } else {
                      $instance->where(function($w) use($psc_id){
                          $w->where('products.psc_id', '=', $psc_id[0]);
                      });
                  }
                }
                if (!empty($pssc_id)) {
                  $count = (Integer)count($pssc_id);
                  $where = array();
                  if ($count > 0) {
                      for ($i = 0; $i < $count; $i++) {
                          $where[] = $pssc_id[$i];
                      }
                      $instance->where(function($w) use($where){
                          $w->whereIn('products.pssc_id', $where);
                      });
                  } else {
                      $instance->where(function($w) use($pssc_id){
                          $w->where('products.pssc_id', '=', $pssc_id[0]);
                      });
                  }
                }
                if (!empty($sz_id)) {
                  $count = (Integer)count($sz_id);
                  $where = array();
                  if ($count > 0) {
                      for ($i = 0; $i < $count; $i++) {
                          $where[] = $sz_id[$i];
                      }
                      $instance->where(function($w) use($where){
                          $w->whereIn('product_stocks.sz_id', $where);
                      });
                  } else {
                      $instance->where(function($w) use($sz_id){
                          $w->where('product_stocks.sz_id', '=', $sz_id[0]);
                      });
                  }
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }  
}