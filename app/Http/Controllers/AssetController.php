<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\ExceptionLocation;

class AssetController extends Controller
{
    protected function UserActivity($activity)
    {
        UserActivity::create([
            'user_id' => Auth::user()->id,
            'ua_description' => $activity,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    protected function getLabel($table, $field, $id)
    {
        $label = DB::table($table)->select($field)->where('id', '=', $id)->get()->first();
        if (!empty($label)) {
            return $label->$field;
        } else {
            return '[field not found]';
        }
    }

    public function getAssetByBrand(Request $request)
    {
        $type = $request->data_type;
        $date = $request->dashboard_date;
        $start = null;
        $end = null;
        if (!empty($date)) {
          $exp = explode('|', $date);
          if (count($exp) > 1) {
            $start = $exp[0];
            $end = $exp[1];
          } else {
            $start = $date;
          }
        } else {
            $start = date('Y-m-d');
        }
        if ($type == 'sales' || $type == '') {
            if(request()->ajax()) {
                return datatables()->of(DB::table('brands')
                ->select("brands.id as id", "br_name", "br_delete", "st_id", "pos_status", DB::raw("sum(ts_pos_transaction_details.pos_td_discount_price) as value"), DB::raw("sum(ts_pos_transaction_details.pos_td_qty) as qty"))
                ->leftJoin('products', 'products.br_id', '=', 'brands.id')
                ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                ->leftJoin('pos_transaction_details', 'pos_transaction_details.pst_id', '=', 'product_stocks.id')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->where('br_delete', '!=', '1')
                ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                ->where(function($w) use ($start, $end) {
                    if (!empty($end)) {
                        $w->whereDate('pos_transactions.created_at', '>=', $start)
                        ->whereDate('pos_transactions.created_at', '<=', $end);
                    } else {
                        $w->whereDate('pos_transactions.created_at', '=', $start);
                    }
                })
                ->groupBy('brands.id'))
                ->editColumn('value', function($d) {
                    return number_format($d->value);
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
        if ($type == 'csales') {
            if(request()->ajax()) {
                return datatables()->of(DB::table('brands')
                ->select("brands.id as id", "br_name", "br_delete", "st_id", "pos_status", DB::raw("sum(ts_pos_transaction_details.pos_td_discount_price) as value"), DB::raw("sum(ts_pos_transaction_details.pos_td_qty) as qty"))
                ->leftJoin('products', 'products.br_id', '=', 'brands.id')
                ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                ->leftJoin('pos_transaction_details', 'pos_transaction_details.pst_id', '=', 'product_stocks.id')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->where('br_delete', '!=', '1')
                ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                ->where(function($w) use ($start, $end) {
                    if (!empty($end)) {
                        $w->whereDate('pos_transactions.created_at', '>=', $start)
                        ->whereDate('pos_transactions.created_at', '<=', $end);
                    } else {
                        $w->whereDate('pos_transactions.created_at', '=', $start);
                    }
                })
                ->groupBy('brands.id'))
                ->editColumn('value', function($d) {
                    return number_format($d->value);
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
        if ($type == 'purchases') {
            if(request()->ajax()) {
                return datatables()->of(DB::table('brands')
                ->select("brands.id as id", "br_name", "br_delete", "st_id",
                DB::raw("sum(ts_purchase_order_article_detail_statuses.poads_total_price) as value"), DB::raw("sum(ts_purchase_order_article_detail_statuses.poads_qty) as qty"))
                ->leftJoin('products', 'products.br_id', '=', 'brands.id')
                ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
                ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
                ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
                ->where('br_delete', '!=', '1')
                ->where(function($w) use ($start, $end) {
                    if (!empty($end)) {
                        $w->whereDate('purchase_order_article_detail_statuses.created_at', '>=', $start)
                        ->whereDate('purchase_order_article_detail_statuses.created_at', '<=', $end);
                    } else {
                        $w->whereDate('purchase_order_article_detail_statuses.created_at', '=', $start);
                    }
                })
                ->groupBy('brands.id'))
                ->editColumn('value', function($d) {
                    return number_format($d->value);
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
        if ($type == 'debts') {
            if(request()->ajax()) {
                return datatables()->of(DB::table('brands')
                ->select("brands.id as id", "br_name", "br_delete", "st_id",
                DB::raw("(sum(ts_debt_lists.dl_total) - sum(ts_debt_list_payments.dlp_value)) as value"),
                DB::raw("(sum(ts_debt_lists.dl_total) - sum(ts_debt_list_payments.dlp_value)) as qty"))
                ->leftJoin('debt_lists', 'debt_lists.br_id', '=', 'brands.id')
                ->leftJoin('debt_list_payments', 'debt_list_payments.dl_id', '=', 'debt_lists.id')
                ->where('br_delete', '!=', '1')
                ->where('debt_lists.dl_delete', '!=', '1')
                ->groupBy('debt_lists.br_id'))
                ->editColumn('value', function($d) {
                    return number_format($d->value);
                })
                ->editColumn('qty', function($d) {
                    return number_format($d->value);
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

    public function loadAssets(Request $request)
    {
        $date = $request->post('dashboard_date');
        $start = null;
        $end = null;
        if (!empty($date)) {
          $exp = explode('|', $date);
          if (count($exp) > 1) {
            $start = $exp[0];
            $end = $exp[1];
          } else {
            $start = $date;
          }
        } else {
            $start = date('Y-m-d');
        }

        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
        ->get()
        ->toArray();

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
                        'cc_assets' => ($row->qty*$purchase),
                        'br_name' => $row->br_name,
                    );
                } else {
                    $cc[$key]['cc_assets'] = $cc[$key]['cc_assets'] + ($row->qty*$purchase);
                }
            }
        }
        rsort($cc);
        $r['item'] = $cc;
        return json_encode($r);
    }

    public function loadCAssets(Request $request)
    {
        $date = $request->post('dashboard_date');
        $start = null;
        $end = null;
        if (!empty($date)) {
          $exp = explode('|', $date);
          if (count($exp) > 1) {
            $start = $exp[0];
            $end = $exp[1];
          } else {
            $start = $date;
          }
        } else {
            $start = date('Y-m-d');
        }

        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
        ->get()
        ->toArray();

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
        ->whereIn('stkt_id', ['2'])
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
                        'c_assets' => ($row->qty*$purchase),
                        'br_name' => $row->br_name,
                    );
                } else {
                    $cc[$key]['c_assets'] = $cc[$key]['c_assets'] + ($row->qty*$purchase);
                }
            }
        }
        rsort($cc);
        $r['item'] = $cc;
        return json_encode($r);
    }

    public function loadProfit(Request $request)
    {
        $date = $request->post('dashboard_date');
        $start = null;
        $end = null;
        if (!empty($date)) {
          $exp = explode('|', $date);
          if (count($exp) > 1) {
            $start = $exp[0];
            $end = $exp[1];
          } else {
            $start = $date;
          }
        } else {
            $start = date('Y-m-d');
        }

        $pf = array();
        $profit = DB::table('pos_transaction_details')
        ->selectRaw("ts_pos_transactions.created_at as created_at, br_name, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->where(function ($w) use ($start, $end) {
            if (!empty($end)) {
                $w->whereDate('pos_transactions.created_at', '>=', $start)
                ->whereDate('pos_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('pos_transactions.created_at', '=', $start);
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
                        'profits' => $total-($row->pos_td_qty*$purchase),
                        'br_name' => $row->br_name,
                    );
                } else {
                    $pf[$key]['profits'] = $pf[$key]['profits'] + ($total-($row->pos_td_qty*$purchase));
                }
            }
        }
        rsort($pf);
        $r['item'] = $pf;
        return json_encode($r);
    }

    public function loadcProfit(Request $request)
    {
        $date = $request->post('dashboard_date');
        $start = null;
        $end = null;
        if (!empty($date)) {
          $exp = explode('|', $date);
          if (count($exp) > 1) {
            $start = $exp[0];
            $end = $exp[1];
          } else {
            $start = $date;
          }
        } else {
            $start = date('Y-m-d');
        }

        $pf = array();
        $profit = DB::table('pos_transaction_details')
        ->selectRaw("ts_pos_transactions.created_at as created_at, br_name, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('pos_transactions', 'pos_transaction_details.pt_id', '=', 'pos_transactions.id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->where(function ($w) use ($start, $end) {
            if (!empty($end)) {
                $w->whereDate('pos_transactions.created_at', '>=', $start)
                ->whereDate('pos_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('pos_transactions.created_at', '=', $start);
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
                        'cprofits' => $total-($row->pos_td_qty*$purchase),
                        'br_name' => $row->br_name,
                    );
                } else {
                    $pf[$key]['cprofits'] = $pf[$key]['cprofits'] + ($total-($row->pos_td_qty*$purchase));
                }
            }
        }
        rsort($pf);
        $r['item'] = $pf;
        return json_encode($r);
    }
}
