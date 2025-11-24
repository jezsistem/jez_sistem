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

class DashboardController extends Controller
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

        //        dd(Auth::user()->st_id);
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
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'pc_id' => ProductCategory::where('pc_delete', '!=', '1')->orderByDesc('id')->pluck('pc_name', 'id'),
            'segment' => request()->segment(1)
        ];
        return view('app.dashboard.dashboard', compact('data'));
    }

    public function getSalesGraph(Request $request)
    {
        $date = $request->post('_range');

        // --- DATE RANGE PARSING
        $start = null;
        $end = null;

        if (!empty($date)) {
            $exp = explode('|', $date);
            if (count($exp) > 1) {
                $start = $exp[0] . " 00:00:00";
                $end   = $exp[1] . " 23:59:59";
            } else {
                $start = $date . " 00:00:00";
                $end   = $date . " 23:59:59";
            }
        }

        // --- QUERY LANGSUNG GROUP BY STORE
        $sql = "
        SELECT
            t.st_name,
            SUM(
                CASE
                    WHEN t.created_at < '2025-10-04 17:30:00' THEN
                        t.Net_sales + t.nameset_price
                        + CASE WHEN t.Net_sales < 0 THEN COALESCE(-online.discount,0)
                               ELSE COALESCE(online.discount,0)
                          END
                    WHEN t.Net_sales < 0 THEN
                        t.Net_sales + t.nameset_price + COALESCE(-online.discount,0)
                    ELSE
                        t.Net_sales + t.nameset_price
                END
            ) AS net_sales_adj
        FROM (
            SELECT
                ts_pos_transactions.pos_invoice,
                SUM(
                    CASE
                        WHEN ts_pos_transactions.pos_invoice NOT LIKE 'INV%' THEN
                            ROUND(ts_pos_transaction_details.pos_td_sell_price, 1)
                        WHEN ts_pos_transaction_details.pos_td_discount_number > 0
                            AND (
                                CASE
                                    WHEN ts_products.article_id = 'CUS01' THEN 0
                                    ELSE ROUND(
                                        (ts_product_stocks.ps_price_tag * ts_pos_transaction_details.pos_td_qty)
                                        - ts_pos_transaction_details.pos_td_sell_price, 1
                                    ) + COALESCE(ts_pos_transaction_details.pos_td_nameset_price,0)
                                END
                            ) = 0
                        THEN
                            ROUND(ts_pos_transaction_details.pos_td_sell_price,1)
                            - ts_pos_transaction_details.pos_td_discount_number
                        ELSE
                            ROUND(ts_pos_transaction_details.pos_td_sell_price,1)
                    END
                ) AS Net_sales,
                COALESCE(SUM(ts_pos_transaction_details.pos_td_nameset_price),0) AS nameset_price,
                ts_stores.st_name,
                ts_pos_transactions.created_at
            FROM ts_pos_transaction_details
            LEFT JOIN ts_pos_transactions ON ts_pos_transactions.id = ts_pos_transaction_details.pt_id
            LEFT JOIN ts_product_stocks   ON ts_product_stocks.id = ts_pos_transaction_details.pst_id
            LEFT JOIN ts_products         ON ts_products.id = ts_product_stocks.p_id
            LEFT JOIN ts_stores           ON ts_pos_transactions.st_id = ts_stores.id
            WHERE ts_pos_transactions.pos_status IN
                ('DONE','CANCEL','NAMESET','REFUND','SHIPPED','TELAH DIKIRIM','TO SHIP')
                AND ts_pos_transactions.st_id IN (2,3,5,8,20,30,40,53)
                AND ts_pos_transactions.created_at BETWEEN ? AND ?
            GROUP BY ts_pos_transactions.pos_invoice, ts_stores.st_name, ts_pos_transactions.created_at
        ) t
        LEFT JOIN (
            SELECT
                ts_online_transactions.order_number,
                ts_online_transaction_details.discount_platform AS discount
            FROM ts_online_transactions
            LEFT JOIN ts_online_transaction_details
                ON ts_online_transaction_details.to_id = ts_online_transactions.id
            GROUP BY ts_online_transactions.order_number
        ) online ON online.order_number = t.pos_invoice
        GROUP BY t.st_name
        ORDER BY SUM(
            CASE
                WHEN t.created_at < '2025-10-04 17:30:00' THEN
                    t.Net_sales + t.nameset_price
                    + CASE WHEN t.Net_sales < 0 THEN COALESCE(-online.discount,0)
                           ELSE COALESCE(online.discount,0)
                      END
                WHEN t.Net_sales < 0 THEN
                    t.Net_sales + t.nameset_price + COALESCE(-online.discount,0)
                ELSE
                    t.Net_sales + t.nameset_price
            END
        ) DESC
    ";

        $result = DB::select($sql, [$start, $end]);

        $item  = [];
        $total = 0;

        foreach ($result as $row) {
            $nett_sales = $row->net_sales_adj ?? 0;
            if ($nett_sales > 0) {
                $item[] = [
                    'st_name' => $row->st_name,
                    'total' => $nett_sales,
                    'color' => $this->getColorForStore($row->st_name),
                ];
                $total += $nett_sales;
            }
        }

        $data = [
            'item' => $item,
            'total' => $total,
        ];

        return view('app.dashboard._load_sales', compact('data'));
    }




    public function getProfitGraph(Request $request)
    {
        $date = $request->post('_range');
        $type = $request->post('type');

        $start = null;
        $end = null;
        $item = array();
        $total = 0;
        if (!empty($date)) {
            $exp = explode('|', $date);
            if (count($exp) > 1) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $date;
            }
        }
        $store = DB::table('stores')
            ->where('st_delete', '!=', '1')->get();
        if (!empty($store->first())) {
            foreach ($store as $row) {
                $st_id = $row->id;
                $admin_cost = DB::table('pos_transactions')->select('pos_admin_cost', 'pos_status')
                    ->where(function ($w) use ($start, $end, $st_id) {
                        if (!empty($st_id)) {
                            $w->where('pos_transactions.st_id', '=', $st_id);
                        }
                        if (!empty($end)) {
                            $w->whereDate('pos_transactions.created_at', '>=', $start)
                                ->whereDate('pos_transactions.created_at', '<=', $end);
                        } else {
                            $w->whereDate('pos_transactions.created_at', '=', $start);
                        }
                    })
                    ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION', 'CANCEL', 'UNPAID'])
                    ->sum('pos_admin_cost');

                if ($type == 'cross') {
                    $cadmin_cost = DB::table('pos_transactions')->select('pos_admin_cost', 'pos_status')
                        ->where(function ($w) use ($start, $end, $st_id) {
                            if (!empty($st_id)) {
                                $w->where('pos_transactions.st_id_ref', '=', $st_id);
                            }
                            if (!empty($end)) {
                                $w->whereDate('pos_transactions.created_at', '>=', $start)
                                    ->whereDate('pos_transactions.created_at', '<=', $end);
                            } else {
                                $w->whereDate('pos_transactions.created_at', '=', $start);
                            }
                        })
                        ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION', 'CANCEL', 'UNPAID'])
                        ->sum('pos_admin_cost');
                    $cadmin_cost;
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
                    ->where(function ($w) use ($start, $end, $st_id) {
                        if (!empty($end)) {
                            $w->whereDate('pos_transactions.created_at', '>=', $start)
                                ->whereDate('pos_transactions.created_at', '<=', $end);
                        } else {
                            $w->whereDate('pos_transactions.created_at', '=', $start);
                        }
                        if (!empty($st_id)) {
                            $w->where('pos_transactions.st_id', '=', $st_id);
                        }
                        $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION', 'CANCEL', 'UNPAID']);
                    })
                    ->groupBy('pos_transaction_details.id')
                    ->get();
                $profit = 0;
                if (!empty($pf->first())) {
                    foreach ($pf as $srow) {
                        $sales_total = 0;
                        if (!empty($srow->pos_td_marketplace_price)) {
                            $sales_total = $srow->pos_td_marketplace_price;
                        } else {
                            $sales_total = $srow->pos_td_discount_price;
                        }
                        $purchase = 0;
                        if (!empty($srow->purchase)) {
                            $purchase = round($srow->purchase);
                        } else {
                            if (!empty($srow->poad_total_price)) {
                                $purchase = round($srow->poad_total_price / $srow->poad_qty);
                            } else {
                                if (!empty($srow->ps_purchase_price)) {
                                    $purchase = $srow->ps_purchase_price;
                                } else {
                                    $purchase = $srow->p_purchase_price;
                                }
                            }
                        }
                        $profit += $sales_total - ($srow->pos_td_qty * $purchase);
                    }
                }
                $profit = $profit - $admin_cost;

                if ($type == 'cross') {
                    $cpf = DB::table('pos_transaction_details')
                        ->selectRaw("ts_pos_transactions.created_at as created_at, pos_invoice, br_name, p_name, p_color, sz_name, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
                        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
                        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                        ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->where(function ($w) use ($start, $end, $st_id) {
                            if (!empty($end)) {
                                $w->whereDate('pos_transactions.created_at', '>=', $start)
                                    ->whereDate('pos_transactions.created_at', '<=', $end);
                            } else {
                                $w->whereDate('pos_transactions.created_at', '=', $start);
                            }
                            if (!empty($st_id)) {
                                $w->where('pos_transactions.st_id_ref', '=', $st_id);
                            }
                            $w->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION', 'CANCEL', 'UNPAID']);
                        })
                        ->groupBy('pos_transaction_details.id')
                        ->get();
                    $cprofit = 0;
                    if (!empty($cpf->first())) {
                        foreach ($cpf as $srow) {
                            $sales_total = 0;
                            if (!empty($srow->pos_td_marketplace_price)) {
                                $sales_total = $srow->pos_td_marketplace_price;
                            } else {
                                $sales_total = $srow->pos_td_discount_price;
                            }
                            $purchase = 0;
                            if (!empty($srow->purchase)) {
                                $purchase = round($srow->purchase);
                            } else {
                                if (!empty($srow->poad_total_price)) {
                                    $purchase = round($srow->poad_total_price / $srow->poad_qty);
                                } else {
                                    if (!empty($srow->ps_purchase_price)) {
                                        $purchase = $srow->ps_purchase_price;
                                    } else {
                                        $purchase = $srow->p_purchase_price;
                                    }
                                }
                            }
                            $cprofit += $sales_total - ($srow->pos_td_qty * $purchase);
                        }
                    }
                    $cprofit = $cprofit - $cadmin_cost;
                    $profit = $profit + $cprofit;
                }

                if ($profit > 0) {
                    $item[] = [
                        'st_name' => $row->st_name,
                        'total' => $profit,
                    ];
                }
                sort($item);
                $total += $profit;
            }
        }
        $data = [
            'item' => $item,
            'total' => $total,
        ];
        return view('app.dashboard._load_profit', compact('data'));
    }

    // public function getcSalesGraph(Request $request)
    // {
    //     $date = $request->post('_range');

    //     $start = null;
    //     $end = null;
    //     $item = array();
    //     $total = 0;
    //     if (!empty($date)) {
    //         $exp = explode('|', $date);
    //         if (count($exp) > 1) {
    //             $start = $exp[0];
    //             $end = $exp[1];
    //         } else {
    //             $start = $date;
    //         }
    //     }
    //     $store = DB::table('stores')
    //         ->where('st_delete', '!=', '1')->get();
    //     if (!empty($store->first())) {
    //                 foreach ($store as $row) {
    //                     $st_id = $row->id;

    //                     $nett_sales = DB::table('pos_transaction_details')
    //                         ->selectRaw("SUM(
    //                             CASE
    //                                 WHEN ts_pos_transactions.pos_invoice NOT LIKE 'INV%' THEN ROUND(ts_pos_transaction_details.pos_td_sell_price, 1)
    //                                 WHEN ts_pos_transaction_details.pos_td_discount_number > 0
    //                                      AND (
    //                                          CASE
    //                                              WHEN ts_products.article_id = 'CUS01' THEN 0
    //                                              ELSE ROUND(
    //                                                  (ts_product_stocks.ps_price_tag * ts_pos_transaction_details.pos_td_qty)
    //                                                  - ts_pos_transaction_details.pos_td_sell_price, 1
    //                                              ) + COALESCE(ts_pos_transaction_details.pos_td_nameset_price, 0)
    //                                          END
    //                                      ) = 0
    //                                 THEN ROUND(ts_pos_transaction_details.pos_td_sell_price, 1) - ts_pos_transaction_details.pos_td_discount_number
    //                                 ELSE ROUND(ts_pos_transaction_details.pos_td_sell_price, 1)
    //                             END
    //                         ) as nett_sales")
    //                         ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
    //                         ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
    //                         ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
    //                         ->where(function ($w) use ($start, $end, $st_id) {
    //                             if (!empty($st_id)) {
    //                                 $w->where('pos_transactions.st_id', '=', $st_id);
    //                             }
    //                             if (!empty($end)) {
    //                                 $w->whereDate('pos_transactions.created_at', '>=', $start)
    //                                     ->whereDate('pos_transactions.created_at', '<=', $end);
    //                             } else {
    //                                 $w->whereDate('pos_transactions.created_at', '=', $start);
    //                             }
    //                         })
    //                         ->whereNotIn('pos_transactions.pos_status', ['UNPAID'])
    //                         ->whereIn('pos_transactions.pos_status', ['DONE', 'NAMESET'])
    //                         ->value('nett_sales')
    //                         ->get();

    //                     $nett_sales = $nett_sales ?? 0;

    //                     if ($nett_sales > 0) {
    //                         $item[] = [
    //                             'st_name' => $row->st_name,
    //                             'total' => $nett_sales,
    //                             'color' => $this->getColorForStore($row->st_name),
    //                         ];
    //                     }
    //                     sort($item);
    //                     $total += $nett_sales;
    //                 }
    //                 dd($nett_sales)
    //             }
    //     $data = [
    //         'item' => $item,
    //         'total' => $total,
    //     ];
    //     return view('app.dashboard._load_csales', compact('data'));
    // }

    public function getcProfitGraph(Request $request)
    {
        $date = $request->post('_range');

        // --- DATE RANGE PARSING
        $start = null;
        $end = null;

        if (!empty($date)) {
            $exp = explode('|', $date);
            if (count($exp) > 1) {
                $start = $exp[0] . " 00:00:00";
                $end   = $exp[1] . " 23:59:59";
            } else {
                $start = $date . " 00:00:00";
                $end   = $date . " 23:59:59";
            }
        }
        // --- QUERY LANGSUNG GROUP BY STORE
        $sql = "
        SELECT
            t.st_name,
            round(sum(t.Cogs),0) as Cogs_adj,
            SUM(
                CASE
                    WHEN t.created_at < '2025-10-04 17:30:00' THEN
                        t.Net_sales + t.nameset_price
                        + CASE WHEN t.Net_sales < 0 THEN COALESCE(-online.discount,0)
                               ELSE COALESCE(online.discount,0)
                          END
                    WHEN t.Net_sales < 0 THEN
                        t.Net_sales + t.nameset_price + COALESCE(-online.discount,0)
                    ELSE
                        t.Net_sales + t.nameset_price
                END
            ) AS net_sales_adj
        FROM (
            SELECT
                ts_pos_transactions.pos_invoice,
                SUM(
                    CASE
                        WHEN ts_pos_transactions.pos_invoice NOT LIKE 'INV%' THEN
                            ROUND(ts_pos_transaction_details.pos_td_sell_price, 1)
                        WHEN ts_pos_transaction_details.pos_td_discount_number > 0
                            AND (
                                CASE
                                    WHEN ts_products.article_id = 'CUS01' THEN 0
                                    ELSE ROUND(
                                        (ts_product_stocks.ps_price_tag * ts_pos_transaction_details.pos_td_qty)
                                        - ts_pos_transaction_details.pos_td_sell_price, 1
                                    ) + COALESCE(ts_pos_transaction_details.pos_td_nameset_price,0)
                                END
                            ) = 0
                        THEN
                            ROUND(ts_pos_transaction_details.pos_td_sell_price,1)
                            - ts_pos_transaction_details.pos_td_discount_number
                        ELSE
                            ROUND(ts_pos_transaction_details.pos_td_sell_price,1)
                    END
                ) AS Net_sales,
                COALESCE(SUM(ts_pos_transaction_details.pos_td_nameset_price),0) AS nameset_price,
                ts_stores.st_name,
                sum(ts_product_stocks.ps_price_tag) AS Cogs,
                ts_pos_transactions.created_at
            FROM ts_pos_transaction_details
            LEFT JOIN ts_pos_transactions ON ts_pos_transactions.id = ts_pos_transaction_details.pt_id
            LEFT JOIN ts_product_stocks   ON ts_product_stocks.id = ts_pos_transaction_details.pst_id
            LEFT JOIN ts_products         ON ts_products.id = ts_product_stocks.p_id
            LEFT JOIN ts_stores           ON ts_pos_transactions.st_id = ts_stores.id
            WHERE ts_pos_transactions.pos_status IN
                ('DONE','CANCEL','NAMESET','REFUND','SHIPPED','TELAH DIKIRIM','TO SHIP')
                AND ts_pos_transactions.st_id IN (2,3,5,8,20,30,40,53)
                AND ts_pos_transactions.created_at BETWEEN ? AND ?
            GROUP BY ts_pos_transactions.pos_invoice, ts_stores.st_name, ts_pos_transactions.created_at
        ) t
        LEFT JOIN (
            SELECT
                ts_online_transactions.order_number,
                ts_online_transaction_details.discount_platform AS discount
            FROM ts_online_transactions
            LEFT JOIN ts_online_transaction_details
                ON ts_online_transaction_details.to_id = ts_online_transactions.id
            GROUP BY ts_online_transactions.order_number
        ) online ON online.order_number = t.pos_invoice
        GROUP BY t.st_name
        ORDER BY SUM(
            CASE
                WHEN t.created_at < '2025-10-04 17:30:00' THEN
                    t.Net_sales + t.nameset_price
                    + CASE WHEN t.Net_sales < 0 THEN COALESCE(-online.discount,0)
                           ELSE COALESCE(online.discount,0)
                      END
                WHEN t.Net_sales < 0 THEN
                    t.Net_sales + t.nameset_price + COALESCE(-online.discount,0)
                ELSE
                    t.Net_sales + t.nameset_price
            END
        ) DESC
    ";

        $result = DB::select($sql, [$start, $end]);

        $total = 0;
        $item = [];
        $count = 0; // untuk menghitung jumlah store

        foreach ($result as $row) {
            // pastikan tipe float
            $nett_sales = isset($row->net_sales_adj) ? (float) $row->net_sales_adj : 0;
            $cogs       = isset($row->cogs_adj) ? (float) $row->cogs_adj : 0;

            if ($nett_sales > 0) {
                // hitung margin % dengan benar
                $margin_percent = (($nett_sales - $cogs) / $nett_sales) * 100;

                // pastikan margin tidak lebih dari 100% atau kurang dari 0%
                $margin_percent = max(0, min($margin_percent, 100));

                $item[] = [
                    'st_name' => $row->st_name,
                    'total'   => $margin_percent,                       // nilai untuk chart
                    'margin'  => number_format($margin_percent, 2) . '%', // untuk tampilkan
                    'color'   => $this->getColorForStore($row->st_name),
                ];

                $total += $margin_percent;
                $count++;
            }
        }

        $data = [
            'item' => $item,
            'total' => number_format($margin_percent, 2) . '%', // rata-rata % tampilkan
        ];


        return view('app.dashboard._load_cprofit', compact('data'));
    }

    public function getPurchaseGraph(Request $request)
    {
        $date = $request->post('_range');

        $start = null;
        $end = null;

        if (!empty($date)) {
            $exp = explode('|', $date);
            if (count($exp) > 1) {
                $start = $exp[0] . " 00:00:00";
                $end   = $exp[1] . " 23:59:59";
            } else {
                $start = $date . " 00:00:00";
                $end   = $date . " 23:59:59";
            }
        }

        $items = DB::table('purchase_orders')
            ->select(
                'stores.st_name',
                DB::raw('ROUND(SUM(ts_purchase_order_article_details.poad_qty * ts_purchase_order_article_details.poad_purchase_price), 0) AS total')
            )
            ->leftJoin('purchase_order_articles', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
            ->leftJoin('stores', 'purchase_orders.st_id', '=', 'stores.id')
            ->whereNotNull('purchase_orders.st_id')
            ->whereNotNull('purchase_orders.ps_id')
            ->where('purchase_order_article_details.pst_id', '!=', 702260);


        if ($start && $end) {
            $items->whereBetween('purchase_orders.created_at', [$start, $end]);
        }

        $items = $items
            ->groupBy('stores.st_name')
            ->get();
        $item = [];
        $total = 0;

        if ($items->isNotEmpty()) {
            foreach ($items as $row) {
                if ($row->total > 0) {
                    $item[] = [
                        'st_name' => $row->st_name,
                        'total' => $row->total,
                        'color' => $this->getColorForStore($row->st_name),
                    ];
                }
            }
            sort($item);
            $total = $items->sum('total');
        }

        $data = [
            'item' => $item,
            'total' => round($total),
        ];

        return view('app.dashboard._load_purchase', compact('data'));
    }


    public function getCCAssetGraph(Request $request)
    {
        $date = $request->post('_range');
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();
        $start = null;
        $end = null;
        $item = array();
        $total = 0;
        if (!empty($date)) {
            $exp = explode('|', $date);
            if (count($exp) > 1) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $date;
            }
        }
        $store = DB::table('stores')
            ->where('st_delete', '!=', '1')->get();
        if (!empty($store->first())) {
            foreach ($store as $row) {
                $st_id = $row->id;

                $ccassets = DB::table('product_location_setups')
                    ->selectRaw("ts_product_location_setups.pls_qty as pls_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
                    ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                    ->whereNotIn('pl_code', $exception)
                    ->where(function ($w) use ($st_id) {
                        $w->where('product_locations.st_id', '=', $st_id);
                    })
                    ->where('product_location_setups.pls_qty', '>', '0')
                    ->whereIn('stkt_id', ['1', '3'])
                    ->groupBy('product_location_setups.id')
                    ->get();
                $cc_assets = 0;
                if (!empty($ccassets->first())) {
                    foreach ($ccassets as $srow) {
                        $pp = 0;
                        if (!empty($srow->purchase)) {
                            $pp = round($srow->purchase);
                        } else {
                            if (!empty($srow->ps_purchase_price)) {
                                $pp = $srow->ps_purchase_price;
                            } else {
                                $pp = $srow->p_purchase_price;
                            }
                        }
                        $cc_assets += ($srow->pls_qty * $pp);
                    }
                }

                if ($cc_assets > 0) {
                    $item[] = [
                        'st_name' => $row->st_name,
                        'total' => $cc_assets,
                    ];
                }
                sort($item);
                $total += $cc_assets;
            }
        }
        $data = [
            'item' => $item,
            'total' => round($total),
        ];
        return view('app.dashboard._load_cc_asset', compact('data'));
    }

    public function getCAAssetGraph(Request $request)
    {
        $date = $request->post('_range');
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();
        $start = null;
        $end = null;
        $item = array();
        $total = 0;
        if (!empty($date)) {
            $exp = explode('|', $date);
            if (count($exp) > 1) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $date;
            }
        }
        $store = DB::table('stores')
            ->where('st_delete', '!=', '1')->get();
        if (!empty($store->first())) {
            foreach ($store as $row) {
                $st_id = $row->id;

                $cassets = DB::table('product_location_setups')
                    ->selectRaw("ts_product_location_setups.pls_qty as pls_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, ps_purchase_price, p_purchase_price, stkt_id, pl_code")
                    ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                    ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_location_setups.pst_id')
                    ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                    ->whereNotIn('pl_code', $exception)
                    ->where(function ($w) use ($st_id) {
                        $w->where('product_locations.st_id', '=', $st_id);
                    })
                    ->where('product_location_setups.pls_qty', '>', '0')
                    ->whereIn('stkt_id', ['2'])
                    ->groupBy('product_location_setups.id')
                    ->get();
                $c_assets = 0;
                if (!empty($cassets->first())) {
                    foreach ($cassets as $srow) {
                        $pp = 0;
                        if (!empty($srow->purchase)) {
                            $pp = round($srow->purchase);
                        } else {
                            if (!empty($srow->ps_purchase_price)) {
                                $pp = $srow->ps_purchase_price;
                            } else {
                                $pp = $srow->p_purchase_price;
                            }
                        }
                        $c_assets += ($srow->pls_qty * $pp);
                    }
                }

                if ($c_assets > 0) {
                    $item[] = [
                        'st_name' => $row->st_name,
                        'total' => $c_assets,
                    ];
                }
                sort($item);
                $total += $c_assets;
            }
        }
        $data = [
            'item' => $item,
            'total' => round($total),
        ];
        return view('app.dashboard._load_c_asset', compact('data'));
    }

    public function getDebtGraph(Request $request)
    {
        $date = $request->post('_range');

        $start = null;
        $end = null;
        $item = array();
        $total = 0;
        if (!empty($date)) {
            $exp = explode('|', $date);
            if (count($exp) > 1) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $date;
            }
        }
        $store = DB::table('stores')
            ->where('st_delete', '!=', '1')->get();
        if (!empty($store->first())) {
            foreach ($store as $row) {
                $st_id = $row->id;

                $debt_list = DB::table('debt_lists')->select('dl_total', 'st_id')->where('debt_lists.dl_delete', '!=', '1')
                    ->where(function ($w) use ($st_id) {
                        $w->where('st_id', '=', $st_id);
                    })->sum('dl_total');
                $debt_list_payment = DB::table('debt_list_payments')->select('dlp_value', 'st_id')->leftJoin('debt_lists', 'debt_lists.id', '=', 'debt_list_payments.dl_id')
                    ->where(function ($w) use ($st_id) {
                        $w->where('st_id', '=', $st_id);
                    })->where('debt_lists.dl_delete', '!=', '1')->sum('dlp_value');
                $debts = $debt_list - $debt_list_payment;

                if ($debts > 0) {
                    $item[] = [
                        'st_name' => $row->st_name,
                        'total' => $debts,
                    ];
                }
                sort($item);
                $total += $debts;
            }
        }
        $data = [
            'item' => $item,
            'total' => round($total),
        ];
        return view('app.dashboard._load_debt', compact('data'));
    }

    private function getColorForStore($storeName)
    {
        if (stripos($storeName, 'JEZ MALANG') !== false) {
            return '#ED3500';
        }
        if (stripos($storeName, 'JEZ SURABAYA') !== false) {
            return '#08CB00';
        }
        if (stripos($storeName, 'JEZ KEDIRI') !== false) {
            return '#540863';
        }
        if (stripos($storeName, 'JEZ JEMBER') !== false) {
            return '#F4F754';
        }
        if (stripos($storeName, 'JEZ SEMARANG') !== false) {
            return '#0046FF';
        }
        if (stripos($storeName, 'JEZ SIDOARJO') !== false) {
            return '#FF6C0C';
        }
        if (stripos($storeName, 'ONLINE SURABAYA') !== false) {
            return '#007E6E';
        }
        if (stripos($storeName, 'ONLINE MALANG') !== false) {
            return '#F875AA';
        }

        $hash = substr(md5($storeName), 0, 6);
        return "#" . $hash;
    }
}
