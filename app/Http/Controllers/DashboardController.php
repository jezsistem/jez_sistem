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
    ts_stores.st_name AS Store,

    SUM(
        CASE
            WHEN ts_pos_transactions.pos_invoice NOT LIKE 'INV%'
                THEN ROUND(ts_pos_transaction_details.pos_td_sell_price, 1)
            WHEN ts_pos_transaction_details.pos_td_discount_number > 0
                AND (
                    CASE
                        WHEN ts_products.article_id = 'CUS01' THEN 0
                        ELSE ROUND(
                            (ts_product_stocks.ps_price_tag * ts_pos_transaction_details.pos_td_qty) -
                            ts_pos_transaction_details.pos_td_sell_price, 1
                        ) + COALESCE(ts_pos_transaction_details.pos_td_nameset_price, 0)
                    END
                ) = 0
                THEN ROUND(ts_pos_transaction_details.pos_td_sell_price, 1) - ts_pos_transaction_details.pos_td_discount_number
            ELSE ROUND(ts_pos_transaction_details.pos_td_sell_price, 1)
        END
    ) AS Total_Net_Sales,

    SUM(ts_product_stocks.ps_purchase_price * ts_pos_transaction_details.pos_td_qty) AS Total_COGS
FROM ts_pos_transaction_details
LEFT JOIN ts_pos_transactions ON ts_pos_transactions.id = ts_pos_transaction_details.pt_id
LEFT JOIN ts_stores ON ts_pos_transactions.st_id = ts_stores.id
LEFT JOIN ts_product_stocks ON ts_product_stocks.id = ts_pos_transaction_details.pst_id
LEFT JOIN ts_products ON ts_products.id = ts_product_stocks.p_id
WHERE ts_pos_transactions.pos_status NOT IN ('UNPAID')
  AND ts_pos_transactions.created_at BETWEEN ? AND ?
GROUP BY Store
ORDER BY ts_stores.st_name;
    ";

        $result = DB::select($sql, [$start, $end]);

        $item  = [];
        $total = 0;

        foreach ($result as $row) {
            $nett_sales = $row->Total_Net_Sales ?? 0;
            if ($nett_sales > 0) {
                $item[] = [
                    'st_name' => $row->Store,
                    'total' => $nett_sales,
                    'color' => $this->getColorForStore($row->Store),
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


        $sql = "
            SELECT
    ts_stores.st_name AS Store,
    SUM(ts_product_stocks.ps_purchase_price * ts_pos_transaction_details.pos_td_qty) AS COGS
    FROM ts_pos_transaction_details
    LEFT JOIN ts_pos_transactions ON ts_pos_transactions.id = ts_pos_transaction_details.pt_id
    LEFT JOIN ts_product_stocks ON ts_product_stocks.id = ts_pos_transaction_details.pst_id
    LEFT JOIN ts_stores ON ts_pos_transactions.st_id = ts_stores.id
    WHERE ts_pos_transactions.pos_status NOT IN ('UNPAID')
    AND ts_pos_transactions.created_at BETWEEN ? AND ?
    GROUP BY ts_stores.st_name
    ORDER BY ts_stores.st_name;
    ";

        $result = DB::select($sql, [$start, $end]);

        $item  = [];
        $total = 0;

        foreach ($result as $row) {
            $cogs = $row->COGS ?? 0;
            if ($cogs > 0) {
                $item[] = [
                    'st_name' => $row->Store,
                    'total' => $cogs,
                    'color' => $this->getColorForStore($row->Store),
                ];
                $total += $cogs;
            }
        }

        $data = [
            'item' => $item,
            'total' => $total,
        ];
        return view('app.dashboard._load_profit', compact('data'));
    }

    public function getcSalesGraph(Request $request)
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


        $sql = "
            SELECT
    ts_stores.st_name AS Store,
    SUM(ts_product_stocks.ps_price_tag * ts_pos_transaction_details.pos_td_qty) AS Gross_Sales,
    SUM(ts_product_stocks.ps_purchase_price * ts_pos_transaction_details.pos_td_qty) AS COGS
    FROM ts_pos_transaction_details
    LEFT JOIN ts_pos_transactions ON ts_pos_transactions.id = ts_pos_transaction_details.pt_id
    LEFT JOIN ts_product_stocks ON ts_product_stocks.id = ts_pos_transaction_details.pst_id
    LEFT JOIN ts_stores ON ts_pos_transactions.st_id = ts_stores.id
    WHERE ts_pos_transactions.pos_status NOT IN ('UNPAID')
    AND ts_pos_transactions.created_at BETWEEN ? AND ?
    GROUP BY ts_stores.st_name
    ORDER BY ts_stores.st_name;
    ";

        $result = DB::select($sql, [$start, $end]);

        $item  = [];
        $total = 0;

        foreach ($result as $row) {
            $gross_margin = $row->Gross_Sales ?? 0;
            if ($gross_margin > 0) {
                $item[] = [
                    'st_name' => $row->Store,
                    'total' => $gross_margin,
                    'color' => $this->getColorForStore($row->Store),
                ];
                $total += $gross_margin;
            }
        }

        $data = [
            'item' => $item,
            'total' => $total,
        ];
        return view('app.dashboard._load_csales', compact('data'));
    }

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

        $sql = "
SELECT
    ts_stores.st_name AS Store,

    SUM(
        CASE
            WHEN ts_pos_transactions.pos_invoice NOT LIKE 'INV%'
                THEN ROUND(ts_pos_transaction_details.pos_td_sell_price, 1)
            WHEN ts_pos_transaction_details.pos_td_discount_number > 0
                AND (
                    CASE
                        WHEN ts_products.article_id = 'CUS01' THEN 0
                        ELSE ROUND(
                            (ts_product_stocks.ps_price_tag * ts_pos_transaction_details.pos_td_qty) -
                            ts_pos_transaction_details.pos_td_sell_price, 1
                        ) + COALESCE(ts_pos_transaction_details.pos_td_nameset_price, 0)
                    END
                ) = 0
                THEN ROUND(ts_pos_transaction_details.pos_td_sell_price, 1) - ts_pos_transaction_details.pos_td_discount_number
            ELSE ROUND(ts_pos_transaction_details.pos_td_sell_price, 1)
        END
    ) AS Total_Net_Sales,

    SUM(ts_product_stocks.ps_purchase_price * ts_pos_transaction_details.pos_td_qty) AS Total_COGS
FROM ts_pos_transaction_details
LEFT JOIN ts_pos_transactions ON ts_pos_transactions.id = ts_pos_transaction_details.pt_id
LEFT JOIN ts_stores ON ts_pos_transactions.st_id = ts_stores.id
LEFT JOIN ts_product_stocks ON ts_product_stocks.id = ts_pos_transaction_details.pst_id
LEFT JOIN ts_products ON ts_products.id = ts_product_stocks.p_id
WHERE ts_pos_transactions.pos_status NOT IN ('UNPAID')
  AND ts_pos_transactions.created_at BETWEEN ? AND ?
GROUP BY Store
ORDER BY ts_stores.st_name;
    ";

        $result = DB::select($sql, [$start, $end]);

        $item  = [];
        $total = 0;

        foreach ($result as $row) {
            $net_sales = $row->Total_Net_Sales ?? 0;
            $cogs = $row->Total_COGS ?? 0;

            $margin = $net_sales > 0 ? round((($net_sales - $cogs) / $net_sales) * 100, 2) : 0;

            $item[] = [
                'st_name' => $row->Store,
                'total'   => $margin,
                'color'   => $this->getColorForStore($row->Store),
            ];

            $total += $margin;
        }

        $data = [
            'item' => $item,
            'total' => $total,
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
    ts_brands.br_name AS Brand,

    SUM(
        CASE
            WHEN ts_pos_transactions.pos_invoice NOT LIKE 'INV%'
                THEN ROUND(ts_pos_transaction_details.pos_td_sell_price, 1)
            WHEN ts_pos_transaction_details.pos_td_discount_number > 0
                AND (
                    CASE
                        WHEN ts_products.article_id = 'CUS01' THEN 0
                        ELSE ROUND(
                            (ts_product_stocks.ps_price_tag * ts_pos_transaction_details.pos_td_qty) -
                            ts_pos_transaction_details.pos_td_sell_price, 1
                        ) + COALESCE(ts_pos_transaction_details.pos_td_nameset_price, 0)
                    END
                ) = 0
                THEN ROUND(ts_pos_transaction_details.pos_td_sell_price, 1) - ts_pos_transaction_details.pos_td_discount_number
            ELSE ROUND(ts_pos_transaction_details.pos_td_sell_price, 1)
        END
    ) AS Total_Net_Sales,

FROM ts_pos_transaction_details
LEFT JOIN ts_pos_transactions ON ts_pos_transactions.id = ts_pos_transaction_details.pt_id
LEFT JOIN ts_stores ON ts_pos_transactions.st_id = ts_stores.id
LEFT JOIN ts_product_stocks ON ts_product_stocks.id = ts_pos_transaction_details.pst_id
LEFT JOIN ts_products ON ts_products.id = ts_product_stocks.p_id
LEFT JOIN ts_brands on ts_products.br_id = ts_brands.id
WHERE ts_pos_transactions.pos_status NOT IN ('UNPAID')
AND ts_pos_transactions.created_at BETWEEN ? AND ?
GROUP BY Brand
ORDER BY Total_Net_Sales desc
LIMIT 5;
    ";

        $result = DB::select($sql, [$start, $end]);

        $item  = [];
        $total = 0;

        foreach ($result as $row) {
            $nett_sales = $row->Total_Net_Sales ?? 0;
            if ($nett_sales > 0) {
                $item[] = [
                    'br_name' => $row->Brand,
                    'total' => $nett_sales,
                    'color' => $this->getColorForStore($row->Brand),
                ];
                $total += $nett_sales;
            }
        }

        $data = [
            'item' => $item,
            'total' => $total,
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
