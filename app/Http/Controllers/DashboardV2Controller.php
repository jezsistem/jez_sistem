<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\PurchaseOrderArticleDetail;
use App\Models\PurchaseOrderArticleDetailStatus;
use App\Models\ProductStock;
use App\Models\ProductCategory;
use App\Models\ProductLocationSetup;
use App\Models\ExceptionLocation;
use App\Models\DebtList;
use App\Models\DebtListPayment;
use App\Models\Store;

class DashboardV2Controller extends Controller
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
            'segment' => request()->segment(1)
        ];
        return view('app.dashboard_v2.dashboard_v2', compact('data'));
    }

    private function deleteCurrentStore()
    {   
        DB::table('dashboard_information')
        ->whereDate('created_at', date('Y-m-d'))->delete();
    }

    private function deleteCurrentBrand()
    {   
        DB::table('brand_information')
        ->whereDate('created_at', date('Y-m-d'))->delete();
    }

    private function doFilter($string)
    {
        return ltrim(str_replace(array('/', '-', '<', '>', '&', '{', '}', '*'), ' ', $string));
    }

    public function fetchData() {
        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();

        $data = DB::table('product_stocks')
        ->select('product_stocks.id', 'br_name', 'p_name', 'p_color', 'sz_name')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_stocks.id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->whereNotIn('product_locations.pl_code', $exception)
        ->where('products.p_delete', '!=', '1')
        ->groupBy('product_stocks.id')
        ->get();
        if (!empty($data->first())) {
            $truncate = DB::table('no_symbol_articles')->truncate();
            $temp = array();
            $total_data = count($data);
            foreach ($data as $row) {
                $br_name = $this->doFilter($row->br_name);
                $p_name = $this->doFilter($row->p_name);
                $p_color = $this->doFilter($row->p_color);
                $sz_name = $this->doFilter($row->sz_name);
                $fullname = $br_name.' '.$p_name.' '.$p_color.' '.$sz_name;
                $brandname = $br_name.' '.$p_name;
                $temp[] = [
                    'pst_id' => $row->id,
                    'brand' => $br_name,
                    'name' => $p_name,
                    'color' => $p_color,
                    'size' => $sz_name,
                    'fullname' => $fullname,
                    'brandname' => $brandname,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $total_temp = count($temp);
                if ($total_data >= 2000 AND $total_temp >= 2000) {
                    $insert = DB::table('no_symbol_articles')->insert($temp);
                    if (!empty($insert)) {
                        $total_data = $total_data - $total_temp;
                        $temp = array();
                    }
                }
                if ($total_data < 2000) {
                    $insert = DB::table('no_symbol_articles')->insert($temp);
                    if (!empty($insert)) {
                        $temp = array();
                    }
                }
            }
        }
        $r['status'] = 200;
        return json_encode($r);
    }
    
    private function deleteNullPt() {
        $check = DB::table('pos_transactions')->select('id')
        ->whereDate('pos_transactions.created_at', '>=', date('Y-m-01'))
        ->whereDate('pos_transactions.created_at', '<=', date('Y-m-d'))
        ->get();
        foreach ($check as $row) {
            $exists = DB::table('pos_transaction_details')->where('pt_id', '=', $row->id)->exists();
            if (!$exists) {
                DB::table('pos_transactions')->where('id', '=', $row->id)->delete();
            }
        }
    }

    public function closeData()
    {
        $this->fetchData();
        $this->deleteNullPt();
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        $this->deleteCurrentStore();
        $st_id_data = DB::table('stores')->select('id')->get();
        if (!empty($st_id_data)) {
            $store_data = array();
            foreach ($st_id_data as $strow) {
                $sales = 0;
                $profit = 0;
                $purchase = 0;
                $cc_asset = 0;
                $con_asset = 0;
                $debt = 0;

                // sales and profit
                $pos_transaction = PosTransaction::select('id', 'st_id', 'st_id_ref', 'pos_admin_cost', 'pos_status', 'pos_refund')
                ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                ->where('st_id', '=', $strow->id)
                ->whereDate('pos_transactions.created_at', date('Y-m-d'))
                ->get();
                if (!empty($pos_transaction)) {
                    foreach ($pos_transaction as $pt) {
                        $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_transaction_details.pst_id as pst_id', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status')
                        ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                        ->where('pos_transaction_details.pt_id', $pt->id)
                        ->groupBy('pos_transaction_details.id')
                        ->get();
                        if (!empty($pos_transaction_detail)) {
                            $ptd = 0;
                            $ptd_profit = 0;
                            foreach ($pos_transaction_detail as $ptdr) {
                                if ($pt->pos_refund == '0' AND $ptdr->plst_status == 'INSTOCK') {
                                    continue;
                                }
                                $hpp = 0;
                                $poads = DB::table('purchase_order_article_detail_statuses')
                                ->select('poads_purchase_price', 'ps_purchase_price')
                                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                                ->where('product_stocks.id', '=', $ptdr->pst_id)
                                ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                                ->groupBy('poads_purchase_price')
                                ->get()->first();
                                if (!empty($poads)) {
                                    if (!empty($poads->poads_purchase_price)) {
                                        $hpp = $poads->poads_purchase_price;
                                    } else {
                                        $hpp = $poads->ps_purchase_price;
                                    }
                                }

                                if (!empty($ptdr->pos_td_marketplace_price)) {
                                    $ptd += $ptdr->pos_td_marketplace_price;
                                    $ptd_profit += $ptdr->pos_td_marketplace_price - ($ptdr->pos_td_qty * $hpp);
                                } else {
                                    $ptd += $ptdr->pos_td_discount_price;
                                    $ptd_profit += $ptdr->pos_td_discount_price - ($ptdr->pos_td_qty * $hpp);
                                }
                            }
                            if ($ptd > 0) {
                                $sales += $ptd - $pt->pos_admin_cost;
                            } else {
                                $sales += $ptd;
                            }
                            if ($ptd_profit > 0) {
                                $profit += $ptd_profit - $pt->pos_admin_cost;
                            } else {
                                $profit += $ptd_profit;
                            }
                        }
                    }
                }
                // sales and profit
                // purchase 
                $purchase = PurchaseOrderArticleDetailStatus::select('purchase_orders.st_id as st_id', 'poads_total_price')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
                ->where('purchase_orders.st_id', '=', $strow->id)
                ->whereDate('purchase_order_article_detail_statuses.created_at', date('Y-m-d'))->sum('poads_total_price');
                // purchase 
                // cc asset 
                $assets_cc = ProductLocationSetup::select('pls_qty', 'product_location_setups.pst_id', 'pl_code', 'stkt_id', 'product_locations.st_id as st_id', 'poads_purchase_price')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
                ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                ->whereIn('purchase_order_article_detail_statuses.stkt_id', ['1', '3'])
                ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                ->where('product_locations.st_id', '=', $strow->id)
                ->groupBy('product_location_setups.id')
                ->whereNotIn('pl_code', $exception)->get();
                if (!empty($assets_cc)) {
                    foreach ($assets_cc as $row) {
                        $purchase_price = DB::table('purchase_order_article_details')->select('poads_purchase_price', 'stkt_id')
                        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                        ->where('purchase_order_article_details.pst_id', '=', $row->pst_id)
                        ->whereIn('stkt_id', ['1', '3'])
                        ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                        ->groupBy('poads_purchase_price')
                        ->orderByDesc('purchase_order_article_detail_statuses.id')
                        ->get()->first();
                        $pp_cc = 0;
                        if (!empty($purchase_price)) {
                            $pp_cc = $purchase_price->poads_purchase_price;
                        } else {
                            $check_purchase_price = ProductStock::select('ps_purchase_price', 'p_purchase_price')
                            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                            ->where('product_stocks.id', '=', $row->pst_id)
                            ->get()->first();
                            if (!empty($check_purchase_price->ps_purchase_price)) {
                                $pp_cc = $check_purchase_price->ps_purchase_price;
                            } else {
                                $pp_cc = $check_purchase_price->p_purchase_price;
                            }
                        }
                        $cc_asset += ($row->pls_qty * $pp_cc);
                    }
                }
                // cc asset
                // con asset 
                $assets_con = ProductLocationSetup::select('pls_qty', 'product_location_setups.pst_id', 'pl_code', 'stkt_id', 'product_locations.st_id as st_id', 'poads_purchase_price')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
                ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                ->whereIn('purchase_order_article_detail_statuses.stkt_id', ['2'])
                ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                ->where('product_locations.st_id', '=', $strow->id)
                ->groupBy('product_location_setups.id')
                ->whereNotIn('pl_code', $exception)->get();
                if (!empty($assets_con)) {
                    foreach ($assets_con as $row) {
                        $purchase_price = DB::table('purchase_order_article_details')->select('poads_purchase_price', 'stkt_id')
                        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                        ->where('purchase_order_article_details.pst_id', '=', $row->pst_id)
                        ->whereIn('stkt_id', ['2'])
                        ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                        ->groupBy('poads_purchase_price')
                        ->orderByDesc('purchase_order_article_detail_statuses.id')
                        ->get()->first();
                        $pp_con = 0;
                        if (!empty($purchase_price)) {
                            $pp_con = $purchase_price->poads_purchase_price;
                        } else {
                            $check_purchase_price = ProductStock::select('ps_purchase_price', 'p_purchase_price')
                            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                            ->where('product_stocks.id', '=', $row->pst_id)
                            ->get()->first();
                            if (!empty($check_purchase_price->ps_purchase_price)) {
                                $pp_con = $check_purchase_price->ps_purchase_price;
                            } else {
                                $pp_con = $check_purchase_price->p_purchase_price;
                            }
                        }
                        $con_asset += ($row->pls_qty * $pp_con);
                    }
                }
                // con asset 
                // debt 
                $debt_list = DebtList::select('dl_total', 'st_id')->where('debt_lists.dl_delete', '!=', '1')->where('st_id', '=', $strow->id)->sum('dl_total');
                $debt_list_payment = DebtListPayment::select('dlp_value', 'st_id')->leftJoin('debt_lists', 'debt_lists.id', '=', 'debt_list_payments.dl_id')
                    ->where('st_id', '=', $strow->id)->where('debt_lists.dl_delete', '!=', '1')->sum('dlp_value');
                $debt = $debt_list - $debt_list_payment;
                // debt

                $store_data[] = [
                    'st_id' => $strow->id,
                    'sales' => $sales, // done
                    'profit' => $profit, // done
                    'purchase' => $purchase, // done 
                    'cc_asset' => $cc_asset,
                    'con_asset' => $con_asset,
                    'debt' => $debt, // done
                    'created_at' => date('Y-m-d H:i:s'), // done
                    'updated_at' => date('Y-m-d H:i:s'), // done
                ];
            }
            $save = DB::table('dashboard_information')->insert($store_data);
        }
        /////////////////////////////////// BRAND
        $this->deleteCurrentBrand();
        $br_id_data = DB::table('brands')->select('id')
        ->where('br_delete', '!=' , '1')->get();
        if (!empty($br_id_data)) {
            $brand_data = array();
            foreach ($br_id_data as $brrow) {
                $sales = 0;
                $profit = 0;
                $purchase = 0;
                $cc_asset = 0;
                $con_asset = 0;
                $debt = 0;

                // sales and profit
                $pos_transaction = PosTransaction::select('id', 'st_id', 'st_id_ref', 'pos_admin_cost', 'pos_status', 'pos_refund')
                ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                ->whereDate('pos_transactions.created_at', date('Y-m-d'))
                ->get();
                if (!empty($pos_transaction)) {
                    foreach ($pos_transaction as $pt) {
                        $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_transaction_details.pst_id as pst_id', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status')
                        ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->where('products.br_id', $brrow->id)
                        ->where('pos_transaction_details.pt_id', $pt->id)
                        ->groupBy('pos_transaction_details.id')
                        ->get();
                        if (!empty($pos_transaction_detail)) {
                            $ptd = 0;
                            $ptd_profit = 0;
                            foreach ($pos_transaction_detail as $ptdr) {
                                if ($pt->pos_refund == '0' AND $ptdr->plst_status == 'INSTOCK') {
                                    continue;
                                }
                                $hpp = 0;
                                $poads = DB::table('purchase_order_article_detail_statuses')
                                ->select('poads_purchase_price', 'ps_purchase_price')
                                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                                ->where('product_stocks.id', '=', $ptdr->pst_id)
                                ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                                ->groupBy('poads_purchase_price')
                                ->get()->first();
                                if (!empty($poads)) {
                                    if (!empty($poads->poads_purchase_price)) {
                                        $hpp = $poads->poads_purchase_price;
                                    } else {
                                        $hpp = $poads->ps_purchase_price;
                                    }
                                }

                                if (!empty($ptdr->pos_td_marketplace_price)) {
                                    $ptd += $ptdr->pos_td_marketplace_price;
                                    $ptd_profit += $ptdr->pos_td_marketplace_price - ($ptdr->pos_td_qty * $hpp);
                                } else {
                                    $ptd += $ptdr->pos_td_discount_price;
                                    $ptd_profit += $ptdr->pos_td_discount_price - ($ptdr->pos_td_qty * $hpp);
                                }
                            }
                            if ($ptd > 0) {
                                $sales += $ptd - $pt->pos_admin_cost;
                            } else {
                                $sales += $ptd;
                            }
                            if ($ptd_profit > 0) {
                                $profit += $ptd_profit - $pt->pos_admin_cost;
                            } else {
                                $profit += $ptd_profit;
                            }
                        }
                    }
                }
                // sales and profit
                // purchase 
                $purchase = PurchaseOrderArticleDetailStatus::select('poads_total_price')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->where('products.br_id', '=', $brrow->id)
                ->whereDate('purchase_order_article_detail_statuses.created_at', date('Y-m-d'))->sum('poads_total_price');
                // purchase 
                // debt 
                $debt_list = DebtList::select('dl_total')->where('debt_lists.dl_delete', '!=', '1')
                ->where('br_id', '=', $brrow->id)->sum('dl_total');
                $debt_list_payment = DebtListPayment::select('dlp_value')
                ->leftJoin('debt_lists', 'debt_lists.id', '=', 'debt_list_payments.dl_id')->where('debt_lists.dl_delete', '!=', '1')
                ->where('br_id', '=', $brrow->id)->sum('dlp_value');
                $debt = $debt_list - $debt_list_payment;
                // debt 
                // cc asset 
                $assets_cc = ProductLocationSetup::select('pls_qty', 'product_location_setups.pst_id', 'pl_code', 'stkt_id', 'product_locations.st_id as st_id', 'poads_purchase_price')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'product_stocks.p_id', '=', 'products.id')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
                ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                ->whereIn('purchase_order_article_detail_statuses.stkt_id', ['1', '3'])
                ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                ->where('products.br_id', '=', $brrow->id)
                ->groupBy('product_location_setups.id')
                ->whereNotIn('pl_code', $exception)->get();
                if (!empty($assets_cc)) {
                    foreach ($assets_cc as $row) {
                        $purchase_price = DB::table('purchase_order_article_details')->select('poads_purchase_price', 'stkt_id')
                        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                        ->where('purchase_order_article_details.pst_id', '=', $row->pst_id)
                        ->whereIn('stkt_id', ['1', '3'])
                        ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                        ->groupBy('poads_purchase_price')
                        ->orderByDesc('purchase_order_article_detail_statuses.id')
                        ->get()->first();
                        $pp_cc = 0;
                        if (!empty($purchase_price)) {
                            $pp_cc = $purchase_price->poads_purchase_price;
                        } else {
                            $check_purchase_price = ProductStock::select('ps_purchase_price', 'p_purchase_price')
                            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                            ->where('product_stocks.id', '=', $row->pst_id)
                            ->get()->first();
                            if (!empty($check_purchase_price->ps_purchase_price)) {
                                $pp_cc = $check_purchase_price->ps_purchase_price;
                            } else {
                                $pp_cc = $check_purchase_price->p_purchase_price;
                            }
                        }
                        $cc_asset += ($row->pls_qty * $pp_cc);
                    }
                }
                // cc asset
                // con asset 
                $assets_con = ProductLocationSetup::select('pls_qty', 'product_location_setups.pst_id', 'pl_code', 'stkt_id', 'product_locations.st_id as st_id', 'poads_purchase_price')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                ->leftJoin('products', 'product_stocks.p_id', '=', 'products.id')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
                ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                ->whereIn('purchase_order_article_detail_statuses.stkt_id', ['2'])
                ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                ->where('products.br_id', '=', $brrow->id)
                ->groupBy('product_location_setups.id')
                ->whereNotIn('pl_code', $exception)->get();
                if (!empty($assets_con)) {
                    foreach ($assets_con as $row) {
                        $purchase_price = DB::table('purchase_order_article_details')->select('poads_purchase_price', 'stkt_id')
                        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                        ->where('purchase_order_article_details.pst_id', '=', $row->pst_id)
                        ->whereIn('stkt_id', ['2'])
                        ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                        ->groupBy('poads_purchase_price')
                        ->orderByDesc('purchase_order_article_detail_statuses.id')
                        ->get()->first();
                        $pp_con = 0;
                        if (!empty($purchase_price)) {
                            $pp_con = $purchase_price->poads_purchase_price;
                        } else {
                            $check_purchase_price = ProductStock::select('ps_purchase_price', 'p_purchase_price')
                            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                            ->where('product_stocks.id', '=', $row->pst_id)
                            ->get()->first();
                            if (!empty($check_purchase_price->ps_purchase_price)) {
                                $pp_con = $check_purchase_price->ps_purchase_price;
                            } else {
                                $pp_con = $check_purchase_price->p_purchase_price;
                            }
                        }
                        $con_asset += ($row->pls_qty * $pp_con);
                    }
                }
                // con asset 

                $brand_data[] = [
                    'br_id' => $brrow->id,
                    'sales' => $sales,
                    'profit' => $profit,
                    'purchase' => $purchase,
                    'cc_asset' => $cc_asset,
                    'con_asset' => $con_asset,
                    'debt' => $debt,
                    'created_at' => date('Y-m-d H:i:s'), // done
                    'updated_at' => date('Y-m-d H:i:s'), // done
                ];
            }
            $save = DB::table('brand_information')->insert($brand_data);
        }
        $r['status'] = '200';
        return json_encode($r);
    }

    public function summaryV2(Request $request)
    {
        $date = $request->post('date');
        $dashboard_summary = DB::table('dashboard_information')->select('dashboard_information.id as id', 'sales', 'profit', 'purchase', 'cc_asset', 'con_asset', 'debt', 'created_at')
        ->where(function($w) use ($request) {
            if (!empty($request->get('date'))) {
                $exp = explode('|', $request->get('date'));
                $start = '';
                $end = '';
                if (count($exp) > 1) {
                    $start = $exp[0];
                    $end = $exp[1];
                    $w->whereDate('dashboard_information.created_at', '>=', $start)
                    ->whereDate('dashboard_information.created_at', '<=', $end);
                } else {
                    $start = $request->get('date');
                    $w->whereDate('dashboard_information.created_at', '=', $start);
                }
            }
        })->get();
        $nett_sales = 0;
        $profit = 0;
        $purchase = 0;
        $assets = 0;
        $consign_assets = 0;
        $debt = 0;
        if (!empty($dashboard_summary)) {
            foreach ($dashboard_summary as $ds) {
                $nett_sales += $ds->sales;
                $profit += $ds->profit;
                $purchase += $ds->purchase;
                $assets += $ds->cc_asset;
                $consign_assets += $ds->con_asset;
                $debt += $ds->debt;
            }
        }
        $r['nett_sales'] = number_format($nett_sales);
        $r['profit'] = number_format($profit);
        $r['purchase'] = number_format($purchase);
        $r['assets'] = number_format($assets);
        $r['consign_assets'] = number_format($consign_assets);
        $r['debt'] = number_format($debt);
        $r['status'] = '200';
        return json_encode($r);
    }

    public function getStoreInfoDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('dashboard_information')->select('dashboard_information.id as id', 'st_name', 'sales', 'profit', 'purchase', 'cc_asset', 'con_asset', 'debt', 'dashboard_information.created_at as created_at')
            ->leftJoin('stores', 'stores.id', '=', 'dashboard_information.st_id')
            ->where(function($w) use ($request) {
                if (!empty($request->get('dashboard_date'))) {
                    $exp = explode('|', $request->get('dashboard_date'));
                    $start = '';
                    $end = '';
                    if (count($exp) > 1) {
                        $start = $exp[0];
                        $end = $exp[1];
                        $w->whereDate('dashboard_information.created_at', '>=', $start)
                        ->whereDate('dashboard_information.created_at', '<=', $end);
                    } else {
                        $start = $request->get('dashboard_date');
                        $w->whereDate('dashboard_information.created_at', '=', $start);
                    }
                }
            }))
            ->editColumn('created_at_x', function($data) {
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->editColumn('sales', function($data) {
                return number_format($data->sales);
            })
            ->editColumn('profit', function($data) {
                return number_format($data->profit);
            })
            ->editColumn('purchase', function($data) {
                return number_format($data->purchase);
            })
            ->editColumn('cc_asset', function($data) {
                return number_format($data->cc_asset);
            })
            ->editColumn('con_asset', function($data) {
                return number_format($data->con_asset);
            })
            ->editColumn('debt', function($data) {
                return number_format($data->debt);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('st_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getBrandInfoDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('brand_information')->select('brand_information.id as id', 'br_name', 'sales', 'profit', 'purchase', 'cc_asset', 'con_asset', 'debt', 'brand_information.created_at as created_at')
            ->leftJoin('brands', 'brands.id', '=', 'brand_information.br_id')
            ->where(function($w) use ($request) {
                if (!empty($request->get('dashboard_date'))) {
                    $exp = explode('|', $request->get('dashboard_date'));
                    $start = '';
                    $end = '';
                    if (count($exp) > 1) {
                        $start = $exp[0];
                        $end = $exp[1];
                        $w->whereDate('brand_information.created_at', '>=', $start)
                        ->whereDate('brand_information.created_at', '<=', $end);
                    } else {
                        $start = $request->get('dashboard_date');
                        $w->whereDate('brand_information.created_at', '=', $start);
                    }
                }
            }))
            ->editColumn('created_at_x', function($data) {
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->editColumn('sales', function($data) {
                return number_format($data->sales);
            })
            ->editColumn('profit', function($data) {
                return number_format($data->profit);
            })
            ->editColumn('purchase', function($data) {
                return number_format($data->purchase);
            })
            ->editColumn('cc_asset', function($data) {
                return number_format($data->cc_asset);
            })
            ->editColumn('con_asset', function($data) {
                return number_format($data->con_asset);
            })
            ->editColumn('debt', function($data) {
                return number_format($data->debt);
            })
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

}
