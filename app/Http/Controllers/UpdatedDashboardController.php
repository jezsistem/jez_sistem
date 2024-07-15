<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\ExceptionLocation;
use App\Exports\ExportData;
use Maatwebsite\Excel\Facades\Excel;

class UpdatedDashboardController extends Controller
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
        $user_activity = new UserActivity;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $select_activity = ['user_activities.id as uaid', 'u_name', 'ua_description', 'user_activities.created_at as ua_created_at'];
        $activity = $user_activity->getAllJoinData($select_activity);
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'activity' => $activity,
            'st_id' => DB::table('stores')->where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'pc_id' => DB::table('product_categories')->where('pc_delete', '!=', '1')->orderByDesc('id')->pluck('pc_name', 'id'),
            'segment' => request()->segment(1),
        ];

        return view('app.updated_dashboard.dashboard', compact('data'));
    }

    public function getSummaries(Request $req)
    {
        $date = $req->input('date');
        $st_id = $req->input('store');
        $label = $req->input('label');
        $division = $req->input('division');

        $start = null;
        $end = null;
        $exp = explode('|', $date);
        $total = count($exp);
        if ($total > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            $start = $date;
        }
        $adm_cross_nett_sales = 0;
        $adm_cross_profit = 0;
        $adm_nett_sales = 0;
        $adm_profit = 0;
        $cross_nett_sales = 0;
        $cross_profit = 0;
        $nett_sales = 0;
        $profit = 0;
        $purchases = 0;
        $cc_assets = 0;
        $c_assets = 0;
        $debts = 0;
        $cc_exc_assets = 0;
        $c_exc_assets = 0;
        $gid = 0;
        $git = 0;

        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();
            
        $draft =  DB::table('stock_transfer_details')
        ->selectRaw("stfd_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
        ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->groupBy('stock_transfer_details.id')
        ->where(function($w) use ($start, $end, $st_id) {
            if (!empty($st_id)) {
                $w->whereIn('stock_transfers.st_id_start', $st_id);
            } else {
                $w->where('stock_transfers.st_id_start', '!=', '4');
            }
            $w->where('stock_transfers.stf_status', '=', '3');
        })
        ->get();
        if (!empty($draft)) {
            foreach ($draft as $row) {
                $purchase = 0;
                if (!empty ($row->purchase)) {
                    $purchase = round($row->purchase);
                } else {
                    if (!empty($row->poad_total_price)) {
                        $purchase = round($row->poad_total_price / $row->poad_qty);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                }
                $gid += $row->stfd_qty * $purchase;
            }
        }

        $transit = DB::table('stock_transfer_details')
        ->selectRaw("ts_stock_transfer_details.id as id, stfd_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
        ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->groupBy('stock_transfer_details.id')
        ->where(function($w) use ($start, $end, $st_id) {
            if (!empty($st_id)) {
                $w->whereIn('stock_transfers.st_id_start', $st_id);
            } else {
                $w->where('stock_transfers.st_id_start', '!=', '4');
            }
            $w->where('stock_transfers.stf_status', '=', '1')
            ->where('stock_transfer_details.stfd_status', '=', '1');
        })
        ->get();
        if (!empty($transit)) {
            foreach ($transit as $row) {
                $purchase = 0;
                if (!empty ($row->purchase)) {
                    $purchase = round($row->purchase);
                } else {
                    if (!empty($row->poad_total_price)) {
                        $purchase = round($row->poad_total_price / $row->poad_qty);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                }
                $trans_qty = $row->stfd_qty;
                $receive_qty = DB::table('stock_transfer_detail_statuses')->where('stfd_id', '=', $row->id)->sum('stfds_qty');
                if ($receive_qty > $trans_qty) {
                    $receive_qty = $trans_qty;
                }
                $git += ($trans_qty - $receive_qty) * $purchase;
            }
        }


        $admin_cost = DB::table('pos_transactions')->select('pos_admin_cost', 'pos_status')
        ->where(function($w) use ($start, $end, $st_id, $label, $division) {
            if (!empty($st_id)) {
                $w->whereIn('pos_transactions.st_id', $st_id);
            } else {
                $w->where('pos_transactions.st_id', '!=', '4');
            }
            if ($division != 'all') {
                if ($division == 'online') {
                    $w->where('pos_transactions.stt_id', '=', '1');
                } else {
                    $w->where('pos_transactions.stt_id', '=', '2');
                }
            }
            if (!empty($end)) {
                $w->whereDate('pos_transactions.created_at', '>=', $start)
                ->whereDate('pos_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('pos_transactions.created_at', '=', $start);
            }
        })
        ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
        ->sum('pos_admin_cost');

        $cross_admin_cost = DB::table('pos_transactions')->select('pos_admin_cost', 'pos_status')
        ->where(function($w) use ($start, $end, $st_id, $label, $division) {
            if (!empty($st_id)) {
                $w->whereIn('pos_transactions.st_id_ref', $st_id);
            } else {
                $w->whereNotNull('pos_transactions.st_id_ref')
                ->where('pos_transactions.st_id_ref', '!=', '4');
            }
            if ($division != 'all') {
                if ($division == 'online') {
                    $w->where('pos_transactions.stt_id', '=', '1');
                } else {
                    $w->where('pos_transactions.stt_id', '=', '2');
                }
            }
            if (!empty($end)) {
                $w->whereDate('pos_transactions.created_at', '>=', $start)
                ->whereDate('pos_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('pos_transactions.created_at', '=', $start);
            }
        })
        ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
        ->sum('pos_admin_cost');

        $ns = DB::table('pos_transaction_details')->select('pos_transactions.id as pt_id', 'pos_td_discount_price', 'pos_td_marketplace_price', 'pos_status', 'pos_refund', 'pst_id')
        ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
        ->where(function($w) use ($start, $end, $st_id, $division) {
            if (!empty($st_id)) {
                $w->whereIn('pos_transactions.st_id', $st_id);
            }
//            else {
//                $w->where('pos_transactions.st_id', '!=', '4');
//            }
            if ($division != 'all') {
                if ($division == 'online') {
                    $w->where('pos_transactions.stt_id', '=', '1');
                } else {
                    $w->where('pos_transactions.stt_id', '=', '2');
                }
            }
            if (!empty($end)) {
                $w->whereDate('pos_transactions.created_at', '>=', $start)
                ->whereDate('pos_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('pos_transactions.created_at', '=', $start);
            }
        })
        ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
        ->groupBy('pos_transaction_details.id')
        ->get();
        if (!empty($ns->first())) {
            $pt_id = null;
            $price = 0;
            foreach ($ns as $row) {
                if (!empty($row->pos_td_marketplace_price)) {
                    $price = $row->pos_td_marketplace_price;
                } else {
                    $price = $row->pos_td_discount_price;
                }
                $nett_sales += $price;
            }
            $adm_nett_sales = $nett_sales;
            $nett_sales = $nett_sales - $admin_cost;
        }

        $pf = DB::table('pos_transaction_details')
        ->selectRaw("ts_pos_transactions.created_at as created_at, pos_invoice, br_name, p_name, p_color, sz_name, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
        ->where(function ($w) use ($start, $end, $st_id, $division) {
            if (!empty($end)) {
                $w->whereDate('pos_transactions.created_at', '>=', $start)
                ->whereDate('pos_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('pos_transactions.created_at', '=', $start);
            }
            if (!empty($st_id)) {
                $w->whereIn('pos_transactions.st_id', $st_id);
            } else {
                $w->where('pos_transactions.st_id', '!=', '4');
            }
            if ($division != 'all') {
                if ($division == 'online') {
                    $w->where('pos_transactions.stt_id', '=', '1');
                } else {
                    $w->where('pos_transactions.stt_id', '=', '2');
                }
            }
            $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
        })
        ->groupBy('pos_transaction_details.id')
        ->get();
        if (!empty($pf->first())) {
            foreach ($pf as $row) {
                $created_at = date('d/m/Y H:i:s', strtotime($row->created_at));
                $total = 0;
                if (!empty($row->pos_td_marketplace_price)) {
                    $total = $row->pos_td_marketplace_price;
                } else {
                    $total = $row->pos_td_discount_price;
                }
                $purchase = 0;
                if (!empty ($row->purchase)) {
                    $purchase = round($row->purchase);
                } else {
                    if (!empty($row->poad_total_price)) {
                        $purchase = round($row->poad_total_price / $row->poad_qty);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                }
                $profit += $total-($row->pos_td_qty*$purchase);
            }
            $adm_profit = $profit;
            $profit = $profit - $admin_cost;
        }

        $c_ns = DB::table('pos_transaction_details')->select('pos_transactions.id as pt_id', 'pos_td_discount_price', 'pos_td_marketplace_price', 'pos_status', 'pos_refund', 'pst_id')
        ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
        ->where(function($w) use ($start, $end, $st_id, $division) {
            if (!empty($st_id)) {
                $w->whereIn('pos_transactions.st_id_ref', $st_id);
            } else {
                $w->whereNotNull('pos_transactions.st_id_ref')
                ->where('pos_transactions.st_id_ref', '!=', '4');
            }
            if ($division != 'all') {
                if ($division == 'online') {
                    $w->where('pos_transactions.stt_id', '=', '1');
                } else {
                    $w->where('pos_transactions.stt_id', '=', '2');
                }
            }
            if (!empty($end)) {
                $w->whereDate('pos_transactions.created_at', '>=', $start)
                ->whereDate('pos_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('pos_transactions.created_at', '=', $start);
            }
        })
        ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
        ->groupBy('pos_transaction_details.id')
        ->get();
        if (!empty($c_ns->first())) {
            $pt_id = null;
            $price = 0;
            foreach ($c_ns as $row) {
                if (!empty($row->pos_td_marketplace_price)) {
                    $price = $row->pos_td_marketplace_price;
                } else {
                    $price = $row->pos_td_discount_price;
                }
                $cross_nett_sales += $price;
            }
            $adm_cross_nett_sales = $cross_nett_sales;
            $cross_nett_sales = $cross_nett_sales - $cross_admin_cost;
        }

        $c_pf = DB::table('pos_transaction_details')
        ->selectRaw("ts_pos_transactions.created_at as created_at, pos_invoice, br_name, p_name, p_color, sz_name, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
        ->where(function ($w) use ($start, $end, $st_id, $division) {
            if (!empty($end)) {
                $w->whereDate('pos_transactions.created_at', '>=', $start)
                ->whereDate('pos_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('pos_transactions.created_at', '=', $start);
            }
            if (!empty($st_id)) {
                $w->whereIn('pos_transactions.st_id_ref', $st_id);
            } else {
                $w->whereNotNull('pos_transactions.st_id_ref')
                ->where('pos_transactions.st_id_ref', '!=', '4');
            }
            if ($division != 'all') {
                if ($division == 'online') {
                    $w->where('pos_transactions.stt_id', '=', '1');
                } else {
                    $w->where('pos_transactions.stt_id', '=', '2');
                }
            }
            $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
        })
        ->groupBy('pos_transaction_details.id')
        ->get();
        if (!empty($c_pf->first())) {
            foreach ($c_pf as $row) {
                $created_at = date('d/m/Y H:i:s', strtotime($row->created_at));
                $total = 0;
                if (!empty($row->pos_td_marketplace_price)) {
                    $total = $row->pos_td_marketplace_price;
                } else {
                    $total = $row->pos_td_discount_price;
                }
                $purchase = 0;
                if (!empty ($row->purchase)) {
                    $purchase = round($row->purchase);
                } else {
                    if (!empty($row->poad_total_price)) {
                        $purchase = round($row->poad_total_price / $row->poad_qty);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                }
                $cross_profit += $total-($row->pos_td_qty*$purchase);
            }
            $adm_cross_profit = $cross_profit;
            $cross_profit = $cross_profit - $cross_admin_cost;
        }

        $purchases = DB::table('purchase_order_article_detail_statuses')->select('poads_total_price')
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
        ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
        ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
        ->where(function($w) use ($start, $end, $st_id) {
            if (!empty($st_id)) {
                $w->whereIn('purchase_orders.st_id', $st_id);
            } else {
                $w->where('purchase_orders.st_id', '!=', '4');
            }
            if (!empty($end)) {
                $w->whereDate('purchase_order_article_detail_statuses.created_at', '>=', $start)
                ->whereDate('purchase_order_article_detail_statuses.created_at', '<=', $end);
            } else {
                $w->whereDate('purchase_order_article_detail_statuses.created_at', '=', $start);
            }
        })->sum('poads_total_price');

        $ccassets = DB::table('product_location_setups')
        ->selectRaw("ts_product_location_setups.pls_qty as pls_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->whereNotIn('pl_code', $exception)
        ->where(function($w) use ($st_id) {
            if (!empty($st_id)) {
                $w->whereIn('product_locations.st_id', $st_id);
            } else {
                $w->where('product_locations.st_id', '!=', '4');
            }
        })
        ->where('product_location_setups.pls_qty', '>', '0')
        ->whereIn('stkt_id', ['1', '3'])
        ->groupBy('product_location_setups.id')
        ->get();
        if (!empty($ccassets->first())) {
            foreach ($ccassets as $row) {
                $pp = 0;
                if (!empty ($row->purchase)) {
                    $pp = round($row->purchase);
                } else {
                    if (!empty($row->ps_purchase_price)) {
                        $pp = $row->ps_purchase_price;
                    } else {
                        $pp = $row->p_purchase_price;
                    }
                }
                $cc_assets += ($row->pls_qty * $pp);
            }
        }

        $cassets = DB::table('product_location_setups')
        ->selectRaw("ts_product_location_setups.pls_qty as pls_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->whereNotIn('pl_code', $exception)
        ->where(function($w) use ($st_id) {
            if (!empty($st_id)) {
                $w->whereIn('product_locations.st_id', $st_id);
            } else {
                $w->where('product_locations.st_id', '!=', '4');
            }
        })
        ->where('product_location_setups.pls_qty', '>', '0')
        ->where('stkt_id', '=', '2')
        ->groupBy('product_location_setups.id')
        ->get();
        if (!empty($cassets->first())) {
            foreach ($cassets as $row) {
                $pp = 0;
                if (!empty ($row->purchase)) {
                    $pp = round($row->purchase);
                } else {
                    if (!empty($row->ps_purchase_price)) {
                        $pp = $row->ps_purchase_price;
                    } else {
                        $pp = $row->p_purchase_price;
                    }
                }
                $c_assets += ($row->pls_qty * $pp);
            }
        }

        $ccexcassets = DB::table('product_location_setups')
        ->selectRaw("ts_product_location_setups.pls_qty as pls_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->whereIn('pl_code', $exception)
        ->where(function($w) use ($st_id) {
            if (!empty($st_id)) {
                $w->whereIn('product_locations.st_id', $st_id);
            } else {
                $w->where('product_locations.st_id', '!=', '4');
            }
        })
        ->where('product_location_setups.pls_qty', '>', '0')
        ->whereIn('stkt_id', ['1', '3'])
        ->groupBy('product_location_setups.id')
        ->get();
        if (!empty($ccexcassets->first())) {
            foreach ($ccexcassets as $row) {
                $pp = 0;
                if (!empty ($row->purchase)) {
                    $pp = round($row->purchase);
                } else {
                    if (!empty($row->ps_purchase_price)) {
                        $pp = $row->ps_purchase_price;
                    } else {
                        $pp = $row->p_purchase_price;
                    }
                }
                $cc_exc_assets += ($row->pls_qty * $pp);
            }
        }

        $cexcassets = DB::table('product_location_setups')
        ->selectRaw("ts_product_location_setups.pls_qty as pls_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->whereIn('pl_code', $exception)
        ->where(function($w) use ($st_id) {
            if (!empty($st_id)) {
                $w->whereIn('product_locations.st_id', $st_id);
            } else {
                $w->where('product_locations.st_id', '!=', '4');
            }
        })
        ->where('product_location_setups.pls_qty', '>', '0')
        ->where('stkt_id', '=', '2')
        ->groupBy('product_location_setups.id')
        ->get();
        if (!empty($cexcassets->first())) {
            foreach ($cexcassets as $row) {
                $pp = 0;
                if (!empty ($row->purchase)) {
                    $pp = round($row->purchase);
                } else {
                    if (!empty($row->ps_purchase_price)) {
                        $pp = $row->ps_purchase_price;
                    } else {
                        $pp = $row->p_purchase_price;
                    }
                }
                $c_exc_assets += ($row->pls_qty * $pp);
            }
        }

        $debt_list = DB::table('debt_lists')->select('dl_total', 'st_id')->where('debt_lists.dl_delete', '!=', '1')
        ->where(function($w) use ($st_id) {
            if (!empty($st_id)) {
                $w->whereIn('st_id', $st_id);
            } else {
                $w->where('st_id', '!=', '4');
            }
        })->sum('dl_total');
        $debt_list_payment = DB::table('debt_list_payments')->select('dlp_value', 'st_id')->leftJoin('debt_lists', 'debt_lists.id', '=', 'debt_list_payments.dl_id')
        ->where(function($w) use ($st_id) {
            if (!empty($st_id)) {
                $w->whereIn('st_id', $st_id);
            } else {
                $w->where('st_id', '!=', '4');
            }
        })->where('debt_lists.dl_delete', '!=', '1')->sum('dlp_value');
        $debts = $debt_list - $debt_list_payment;

        $r['adm_nett_sales'] = number_format($adm_nett_sales);
        $r['adm_profit'] = number_format($adm_profit);
        $r['adm_cross_nett_sales'] = number_format($adm_cross_nett_sales);
        $r['adm_cross_profit'] = number_format($adm_cross_profit);
        $r['nett_sales'] = number_format($nett_sales);
        $r['profit'] = number_format($profit);
        $r['cross_nett_sales'] = number_format($cross_nett_sales);
        $r['cross_profit'] = number_format($cross_profit);
        $r['purchase'] = number_format($purchases);
        $r['cc_assets'] = number_format($cc_assets);
        $r['c_assets'] = number_format($c_assets);
        $r['debts'] = number_format($debts);
        $r['cc_exc_assets'] = number_format($cc_exc_assets);
        $r['c_exc_assets'] = number_format($c_exc_assets);
        $r['gid'] = number_format($gid);
        $r['git'] = number_format($git);
        $r['status'] = 200;
        return json_encode($r);
    }

    public function loadTable(Request $req)
    {
        $label = $req->post('label');
        session()->put('st_id', $req->post('store'));
        $data = [
            'date' => $req->post('date'),
            'label' => $req->post('label'),
            'division' => $req->post('division')
        ];
        if ($label == 'sales') {
            return view('app.updated_dashboard._load_sales', compact('data'));
        }
        if ($label == 'profits') {
            return view('app.updated_dashboard._load_profits', compact('data'));
        }
        if ($label == 'cross_sales') {
            return view('app.updated_dashboard._load_cross_sales', compact('data'));
        }
        if ($label == 'cross_profits') {
            return view('app.updated_dashboard._load_cross_profits', compact('data'));
        }
        if ($label == 'purchases') {
            return view('app.updated_dashboard._load_purchases', compact('data'));
        }
        if ($label == 'cc_exc_assets') {
            return view('app.updated_dashboard._load_exc_cc_assets', compact('data'));
        }
        if ($label == 'c_exc_assets') {
            return view('app.updated_dashboard._load_exc_c_assets', compact('data'));
        }
        if ($label == 'cc_assets') {
            return view('app.updated_dashboard._load_cc_assets', compact('data'));
        }
        if ($label == 'c_assets') {
            return view('app.updated_dashboard._load_c_assets', compact('data'));
        }
    }

    public function getNettSales(Request $req)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('pos_transactions')
            ->selectRaw("ts_pos_transactions.id as id, pos_invoice, sum(ts_pos_transaction_details.pos_td_qty) as item, sum(ts_pos_transaction_details.pos_td_discount_price) as item_total_1, sum(ts_pos_transaction_details.pos_td_marketplace_price) as item_total_2, ts_pos_transactions.created_at as created_at, pos_admin_cost")
            ->leftJoin('pos_transaction_details', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->where(function ($w) use ($req) {
                $date = $req->get('date');
                $start = null;
                $end = null;
                $exp = explode('|', $date);
                $total = count($exp);
                if ($total > 1) {
                    if ($exp[0] != $exp[1]) {
                        $start = $exp[0];
                        $end = $exp[1];
                    } else {
                        $start = $exp[0];
                    }
                } else {
                    $start = $date;
                }
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                $st_id = session()->get('st_id');
                $division = $req->get('division');
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id', $st_id);
                } else {
                    $w->where('pos_transactions.st_id', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transactions.id'))
            ->editColumn('created_at', function($data) {
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->editColumn('item_total', function($data) {
                if (!empty($data->item_total_2)) {
                    return number_format($data->item_total_2);
                } else {
                    return number_format($data->item_total_1);
                }
            })
            ->editColumn('pos_admin_cost', function($data) {
                return number_format($data->pos_admin_cost);
            })
            ->editColumn('total', function($data) {
                if (!empty($data->item_total_2)) {
                    return number_format($data->item_total_2 - $data->pos_admin_cost);
                } else {
                    return number_format($data->item_total_1 - $data->pos_admin_cost);
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getProfits(Request $req)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('pos_transaction_details')
            ->selectRaw("ts_pos_transactions.created_at as created_at, pos_invoice, br_name, p_name, p_color, sz_name, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where(function ($w) use ($req) {
                $date = $req->get('date');
                $start = null;
                $end = null;
                $exp = explode('|', $date);
                $total = count($exp);
                if ($total > 1) {
                    if ($exp[0] != $exp[1]) {
                        $start = $exp[0];
                        $end = $exp[1];
                    } else {
                        $start = $exp[0];
                    }
                } else {
                    $start = $date;
                }
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                $st_id = session()->get('st_id');
                $division = $req->get('division');
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id', $st_id);
                } else {
                    $w->where('pos_transactions.st_id', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transaction_details.id'))
            ->editColumn('created_at', function($data) {
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->editColumn('pos_td_sell_price', function($data) {
                return number_format($data->pos_td_sell_price);
            })
            ->editColumn('total', function($data) {
                if (!empty($data->pos_td_marketplace_price)) {
                    $total = $data->pos_td_marketplace_price;
                } else {
                    $total = $data->pos_td_discount_price;
                }
                return number_format($total);
            })
            ->editColumn('purchase', function($data) {
                if (!empty ($data->purchase)) {
                    return number_format($data->purchase);
                } else {
                    if (!empty($data->poad_total_price)) {
                        return number_format(round($data->poad_total_price / $data->poad_qty));
                    } else {
                        if (!empty($data->ps_purchase_price)) {
                            return number_format($data->ps_purchase_price);
                        } else {
                            return number_format($data->p_purchase_price);
                        }
                    }
                }
            })
            ->editColumn('purchase_total', function($data) {
                $purchase = 0;
                if (!empty ($data->purchase)) {
                    $purchase = $data->purchase;
                } else {
                    if (!empty($data->poad_total_price)) {
                        $purchase = round($data->poad_total_price / $data->poad_qty);
                    } else {
                        if (!empty($data->ps_purchase_price)) {
                            $purchase = $data->ps_purchase_price;
                        } else {
                            $purchase = $data->p_purchase_price;
                        }
                    }
                }
                return number_format($purchase*$data->pos_td_qty);
            })
            ->editColumn('profit', function($data) {
                $total = 0;
                $purchase = 0;
                if (!empty($data->pos_td_marketplace_price)) {
                    $total = $data->pos_td_marketplace_price;
                } else {
                    $total = $data->pos_td_discount_price;
                }
                if (!empty ($data->purchase)) {
                    $purchase = round($data->purchase);
                } else {
                    if (!empty($data->poad_total_price)) {
                        $purchase = round($data->poad_total_price / $data->poad_qty);
                    } else {
                        if (!empty($data->ps_purchase_price)) {
                            $purchase = $data->ps_purchase_price;
                        } else {
                            $purchase = $data->p_purchase_price;
                        }
                    }
                }
                return number_format($total-($purchase*$data->pos_td_qty));
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getCrossNettSales(Request $req)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('pos_transactions')
            ->selectRaw("ts_pos_transactions.id as id, pos_invoice, sum(ts_pos_transaction_details.pos_td_qty) as item, sum(ts_pos_transaction_details.pos_td_discount_price) as item_total_1, sum(ts_pos_transaction_details.pos_td_marketplace_price) as item_total_2, ts_pos_transactions.created_at as created_at, pos_admin_cost")
            ->leftJoin('pos_transaction_details', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->where(function ($w) use ($req) {
                $date = $req->get('date');
                $start = null;
                $end = null;
                $exp = explode('|', $date);
                $total = count($exp);
                if ($total > 1) {
                    if ($exp[0] != $exp[1]) {
                        $start = $exp[0];
                        $end = $exp[1];
                    } else {
                        $start = $exp[0];
                    }
                } else {
                    $start = $date;
                }
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                $st_id = session()->get('st_id');
                $division = $req->get('division');
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id_ref', $st_id);
                } else {
                    $w->whereNotNull('pos_transactions.st_id_ref')
                    ->where('pos_transactions.st_id_ref', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transactions.id'))
            ->editColumn('created_at', function($data) {
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->editColumn('item_total', function($data) {
                if (!empty($data->item_total_2)) {
                    return number_format($data->item_total_2);
                } else {
                    return number_format($data->item_total_1);
                }
            })
            ->editColumn('pos_admin_cost', function($data) {
                return number_format($data->pos_admin_cost);
            })
            ->editColumn('total', function($data) {
                if (!empty($data->item_total_2)) {
                    return number_format($data->item_total_2 - $data->pos_admin_cost);
                } else {
                    return number_format($data->item_total_1 - $data->pos_admin_cost);
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getCrossProfits(Request $req)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('pos_transaction_details')
            ->selectRaw("ts_pos_transactions.created_at as created_at, pos_invoice, br_name, p_name, p_color, sz_name, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where(function ($w) use ($req) {
                $date = $req->get('date');
                $start = null;
                $end = null;
                $exp = explode('|', $date);
                $total = count($exp);
                if ($total > 1) {
                    if ($exp[0] != $exp[1]) {
                        $start = $exp[0];
                        $end = $exp[1];
                    } else {
                        $start = $exp[0];
                    }
                } else {
                    $start = $date;
                }
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                $st_id = session()->get('st_id');
                $division = $req->get('division');
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id_ref', $st_id);
                } else {
                    $w->whereNotNull('pos_transactions.st_id_ref')
                    ->where('pos_transactions.st_id_ref', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transaction_details.id'))
            ->editColumn('created_at', function($data) {
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->editColumn('pos_td_sell_price', function($data) {
                return number_format($data->pos_td_sell_price);
            })
            ->editColumn('total', function($data) {
                if (!empty($data->pos_td_marketplace_price)) {
                    $total = $data->pos_td_marketplace_price;
                } else {
                    $total = $data->pos_td_discount_price;
                }
                return number_format($total);
            })
            ->editColumn('purchase', function($data) {
                if (!empty ($data->purchase)) {
                    return number_format($data->purchase);
                } else {
                    if (!empty($data->poad_total_price)) {
                        return number_format(round($data->poad_total_price / $data->poad_qty));
                    } else {
                        if (!empty($data->ps_purchase_price)) {
                            return number_format($data->ps_purchase_price);
                        } else {
                            return number_format($data->p_purchase_price);
                        }
                    }
                }
            })
            ->editColumn('purchase_total', function($data) {
                $purchase = 0;
                if (!empty ($data->purchase)) {
                    $purchase = $data->purchase;
                } else {
                    if (!empty($data->poad_total_price)) {
                        $purchase = round($data->poad_total_price / $data->poad_qty);
                    } else {
                        if (!empty($data->ps_purchase_price)) {
                            $purchase = $data->ps_purchase_price;
                        } else {
                            $purchase = $data->p_purchase_price;
                        }
                    }
                }
                return number_format($purchase*$data->pos_td_qty);
            })
            ->editColumn('profit', function($data) {
                $total = 0;
                $purchase = 0;
                if (!empty($data->pos_td_marketplace_price)) {
                    $total = $data->pos_td_marketplace_price;
                } else {
                    $total = $data->pos_td_discount_price;
                }
                if (!empty ($data->purchase)) {
                    $purchase = round($data->purchase);
                } else {
                    if (!empty($data->poad_total_price)) {
                        $purchase = round($data->poad_total_price / $data->poad_qty);
                    } else {
                        if (!empty($data->ps_purchase_price)) {
                            $purchase = $data->ps_purchase_price;
                        } else {
                            $purchase = $data->p_purchase_price;
                        }
                    }
                }
                return number_format($total-($purchase*$data->pos_td_qty));
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getPurchases(Request $req)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('purchase_order_article_detail_statuses')
            ->selectRaw("ts_purchase_order_article_detail_statuses.created_at as created_at, po_invoice, br_name, p_name, p_color, sz_name, sum(ts_purchase_order_article_detail_statuses.poads_qty) as qty, sum(ts_purchase_order_article_detail_statuses.poads_total_price) as total, poads_purchase_price")
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
            ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where(function($w) use ($req) {
                $date = $req->get('date');
                $start = null;
                $end = null;
                $exp = explode('|', $date);
                $total = count($exp);
                if ($total > 1) {
                    if ($exp[0] != $exp[1]) {
                        $start = $exp[0];
                        $end = $exp[1];
                    } else {
                        $start = $exp[0];
                    }
                } else {
                    $start = $date;
                }
                $st_id = session()->get('st_id');
                if (!empty($st_id)) {
                    $w->whereIn('purchase_orders.st_id', $st_id);
                } else {
                    $w->where('purchase_orders.st_id', '!=', '4');
                }
                if (!empty($end)) {
                    $w->whereDate('purchase_order_article_detail_statuses.created_at', '>=', $start)
                    ->whereDate('purchase_order_article_detail_statuses.created_at', '<=', $end);
                } else {
                    $w->whereDate('purchase_order_article_detail_statuses.created_at', '=', $start);
                }
            })
            ->groupBy('purchase_order_article_details.pst_id'))
            ->editColumn('created_at', function($data) {
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getCCAssets(Request $req)
    {
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        if(request()->ajax()) {
            return datatables()->of(DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, p_name, p_color, sz_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereNotIn('pl_code', $exception)
            ->where(function($w) use ($req) {
                $st_id = session()->get('st_id');
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->whereIn('stkt_id', ['1', '3'])
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id'))
            ->editColumn('purchase', function($data) {
                if (!empty($data->purchase)) {
                    return number_format($data->purchase);
                } else if (!empty($data->ps_purchase_price)) {
                    return number_format($data->ps_purchase_price);
                } else {
                    return number_format($data->p_purchase_price);
                }
            })
            ->editColumn('total', function($data) {
                if (!empty($data->purchase)) {
                    return number_format($data->qty * $data->purchase);
                } else if (!empty($data->ps_purchase_price)) {
                    return number_format($data->qty * $data->ps_purchase_price);
                } else {
                    return number_format($data->qty * $data->p_purchase_price);
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getCAssets(Request $req)
    {
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        if(request()->ajax()) {
            return datatables()->of(DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, p_name, p_color, sz_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereNotIn('pl_code', $exception)
            ->where(function($w) use ($req) {
                $st_id = session()->get('st_id');
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->whereIn('stkt_id', ['2']) //Stkt_id 2 adalah CONSIGNMENT
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id'))
            ->editColumn('purchase', function($data) {
                if (!empty($data->purchase)) {
                    return number_format($data->purchase);
                } else if (!empty($data->ps_purchase_price)) {
                    return number_format($data->ps_purchase_price);
                } else {
                    return number_format($data->p_purchase_price);
                }
            })
            ->editColumn('total', function($data) {
                if (!empty($data->purchase)) {
                    return number_format($data->qty * $data->purchase);
                } else if (!empty($data->ps_purchase_price)) {
                    return number_format($data->qty * $data->ps_purchase_price);
                } else {
                    return number_format($data->qty * $data->p_purchase_price);
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getEXCCCAssets(Request $req)
    {
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        if(request()->ajax()) {
            return datatables()->of(DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, p_name, p_color, sz_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereIn('pl_code', $exception)
            ->where(function($w) use ($req) {
                $st_id = session()->get('st_id');
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->whereIn('stkt_id', ['1', '3'])
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id'))
            ->editColumn('purchase', function($data) {
                if (!empty($data->purchase)) {
                    return number_format($data->purchase);
                } else if (!empty($data->ps_purchase_price)) {
                    return number_format($data->ps_purchase_price);
                } else {
                    return number_format($data->p_purchase_price);
                }
            })
            ->editColumn('total', function($data) {
                if (!empty($data->purchase)) {
                    return number_format($data->qty * $data->purchase);
                } else if (!empty($data->ps_purchase_price)) {
                    return number_format($data->qty * $data->ps_purchase_price);
                } else {
                    return number_format($data->qty * $data->p_purchase_price);
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getEXCCAssets(Request $req)
    {
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        if(request()->ajax()) {
            return datatables()->of(DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, p_name, p_color, sz_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereIn('pl_code', $exception)
            ->where(function($w) use ($req) {
                $st_id = session()->get('st_id');
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->whereIn('stkt_id', ['2'])
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id'))
            ->editColumn('purchase', function($data) {
                if (!empty($data->purchase)) {
                    return number_format($data->purchase);
                } else if (!empty($data->ps_purchase_price)) {
                    return number_format($data->ps_purchase_price);
                } else {
                    return number_format($data->p_purchase_price);
                }
            })
            ->editColumn('total', function($data) {
                if (!empty($data->purchase)) {
                    return number_format($data->qty * $data->purchase);
                } else if (!empty($data->ps_purchase_price)) {
                    return number_format($data->qty * $data->ps_purchase_price);
                } else {
                    return number_format($data->qty * $data->p_purchase_price);
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function exportTable(Request $req)
    {
        $date = $req->post('date');
        $exp = explode('|', $date);
        $start = null;
        $end = null;
        if (!empty($exp[1])) {
            $start = $exp[0];
            $end = $exp[1];
        } else {
            $start = $req->post('date');
        }
        $label = $req->post('label');
        $store = $req->post('store');
        $division = $req->post('division');
        return Excel::download(new ExportData($start, $end, $label, $store, $division), 'ExportResults.xlsx');
    }

    public function loadAdminCost(Request $req)
    {
        $date = $req->post('date');
        $st_id = $req->post('store');
        $label = $req->post('label');
        $division = $req->post('division');
        $exp = explode('|', $date);
        $start = null;
        $end = null;
        if (!empty($exp[1])) {
            $start = $exp[0];
            $end = $exp[1];
        } else {
            $start = $req->post('date');
        }

        $admin_cost = DB::table('pos_transactions')->select('pos_admin_cost', 'pos_status')
        ->where(function($w) use ($start, $end, $st_id, $label, $division) {
            if ($label == 'sales' || $label == 'profits') {
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id', $st_id);
                } else {
                    $w->where('pos_transactions.st_id', '!=', '4');
                }
            } else {
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id_ref', $st_id);
                } else {
                    $w->whereNotNull('pos_transactions.st_id_ref')
                    ->where('pos_transactions.st_id_ref', '!=', '4');
                }
            }
            if ($division != 'all') {
                if ($division == 'online') {
                    $w->where('pos_transactions.stt_id', '=', '1');
                } else {
                    $w->where('pos_transactions.stt_id', '=', '2');
                }
            }
            if (!empty($end)) {
                $w->whereDate('pos_transactions.created_at', '>=', $start)
                ->whereDate('pos_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('pos_transactions.created_at', '=', $start);
            }
        })
        ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
        ->sum('pos_admin_cost');

        $r['status'] = '200';
        $r['admin_cost'] = number_format($admin_cost);
        return json_encode($r);
    }

    public function loadGraph(Request $req)
    {
        $label = $req->post('label');
        $date = $req->post('date');
        $st_id = $req->post('store');
        $division = $req->post('division');

        $exp = explode('|', $date);
        $start = null;
        $end = null;
        if (!empty($exp[1])) {
            $start = $exp[0];
            $end = $exp[1];
        } else {
            $start = $req->post('date');
        }

        if ($label == 'sales') {
            $nett_sales = DB::table('pos_transaction_details')
            ->selectRaw("ts_pos_transactions.created_at as created_at, pos_invoice, br_name, pos_td_nameset_price, pos_td_marketplace_price, pos_td_discount_price")
            ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where(function ($w) use ($start, $end, $st_id, $division) {
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id', $st_id);
                } else {
                    $w->where('pos_transactions.st_id', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transaction_details.id')
            ->orderBy('br_name')
            ->get();
            $ns = array();
            if (!empty($nett_sales->first())) {
                foreach ($nett_sales as $row) {
                    $total = 0;
                    if (!empty($row->pos_td_marketplace_price)) {
                        $total = $row->pos_td_marketplace_price;
                    } else {
                        $total = $row->pos_td_discount_price;
                    }

                    $key = $row->br_name;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'br_name' => $row->br_name,
                            'sales' => $total,
                        );
                    } else {
                        $ns[$key]['sales'] = $ns[$key]['sales'] + $total;
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $ns
            ];
            return view('app.updated_dashboard._load_graph', compact('data'));
        }

        if ($label == 'profits') {
            $pf = array();
            $profit = DB::table('pos_transaction_details')
            ->selectRaw("ts_pos_transactions.created_at as created_at, br_name, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where(function ($w) use ($start, $end, $st_id, $division) {
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id', $st_id);
                } else {
                    $w->where('pos_transactions.st_id', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transaction_details.id')
            ->orderBy('br_name')
            ->get();
            if (!empty($profit->first())) {
                foreach ($profit as $row) {
                    $created_at = date('d/m/Y H:i:s', strtotime($row->created_at));
                    $total = 0;
                    if (!empty($row->pos_td_marketplace_price)) {
                        $total = $row->pos_td_marketplace_price;
                    } else {
                        $total = $row->pos_td_discount_price;
                    }
                    $purchase = 0;
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->poad_total_price)) {
                            $purchase = round($row->poad_total_price / $row->poad_qty);
                        } else {
                            if (!empty($row->ps_purchase_price)) {
                                $purchase = $row->ps_purchase_price;
                            } else {
                                $purchase = $row->p_purchase_price;
                            }
                        }
                    }
                    $key = $row->br_name;
                    if (!array_key_exists($key, $pf)) {
                        $pf[$key] = array(
                            'br_name' => $row->br_name,
                            'profits' => $total-($row->pos_td_qty*$purchase),
                        );
                    } else {
                        $pf[$key]['profits'] = $pf[$key]['profits'] + ($total-($row->pos_td_qty*$purchase));
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $pf
            ];
            return view('app.updated_dashboard._load_graph', compact('data'));
        }

        if ($label == 'cross_sales') {
            $cross_nett_sales = DB::table('pos_transaction_details')
            ->selectRaw("ts_pos_transactions.created_at as created_at, pos_invoice, br_name, pos_td_nameset_price, pos_td_marketplace_price, pos_td_discount_price")
            ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where(function ($w) use ($start, $end, $st_id, $division) {
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id_ref', $st_id);
                } else {
                    $w->whereNotNull('pos_transactions.st_id_ref')
                    ->where('pos_transactions.st_id_ref', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transaction_details.id')
            ->orderBy('br_name')
            ->get();
            $ns = array();
            if (!empty($cross_nett_sales->first())) {
                foreach ($cross_nett_sales as $row) {
                    $total = 0;
                    if (!empty($row->pos_td_marketplace_price)) {
                        $total = $row->pos_td_marketplace_price;
                    } else {
                        $total = $row->pos_td_discount_price;
                    }

                    $key = $row->br_name;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'br_name' => $row->br_name,
                            'csales' => $total,
                        );
                    } else {
                        $ns[$key]['csales'] = $ns[$key]['csales'] + $total;
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $ns
            ];
            return view('app.updated_dashboard._load_graph', compact('data'));
        }

        if ($label == 'cross_profits') {
            $pf = array();
            $cross_profit = DB::table('pos_transaction_details')
            ->selectRaw("ts_pos_transactions.created_at as created_at, br_name, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where(function ($w) use ($start, $end, $st_id, $division) {
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id_ref', $st_id);
                } else {
                    $w->whereNotNull('pos_transactions.st_id_ref')
                    ->where('pos_transactions.st_id_ref', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transaction_details.id')
            ->orderBy('br_name')
            ->get();
            if (!empty($cross_profit->first())) {
                foreach ($cross_profit as $row) {
                    $created_at = date('d/m/Y H:i:s', strtotime($row->created_at));
                    $total = 0;
                    if (!empty($row->pos_td_marketplace_price)) {
                        $total = $row->pos_td_marketplace_price;
                    } else {
                        $total = $row->pos_td_discount_price;
                    }
                    $purchase = 0;
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->poad_total_price)) {
                            $purchase = round($row->poad_total_price / $row->poad_qty);
                        } else {
                            if (!empty($row->ps_purchase_price)) {
                                $purchase = $row->ps_purchase_price;
                            } else {
                                $purchase = $row->p_purchase_price;
                            }
                        }
                    }
                    $key = $row->br_name;
                    if (!array_key_exists($key, $pf)) {
                        $pf[$key] = array(
                            'br_name' => $row->br_name,
                            'profits' => $total-($row->pos_td_qty*$purchase),
                        );
                    } else {
                        $pf[$key]['profits'] = $pf[$key]['profits'] + ($total-($row->pos_td_qty*$purchase));
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $pf
            ];
            return view('app.updated_dashboard._load_graph', compact('data'));
        }

        if ($label == 'purchases') {
            $pr = array();
            $purchases = DB::table('purchase_order_article_detail_statuses')
            ->selectRaw("ts_purchase_order_article_detail_statuses.created_at as created_at, br_name, sum(ts_purchase_order_article_detail_statuses.poads_total_price) as total")
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
            ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where(function($w) use ($start, $end, $st_id) {
                if (!empty($end)) {
                    $w->whereDate('purchase_order_article_detail_statuses.created_at', '>=', $start)
                    ->whereDate('purchase_order_article_detail_statuses.created_at', '<=', $end);
                } else {
                    $w->whereDate('purchase_order_article_detail_statuses.created_at', '=', $start);
                }
                if (!empty($st_id)) {
                    $w->whereIn('purchase_orders.st_id', $st_id);
                } else {
                    $w->where('purchase_orders.st_id', '!=', '4');
                }
            })
            ->groupBy('purchase_order_article_details.pst_id')
            ->orderBy('br_name')
            ->get();
            if (!empty($purchases->first())) {
                foreach ($purchases as $row) {
                    $key = $row->br_name;
                    if (!array_key_exists($key, $pr)) {
                        $pr[$key] = array(
                            'br_name' => $row->br_name,
                            'purchases' => $row->total,
                        );
                    } else {
                        $pr[$key]['purchases'] = $pr[$key]['purchases'] + $row->total;
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $pr
            ];
            return view('app.updated_dashboard._load_graph', compact('data'));
        }

        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        if ($label == 'cc_assets') {
            $cc = array();
            $cc_assets = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereNotIn('pl_code', $exception)
            ->where(function ($w) use ($st_id) {
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->whereIn('stkt_id', ['1', '3'])
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id')
            ->orderBy('br_name')
            ->get();
            if (!empty($cc_assets->first())) {
                foreach ($cc_assets as $row) {
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                    $key = $row->br_name;
                    if (!array_key_exists($key, $cc)) {
                        $cc[$key] = array(
                            'br_name' => $row->br_name,
                            'cc_assets' => ($row->qty*$purchase),
                        );
                    } else {
                        $cc[$key]['cc_assets'] = $cc[$key]['cc_assets'] + ($row->qty*$purchase);
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $cc
            ];
            return view('app.updated_dashboard._load_graph', compact('data'));
        }

        if ($label == 'c_assets') {
            $c = array();
            $c_assets = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereNotIn('pl_code', $exception)
            ->where(function ($w) use ($st_id) {
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->where('stkt_id', '=', '2')
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id')
            ->orderBy('br_name')
            ->get();
            if (!empty($c_assets->first())) {
                foreach ($c_assets as $row) {
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                    $key = $row->br_name;
                    if (!array_key_exists($key, $c)) {
                        $c[$key] = array(
                            'br_name' => $row->br_name,
                            'c_assets' => ($row->qty*$purchase),
                        );
                    } else {
                        $c[$key]['c_assets'] = $c[$key]['c_assets'] + ($row->qty*$purchase);
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $c
            ];
            return view('app.updated_dashboard._load_graph', compact('data'));
        }

        if ($label == 'cc_exc_assets') {
            $exc_cc = array();
            $exc_cc_assets = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereIn('pl_code', $exception)
            ->where(function ($w) use ($st_id) {
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->whereIn('stkt_id', ['1', '3'])
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id')
            ->orderBy('br_name')
            ->get();
            if (!empty($exc_cc_assets->first())) {
                foreach ($exc_cc_assets as $row) {
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                    $key = $row->br_name;
                    if (!array_key_exists($key, $exc_cc)) {
                        $exc_cc[$key] = array(
                            'br_name' => $row->br_name,
                            'cc_assets' => ($row->qty*$purchase),
                        );
                    } else {
                        $exc_cc[$key]['cc_assets'] = $exc_cc[$key]['cc_assets'] + ($row->qty*$purchase);
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $exc_cc
            ];
            return view('app.updated_dashboard._load_graph', compact('data'));
        }

        if ($label == 'c_exc_assets') {
            $exc_c = array();
            $exc_c_assets = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, br_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereIn('pl_code', $exception)
            ->where(function ($w) use ($st_id) {
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->where('stkt_id', '=', '2')
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id')
            ->orderBy('br_name')
            ->get();
            if (!empty($exc_c_assets->first())) {
                foreach ($exc_c_assets as $row) {
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                    $key = $row->br_name;
                    if (!array_key_exists($key, $exc_c)) {
                        $exc_c[$key] = array(
                            'br_name' => $row->br_name,
                            'c_assets' => ($row->qty*$purchase),
                        );
                    } else {
                        $exc_c[$key]['c_assets'] = $exc_c[$key]['c_assets'] + ($row->qty*$purchase);
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $exc_c
            ];
            return view('app.updated_dashboard._load_graph', compact('data'));
        }

        if ($label == 'debts') {
            $debts_dt = array();
            $brands = DB::table('brands')->select('id', 'br_name')->where('br_delete', '!=', '1')->get();
            if (!empty($brands->first())) {
                foreach ($brands as $row) {
                    $debt_list = DB::table('debt_lists')->select('dl_total', 'st_id', 'br_id')
                    ->where('debt_lists.dl_delete', '!=', '1')
                    ->where('br_id', '=', $row->id)
                    ->where(function($w) use ($st_id) {
                        if (!empty($st_id)) {
                            $w->whereIn('st_id', $st_id);
                        }
                    })->sum('dl_total');
                    $debt_list_payment = DB::table('debt_list_payments')->select('dlp_value', 'st_id', 'br_id')
                    ->leftJoin('debt_lists', 'debt_lists.id', '=', 'debt_list_payments.dl_id')
                    ->where('br_id', '=', $row->id)
                    ->where(function($w) use ($st_id) {
                        if (!empty($st_id)) {
                            $w->whereIn('st_id', $st_id);
                        }
                    })->where('debt_lists.dl_delete', '!=', '1')->sum('dlp_value');
                    $debts = $debt_list - $debt_list_payment;

                    if ($debts > 0) {
                        $key = $row->br_name;
                        if (!array_key_exists($key, $debts_dt)) {
                            $debts_dt[$key] = array(
                                'br_name' => $row->br_name,
                                'debts' => $debts,
                            );
                        } else {
                            $debts_dt[$key]['debts'] = $debts_dt[$key]['debts'] + $debts;
                        }
                    }
                }
            }

            $data = [
                'label' => $label,
                'item' => $debts_dt
            ];
            return view('app.updated_dashboard._load_graph', compact('data'));
        }
    }

    public function loadStore(Request $req)
    {
        $label = $req->post('label');
        $date = $req->post('date');
        $st_id = $req->post('store');
        $division = $req->post('division');

        $exp = explode('|', $date);
        $start = null;
        $end = null;
        if (!empty($exp[1])) {
            $start = $exp[0];
            $end = $exp[1];
        } else {
            $start = $req->post('date');
        }

        if ($label == 'sales') {
            $nett_sales = DB::table('pos_transaction_details')
            ->selectRaw("ts_pos_transactions.created_at as created_at, pos_invoice, st_name, pos_td_nameset_price, pos_td_marketplace_price, pos_td_discount_price")
            ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
            ->where(function ($w) use ($start, $end, $st_id, $division) {
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id', $st_id);
                } else {
                    $w->where('pos_transactions.st_id', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transaction_details.id')
            ->orderBy('st_name')
            ->get();
            $ns = array();
            if (!empty($nett_sales->first())) {
                foreach ($nett_sales as $row) {
                    $total = 0;
                    if (!empty($row->pos_td_marketplace_price)) {
                        $total = $row->pos_td_marketplace_price;
                    } else {
                        $total = $row->pos_td_discount_price;
                    }
                    $key = $row->st_name;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'st_name' => $row->st_name,
                            'sales' => $total,
                        );
                    } else {
                        $ns[$key]['sales'] = $ns[$key]['sales'] + $total;
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $ns
            ];
            return view('app.updated_dashboard._load_store', compact('data'));
        }

        if ($label == 'profits') {
            $pf = array();
            $profit = DB::table('pos_transaction_details')
            ->selectRaw("ts_pos_transactions.created_at as created_at, st_name, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
            ->where(function ($w) use ($start, $end, $st_id, $division) {
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id', $st_id);
                } else {
                    $w->where('pos_transactions.st_id', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transaction_details.id')
            ->orderBy('st_name')
            ->get();
            if (!empty($profit->first())) {
                foreach ($profit as $row) {
                    $created_at = date('d/m/Y H:i:s', strtotime($row->created_at));
                    $total = 0;
                    if (!empty($row->pos_td_marketplace_price)) {
                        $total = $row->pos_td_marketplace_price;
                    } else {
                        $total = $row->pos_td_discount_price;
                    }
                    $purchase = 0;
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->poad_total_price)) {
                            $purchase = round($row->poad_total_price / $row->poad_qty);
                        } else {
                            if (!empty($row->ps_purchase_price)) {
                                $purchase = $row->ps_purchase_price;
                            } else {
                                $purchase = $row->p_purchase_price;
                            }
                        }
                    }
                    $key = $row->st_name;
                    if (!array_key_exists($key, $pf)) {
                        $pf[$key] = array(
                            'st_name' => $row->st_name,
                            'profits' => $total-($row->pos_td_qty*$purchase),
                        );
                    } else {
                        $pf[$key]['profits'] = $pf[$key]['profits'] + ($total-($row->pos_td_qty*$purchase));
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $pf
            ];
            return view('app.updated_dashboard._load_store', compact('data'));
        }

        if ($label == 'cross_sales') {
            $cross_nett_sales = DB::table('pos_transaction_details')
            ->selectRaw("ts_pos_transactions.created_at as created_at, pos_invoice, st_name, pos_td_nameset_price, pos_td_marketplace_price, pos_td_discount_price")
            ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
            ->where(function ($w) use ($start, $end, $st_id, $division) {
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id_ref', $st_id);
                } else {
                    $w->whereNotNull('pos_transactions.st_id_ref')
                    ->where('pos_transactions.st_id_ref', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transaction_details.id')
            ->orderBy('st_name')
            ->get();
            $ns = array();
            if (!empty($cross_nett_sales->first())) {
                foreach ($cross_nett_sales as $row) {
                    $total = 0;
                    if (!empty($row->pos_td_marketplace_price)) {
                        $total = $row->pos_td_marketplace_price;
                    } else {
                        $total = $row->pos_td_discount_price;
                    }
                    $key = $row->st_name;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'st_name' => $row->st_name,
                            'csales' => $total,
                        );
                    } else {
                        $ns[$key]['csales'] = $ns[$key]['csales'] + $total;
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $ns
            ];
            return view('app.updated_dashboard._load_store', compact('data'));
        }

        if ($label == 'cross_profits') {
            $pf = array();
            $cross_profit = DB::table('pos_transaction_details')
            ->selectRaw("ts_pos_transactions.created_at as created_at, st_name, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('stores', 'stores.id', '=', 'pos_transactions.st_id')
            ->where(function ($w) use ($start, $end, $st_id, $division) {
                if (!empty($end)) {
                    $w->whereDate('pos_transactions.created_at', '>=', $start)
                    ->whereDate('pos_transactions.created_at', '<=', $end);
                } else {
                    $w->whereDate('pos_transactions.created_at', '=', $start);
                }
                if (!empty($st_id)) {
                    $w->whereIn('pos_transactions.st_id_ref', $st_id);
                } else {
                    $w->whereNotNull('pos_transactions.st_id_ref')
                    ->where('pos_transactions.st_id_ref', '!=', '4');
                }
                if ($division != 'all') {
                    if ($division == 'online') {
                        $w->where('pos_transactions.stt_id', '=', '1');
                    } else {
                        $w->where('pos_transactions.stt_id', '=', '2');
                    }
                }
                $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID']);
            })
            ->groupBy('pos_transaction_details.id')
            ->orderBy('st_name')
            ->get();
            if (!empty($cross_profit->first())) {
                foreach ($cross_profit as $row) {
                    $created_at = date('d/m/Y H:i:s', strtotime($row->created_at));
                    $total = 0;
                    if (!empty($row->pos_td_marketplace_price)) {
                        $total = $row->pos_td_marketplace_price;
                    } else {
                        $total = $row->pos_td_discount_price;
                    }
                    $purchase = 0;
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->poad_total_price)) {
                            $purchase = round($row->poad_total_price / $row->poad_qty);
                        } else {
                            if (!empty($row->ps_purchase_price)) {
                                $purchase = $row->ps_purchase_price;
                            } else {
                                $purchase = $row->p_purchase_price;
                            }
                        }
                    }
                    $key = $row->st_name;
                    if (!array_key_exists($key, $pf)) {
                        $pf[$key] = array(
                            'st_name' => $row->st_name,
                            'profits' => $total-($row->pos_td_qty*$purchase),
                        );
                    } else {
                        $pf[$key]['profits'] = $pf[$key]['profits'] + ($total-($row->pos_td_qty*$purchase));
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $pf
            ];
            return view('app.updated_dashboard._load_store', compact('data'));
        }

        if ($label == 'purchases') {
            $pr = array();
            $purchases = DB::table('purchase_order_article_detail_statuses')
            ->selectRaw("ts_purchase_order_article_detail_statuses.created_at as created_at, st_name, sum(ts_purchase_order_article_detail_statuses.poads_total_price) as total")
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
            ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
            ->leftJoin('stores', 'stores.id', '=', 'purchase_orders.st_id')
            ->where(function($w) use ($start, $end, $st_id) {
                if (!empty($end)) {
                    $w->whereDate('purchase_order_article_detail_statuses.created_at', '>=', $start)
                    ->whereDate('purchase_order_article_detail_statuses.created_at', '<=', $end);
                } else {
                    $w->whereDate('purchase_order_article_detail_statuses.created_at', '=', $start);
                }
                if (!empty($st_id)) {
                    $w->whereIn('purchase_orders.st_id', $st_id);
                } else {
                    $w->where('purchase_orders.st_id', '!=', '4');
                }
            })
            ->groupBy('purchase_order_article_details.pst_id')
            ->orderBy('st_name')
            ->get();
            if (!empty($purchases->first())) {
                foreach ($purchases as $row) {
                    $key = $row->st_name;
                    if (!array_key_exists($key, $pr)) {
                        $pr[$key] = array(
                            'st_name' => $row->st_name,
                            'purchases' => $row->total,
                        );
                    } else {
                        $pr[$key]['purchases'] = $pr[$key]['purchases'] + $row->total;
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $pr
            ];
            return view('app.updated_dashboard._load_store', compact('data'));
        }

        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        if ($label == 'cc_assets') {
            $cc = array();
            $cc_assets = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, st_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->whereNotIn('pl_code', $exception)
            ->where(function ($w) use ($st_id) {
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->whereIn('stkt_id', ['1', '3'])
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id')
            ->orderBy('st_name')
            ->get();
            if (!empty($cc_assets->first())) {
                foreach ($cc_assets as $row) {
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                    $key = $row->st_name;
                    if (!array_key_exists($key, $cc)) {
                        $cc[$key] = array(
                            'st_name' => $row->st_name,
                            'cc_assets' => ($row->qty*$purchase),
                        );
                    } else {
                        $cc[$key]['cc_assets'] = $cc[$key]['cc_assets'] + ($row->qty*$purchase);
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $cc
            ];
            return view('app.updated_dashboard._load_store', compact('data'));
        }

        if ($label == 'c_assets') {
            $c = array();
            $c_assets = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, st_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereNotIn('pl_code', $exception)
            ->where(function ($w) use ($st_id) {
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->where('stkt_id', '=', '2')
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id')
            ->orderBy('st_name')
            ->get();
            if (!empty($c_assets->first())) {
                foreach ($c_assets as $row) {
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                    $key = $row->st_name;
                    if (!array_key_exists($key, $c)) {
                        $c[$key] = array(
                            'st_name' => $row->st_name,
                            'c_assets' => ($row->qty*$purchase),
                        );
                    } else {
                        $c[$key]['c_assets'] = $c[$key]['c_assets'] + ($row->qty*$purchase);
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $c
            ];
            return view('app.updated_dashboard._load_store', compact('data'));
        }

        if ($label == 'cc_exc_assets') {
            $exc_cc = array();
            $exc_cc_assets = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, st_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereIn('pl_code', $exception)
            ->where(function ($w) use ($st_id) {
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->whereIn('stkt_id', ['1', '3'])
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id')
            ->orderBy('st_name')
            ->get();
            if (!empty($exc_cc_assets->first())) {
                foreach ($exc_cc_assets as $row) {
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                    $key = $row->st_name;
                    if (!array_key_exists($key, $exc_cc)) {
                        $exc_cc[$key] = array(
                            'st_name' => $row->st_name,
                            'cc_assets' => ($row->qty*$purchase),
                        );
                    } else {
                        $exc_cc[$key]['cc_assets'] = $exc_cc[$key]['cc_assets'] + ($row->qty*$purchase);
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $exc_cc
            ];
            return view('app.updated_dashboard._load_store', compact('data'));
        }

        if ($label == 'c_exc_assets') {
            $exc_c = array();
            $exc_c_assets = DB::table('product_location_setups')
            ->selectRaw("ts_product_location_setups.pls_qty as qty, st_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->whereIn('pl_code', $exception)
            ->where(function ($w) use ($st_id) {
                if (!empty($st_id)) {
                    $w->whereIn('product_locations.st_id', $st_id);
                } else {
                    $w->where('product_locations.st_id', '!=', '4');
                }
            })
            ->where('stkt_id', '=', '2')
            ->where('product_location_setups.pls_qty', '>', '0')
            ->groupBy('product_location_setups.id')
            ->orderBy('st_name')
            ->get();
            if (!empty($exc_c_assets->first())) {
                foreach ($exc_c_assets as $row) {
                    if (!empty ($row->purchase)) {
                        $purchase = round($row->purchase);
                    } else {
                        if (!empty($row->ps_purchase_price)) {
                            $purchase = $row->ps_purchase_price;
                        } else {
                            $purchase = $row->p_purchase_price;
                        }
                    }
                    $key = $row->st_name;
                    if (!array_key_exists($key, $exc_c)) {
                        $exc_c[$key] = array(
                            'st_name' => $row->st_name,
                            'c_assets' => ($row->qty*$purchase),
                        );
                    } else {
                        $exc_c[$key]['c_assets'] = $exc_c[$key]['c_assets'] + ($row->qty*$purchase);
                    }
                }
            }
            $data = [
                'label' => $label,
                'item' => $exc_c
            ];
            return view('app.updated_dashboard._load_store', compact('data'));
        }

        if ($label == 'debts') {
            $debts_dt = array();
            $store = DB::table('stores')->select('id', 'st_name')->where('st_delete', '!=', '1')->get();
            if (!empty($store->first())) {
                foreach ($store as $row) {
                    $debt_list = DB::table('debt_lists')->select('dl_total', 'st_id')
                    ->where('debt_lists.dl_delete', '!=', '1')
                    ->where('st_id', '=', $row->id)->sum('dl_total');
                    $debt_list_payment = DB::table('debt_list_payments')->select('dlp_value', 'st_id')
                    ->leftJoin('debt_lists', 'debt_lists.id', '=', 'debt_list_payments.dl_id')
                    ->where('st_id', '=', $row->id)
                    ->where('debt_lists.dl_delete', '!=', '1')->sum('dlp_value');
                    $debts = $debt_list - $debt_list_payment;

                    if ($debts > 0) {
                        $key = $row->st_name;
                        if (!array_key_exists($key, $debts_dt)) {
                            $debts_dt[$key] = array(
                                'st_name' => $row->st_name,
                                'debts' => $debts,
                            );
                        } else {
                            $debts_dt[$key]['debts'] = $debts_dt[$key]['debts'] + $debts;
                        }
                    }
                }
            }

            $data = [
                'label' => $label,
                'item' => $debts_dt
            ];
            return view('app.updated_dashboard._load_store', compact('data'));
        }
    }
}
