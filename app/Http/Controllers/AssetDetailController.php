<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\ProductLocation;
use App\Exports\AdExport;
use Maatwebsite\Excel\Facades\Excel;

class AssetDetailController extends Controller
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
            'st_id' => DB::table('stores')->where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
        ];
        return view('app.asset_detail.asset_detail', compact('data'));
    }

    public function loadData(Request $request) {
        $st_id = $request->post('st_id');
        $data = $request->post('data_filter');
        $article = $request->post('article_filter');
        $date = $request->post('date');

        $start = null;
        $end = null;
        $exp = explode('|', $date);
        if (count($exp) > 1) {
            if ($exp[0] != $exp[1]) {
                $start = $exp[0];
                $end = $exp[1];
            } else {
                $start = $exp[0];
            }
        } else {
            $start = $date;
        }

        $dt = [
            'start' => $start, 
            'end' => $end, 
            'st_id' => $st_id, 
        ];

        if ($data == 'brand') {
            return view('app.asset_detail._load_brand', compact('dt'));
        } else {
            if ($article == 'color') {
                return view('app.asset_detail._load_color', compact('dt'));
            } else {
                return view('app.asset_detail._load_size', compact('dt'));
            }
        }
    }

    public function exportData(Request $request) {
        $st_id = $request->get('st_id');
        $data = $request->get('data_filter');
        $article = $request->get('article_filter');
        $date = $request->get('date');
        $exp = explode('|', $date);
        $start = null;
        $end = null;
        if (!empty($exp[1])) {
            $start = $exp[0];
            $end = $exp[1];
        } else {
            $start = $request->get('date');
        }
        return Excel::download(new AdExport($st_id, $start, $end, $data, $article), 'Export Asset Detail.xlsx');
    }

    private function getBeginningQty($type, $start, $end, $id, $st_id, $s) {
        $beginning = 0;
        $end = date( "Y-m-d", strtotime($start." -1 day"));
        $start = '2021-01-01';
        $purchase = $this->getPurchase('qty', $type, $start, $end, $id, $st_id, $s);
        $sales = $this->getSales('qty', $type, $start, $end, $id, $st_id, $s);
        $trans_in = $this->getTransIn('qty', $type, $start, $end, $id, $st_id, $s);
        $trans_out = $this->getTransOut('qty', $type, $start, $end, $id, $st_id, $s);
        $gid = $this->getGID('qty', $type, $start, $end, $id, $st_id, $s);
        $beginning = $purchase - $sales + $trans_in - $trans_out - $gid;
        
        if ($beginning < 0) {
            $beginning = 0;
        }
        return $beginning;
    }

    private function getBeginningValue($type, $start, $end, $id, $st_id, $s) {
        $beginning = 0;
        $val = 0;
        $end = date( "Y-m-d", strtotime($start." -1 day"));
        $start = '2021-01-01';
        $purchase = $this->getPurchase('qty', $type, $start, $end, $id, $st_id, $s);
        $sales = $this->getSales('qty', $type, $start, $end, $id, $st_id, $s);
        $trans_in = $this->getTransIn('qty', $type, $start, $end, $id, $st_id, $s);
        $trans_out = $this->getTransOut('qty', $type, $start, $end, $id, $st_id, $s);
        $gid = $this->getGID('qty', $type, $start, $end, $id, $st_id, $s);
        $beginning = $purchase - $sales + $trans_in - $trans_out - $gid;

        $row = DB::table('purchase_order_article_detail_statuses')
        ->selectRaw("avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, p_purchase_price, ps_purchase_price")
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
        ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_Id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->where('purchase_orders.st_id', '=', $st_id)
        ->where(function($w) use ($start, $end, $type, $id) {
            if ($type == 'size') {
                $w->where('purchase_order_article_details.pst_id', '=', $id);
            } else if ($type == 'color') {
                $w->where('purchase_order_articles.p_id', '=', $id);
            } else {
                $w->where('products.br_id', '=', $id);
            }
        })
        ->groupBy('purchase_order_article_details.pst_id')
        ->first();
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
                    $purchase = 0;
                }
            }
        }
        
        if ($beginning < 0) {
            $beginning = 0;
        }

        $val = $beginning * $purchase;

        return $val;
    }

    private function getEndingQty($type, $start, $end, $id, $st_id, $s) {
        $ending = 0;
        $purchase = $this->getPurchase('qty', $type, $start, $end, $id, $st_id, $s);
        $sales = $this->getSales('qty', $type, $start, $end, $id, $st_id, $s);
        $trans_in = $this->getTransIn('qty', $type, $start, $end, $id, $st_id, $s);
        $trans_out = $this->getTransOut('qty', $type, $start, $end, $id, $st_id, $s);
        $gid = $this->getGID('qty', $type, $start, $end, $id, $st_id, $s);
        $beginning = $this->getBeginningQty($type, $start, $end, $id, $st_id, $s);
        $ending = $beginning + $purchase - $sales + $trans_in - $trans_out - $gid;
        
        if ($ending < 0) {
            $ending = 0;
        }
        return $ending;
    }

    private function getEndingValue($type, $start, $end, $id, $st_id, $s) {
        $ending = 0;
        $val = 0;
        $purchase = $this->getPurchase('qty', $type, $start, $end, $id, $st_id, $s);
        $sales = $this->getSales('qty', $type, $start, $end, $id, $st_id, $s);
        $trans_in = $this->getTransIn('qty', $type, $start, $end, $id, $st_id, $s);
        $trans_out = $this->getTransOut('qty', $type, $start, $end, $id, $st_id, $s);
        $gid = $this->getGID('qty', $type, $start, $end, $id, $st_id, $s);
        $beginning = $this->getBeginningQty($type, $start, $end, $id, $st_id, $s);
        $ending = $beginning + $purchase - $sales + $trans_in - $trans_out - $gid;

        $row = DB::table('purchase_order_article_detail_statuses')
        ->selectRaw("avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, p_purchase_price, ps_purchase_price")
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
        ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_Id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->where('purchase_orders.st_id', '=', $st_id)
        ->where(function($w) use ($start, $end, $type, $id) {
            if ($type == 'size') {
                $w->where('purchase_order_article_details.pst_id', '=', $id);
            } else if ($type == 'color') {
                $w->where('purchase_order_articles.p_id', '=', $id);
            } else {
                $w->where('products.br_id', '=', $id);
            }
        })
        ->groupBy('purchase_order_article_details.pst_id')
        ->first();
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
                    $purchase = 0;
                }
            }
        }

         
        if ($ending < 0) {
            $ending = 0;
        }
        $val = $ending * $purchase;
        
        return $val;
    }

    private function getPurchase($v, $type, $start, $end, $id, $st_id, $s) {
        $purchase_qty = 0;
        $purchase_value = 0;
        $data = DB::table('purchase_order_article_detail_statuses')
            ->selectRaw("p_color, br_name, sum(ts_purchase_order_article_detail_statuses.poads_qty) as qty, sum(ts_purchase_order_article_detail_statuses.poads_total_price) as total")
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
            ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
            ->leftJoin('products', 'products.id', '=', 'purchase_order_articles.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('purchase_orders.st_id', '=', $st_id)
            ->where(function($w) use ($start, $end, $type, $id, $s) {
                if (!empty($end)) {
                    $w->whereDate('purchase_order_article_detail_statuses.created_at', '>=', $start)
                    ->whereDate('purchase_order_article_detail_statuses.created_at', '<=', $end);
                } else {
                    $w->whereDate('purchase_order_article_detail_statuses.created_at', '=', $start);
                }
                if ($s == 'n') {
                    if ($type == 'size') {
                        $w->where('purchase_order_article_details.pst_id', '=', $id);
                    } else if ($type == 'color') {
                        $w->where('purchase_order_articles.p_id', '=', $id);
                    } else {
                        $w->where('products.br_id', '=', $id);
                    }
                }
            })
            ->groupBy('purchase_order_article_details.pst_id')
            ->get();
            $pr = array();
            if (!empty($data->first())) {
                foreach ($data as $row) {
                    if ($type == 'brand') {
                        $key = $row->br_name;
                        if (!array_key_exists($key, $pr)) {
                            $pr[$key] = array(
                                'br_name' => $row->br_name,
                                'purchase_value' => $row->total,
                                'purchase_qty' => $row->qty,
                            );
                            $purchase_value = $row->total;
                            $purchase_qty = $row->qty;
                        } else {
                            $pr[$key]['purchase_value'] = $pr[$key]['purchase_value'] + $row->total;
                            $pr[$key]['purchase_qty'] = $pr[$key]['purchase_qty'] + $row->qty;
                            $purchase_value += $row->total;
                            $purchase_qty += $row->qty;
                        }
                    } else if ($type == 'color') {
                        $key = $row->p_color;
                        if (!array_key_exists($key, $pr)) {
                            $pr[$key] = array(
                                'p_color' => $row->p_color,
                                'purchase_value' => $row->total,
                                'purchase_qty' => $row->qty,
                            );
                            $purchase_value = $row->total;
                            $purchase_qty = $row->qty;
                        } else {
                            $pr[$key]['purchase_value'] = $pr[$key]['purchase_value'] + $row->total;
                            $pr[$key]['purchase_qty'] = $pr[$key]['purchase_qty'] + $row->qty;
                            $purchase_value += $row->total;
                            $purchase_qty += $row->qty;
                        }
                    } else {
                        $purchase_value += $row->total;
                        $purchase_qty += $row->qty;
                    }
                }
            }
        if ($v == 'qty') {
            return $purchase_qty;
        } else {
            return $purchase_value;
        }
    }

    private function getSales($v, $type, $start, $end, $id, $st_id, $s) {
        $sales_qty = 0;
        $sales_value = 0;

        $nett_sales = DB::table('pos_transaction_details')
        ->select("p_color", "br_name", "pos_td_qty", "pos_td_marketplace_price", "pos_td_discount_price")
        ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
        ->where(function($w) use ($start, $end, $type, $id, $s) {
            if (!empty($end)) {
                $w->where('pos_transactions.created_at', '>=', $start)
                ->where('pos_transactions.created_at', '<=', $end);
            } else {
                $w->where('pos_transactions.created_at', '=', $start);
            }
            if ($s == 'n') {
                if ($type == 'size') {
                    $w->where('pos_transaction_details.pst_id', '=', $id);
                } else if ($type == 'color') {
                    $w
                    ->where('products.id', '=', $id);
                } else {
                    $w->where('products.br_id', '=', $id);
                }
            }
        })
        ->groupBy('pos_transaction_details.id')
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
                $qty = $row->pos_td_qty;
                if ($type == 'brand') {
                    $key = $row->br_name;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'br_name' => $row->br_name,
                            'sales_value' => $total,
                            'sales_qty' => $qty,
                        );
                        $sales_qty = $qty;
                        $sales_value = $total;
                    } else {
                        $ns[$key]['sales_value'] = $ns[$key]['sales_value'] + $total;
                        $ns[$key]['sales_qty'] = $ns[$key]['sales_qty'] + $qty;
                        $sales_qty += $qty;
                        $sales_value += $total;
                    }
                } else if ($type == 'color') {
                    $key = $row->p_color;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'p_color' => $row->p_color,
                            'sales_value' => $total,
                            'sales_qty' => $qty,
                        );
                        $sales_qty = $qty;
                        $sales_value = $total;
                    } else {
                        $ns[$key]['sales_value'] = $ns[$key]['sales_value'] + $total;
                        $ns[$key]['sales_qty'] = $ns[$key]['sales_qty'] + $qty;
                        $sales_qty += $qty;
                        $sales_value += $total;
                    }
                } else {
                    $sales_qty += $qty;
                    $sales_value += $total;
                }
                
            }
        }

        if ($v == 'qty') {
            return $sales_qty;
        } else {
            return $sales_value;
        }
    }

    private function getProfit($type, $start, $end, $id, $st_id, $s) {
        $profit_value = 0;

        $profit = DB::table('pos_transaction_details')
        ->selectRaw("p_color, br_name, ts_pos_transactions.created_at as created_at, pos_td_qty, pos_td_sell_price, pos_td_marketplace_price, pos_td_discount_price, pos_td_total_price, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'pos_transaction_details.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
        ->where(function($w) use ($start, $end, $type, $id, $s) {
            if (!empty($end)) {
                $w->where('pos_transactions.created_at', '>=', $start)
                ->where('pos_transactions.created_at', '<=', $end);
            } else {
                $w->where('pos_transactions.created_at', '=', $start);
            }
            if ($s == 'n') {
                if ($type == 'size') {
                    $w->where('pos_transaction_details.pst_id', '=', $id);
                } else if ($type == 'color') {
                    $w->where('products.id', '=', $id);
                } else {
                    $w->where('products.br_id', '=', $id);
                }
            }
        })
        ->groupBy('pos_transaction_details.id')
        ->get();
        $pr = array();
        if (!empty($profit->first())) {
            foreach ($profit as $row) {
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
                $qty = $row->pos_td_qty;
                if ($type == 'brand') {
                    $key = $row->br_name;
                    if (!array_key_exists($key, $pr)) {
                        $pr[$key] = array(
                            'br_name' => $row->br_name,
                            'profit_value' => $total-($qty*$purchase),
                        );
                        $profit_value = $total-($qty*$purchase);
                    } else {
                        $pr[$key]['profit_value'] = $pr[$key]['profit_value'] + ($total-($qty*$purchase));
                        $profit_value += $total-($qty*$purchase);
                    }
                } else if ($type == 'color') {
                    $key = $row->p_color;
                    if (!array_key_exists($key, $pr)) {
                        $pr[$key] = array(
                            'p_color' => $row->p_color,
                            'profit_value' => $total-($qty*$purchase),
                        );
                        $profit_value = $total-($qty*$purchase);
                    } else {
                        $pr[$key]['profit_value'] = $pr[$key]['profit_value'] + ($total-($qty*$purchase));
                        $profit_value += $total-($qty*$purchase);
                    }
                } else {
                    $profit_value += $total-($qty*$purchase);
                }
            }
        }
        return $profit_value;
    }

    private function getTransIn($v, $type, $start, $end, $id, $st_id, $s) {
        $trans_in_qty = 0;
        $trans_in_value = 0;
        $trans_in = DB::table('stock_transfer_detail_statuses')
        ->selectRaw("p_color, p_name, br_name, stfds_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
        ->leftJoin('stock_transfer_details', 'stock_transfer_details.id', '=', 'stock_transfer_detail_statuses.stfd_id')
        ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->where('stock_transfers.st_id_end', '=', $st_id)
        ->where(function($w) use ($start, $end, $type, $id, $s) {
            if (!empty($end)) {
                $w->whereDate('stock_transfer_detail_statuses.created_at', '>=', $start)
                ->whereDate('stock_transfer_detail_statuses.created_at', '<=', $end);
            } else {
                $w->whereDate('stock_transfer_detail_statuses.created_at', '=', $start);
            }
            if ($s == 'n') {
                if ($type == 'size') {
                    $w->where('stock_transfer_details.pst_id', '=', $id);
                } else if ($type == 'color') {
                    $w->where('product_stocks.p_id', '=', $id);
                } else {
                    $w->where('products.br_id', '=', $id);
                }
            }
        })
        ->groupBy('stock_transfer_detail_statuses.id')
        ->where('stock_transfers.stf_status', '=', '2')
        ->where('stock_transfer_details.stfd_status', '=', '1')
        ->get();
        $ns = array();
        if (!empty($trans_in->first())) {
            foreach($trans_in as $row) {
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
                $total = ($row->stfds_qty * $purchase);
                $qty = $row->stfds_qty;
                if ($type == 'brand') {
                    $key = $row->br_name;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'br_name' => $row->br_name,
                            'trans_in_value' => $total,
                            'trans_in_qty' => $qty,
                        );
                        $trans_in_qty = $qty;
                        $trans_in_value = $total;
                    } else {
                        $ns[$key]['trans_in_value'] = $ns[$key]['trans_in_value'] + $total;
                        $ns[$key]['trans_in_qty'] = $ns[$key]['trans_in_qty'] + $qty;
                        $trans_in_qty += $qty;
                        $trans_in_value += $total;
                    }
                } else if ($type == 'color') {
                    $key = $row->p_color;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'p_color' => $row->p_color,
                            'trans_in_value' => $total,
                            'trans_in_qty' => $qty,
                        );
                        $trans_in_qty = $qty;
                        $trans_in_value = $total;
                    } else {
                        $ns[$key]['trans_in_value'] = $ns[$key]['trans_in_value'] + $total;
                        $ns[$key]['trans_in_qty'] = $ns[$key]['trans_in_qty'] + $qty;
                        $trans_in_qty += $qty;
                        $trans_in_value += $total;
                    }
                } else {
                    $trans_in_qty += $qty;
                    $trans_in_value += $total;
                }
            }
        }

        if ($v == 'qty') {
            return $trans_in_qty;
        } else {
            return $trans_in_value;
        }
    }

    private function getTransOut($v, $type, $start, $end, $id, $st_id, $s) {
        $trans_out_qty = 0;
        $trans_out_value = 0;
        $trans_out = DB::table('stock_transfer_details')
        ->selectRaw("p_color, p_name, br_name, stfd_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->where('stock_transfers.st_id_start', '=', $st_id)
        ->where(function($w) use ($start, $end, $type, $id, $s) {
            if (!empty($end)) {
                $w->whereDate('stock_transfer_details.created_at', '>=', $start)
                ->whereDate('stock_transfer_details.created_at', '<=', $end);
            } else {
                $w->whereDate('stock_transfer_details.created_at', '=', $start);
            }
            if ($s == 'n') {
                if ($type == 'size') {
                    $w->where('stock_transfer_details.pst_id', '=', $id);
                } else if ($type == 'color') {
                    $w->where('product_stocks.p_id', '=', $id);
                } else {
                    $w->where('products.br_id', '=', $id);
                }
            }
        })
        ->groupBy('stock_transfer_details.id')
        ->where('stock_transfers.stf_status', '!=', '3')
        ->where('stock_transfer_details.stfd_status', '=', '1')
        ->get();
        $ns = array();
        if (!empty($trans_out->first())) {
            foreach($trans_out as $row) {
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
                $total = ($row->stfd_qty * $purchase);
                $qty = $row->stfd_qty;
                if ($type == 'brand') {
                    $key = $row->br_name;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'br_name' => $row->br_name,
                            'trans_out_value' => $total,
                            'trans_out_qty' => $qty,
                        );
                        $trans_out_qty = $qty;
                        $trans_out_value = $total;
                    } else {
                        $ns[$key]['trans_out_value'] = $ns[$key]['trans_out_value'] + $total;
                        $ns[$key]['trans_out_qty'] = $ns[$key]['trans_out_qty'] + $qty;
                        $trans_out_qty += $qty;
                        $trans_out_value += $total;
                    }
                } else if ($type == 'color') {
                    $key = $row->p_color;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'p_color' => $row->p_color,
                            'trans_out_value' => $total,
                            'trans_out_qty' => $qty,
                        );
                        $trans_out_qty = $qty;
                        $trans_out_value = $total;
                    } else {
                        $ns[$key]['trans_out_value'] = $ns[$key]['trans_out_value'] + $total;
                        $ns[$key]['trans_out_qty'] = $ns[$key]['trans_out_qty'] + $qty;
                        $trans_out_qty += $qty;
                        $trans_out_value += $total;
                    }
                } else {
                    $trans_out_qty += $qty;
                    $trans_out_value += $total;
                }
            }
        }

        if ($v == 'qty') {
            return $trans_out_qty;
        } else {
            return $trans_out_value;
        }
    }

    private function getGID($v, $type, $start, $end, $id, $st_id, $s) {
        $gid_qty = 0;
        $gid_value = 0;

        $gid = DB::table('stock_transfer_details')
        ->selectRaw("p_color, p_name, br_name, stfd_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->where('stock_transfers.st_id_start', '=', $st_id)
        ->where(function($w) use ($start, $end, $type, $id, $s) {
            if (!empty($end)) {
                $w->whereDate('stock_transfer_details.created_at', '>=', $start)
                ->whereDate('stock_transfer_details.created_at', '<=', $end);
            } else {
                $w->whereDate('stock_transfer_details.created_at', '=', $start);
            }
            if ($s == 'n') {
                if ($type == 'size') {
                    $w->where('stock_transfer_details.pst_id', '=', $id);
                } else if ($type == 'color') {
                    $w->where('product_stocks.p_id', '=', $id);
                } else {
                    $w->where('products.br_id', '=', $id);
                }
            }
        })
        ->groupBy('stock_transfer_details.id')
        ->where('stock_transfers.stf_status', '=', '3')
        ->get();
        $ns = array();
        if (!empty($gid->first())) {
            foreach($gid as $row) {
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
                $total = ($row->stfd_qty * $purchase);
                $qty = $row->stfd_qty;
                if ($type == 'brand') {
                    $key = $row->br_name;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'br_name' => $row->br_name,
                            'gid_value' => $total,
                            'gid_qty' => $qty,
                        );
                        $gid_qty = $qty;
                        $gid_value = $total;
                    } else {
                        $ns[$key]['gid_value'] = $ns[$key]['gid_value'] + $total;
                        $ns[$key]['gid_qty'] = $ns[$key]['gid_qty'] + $qty;
                        $gid_qty += $qty;
                        $gid_value += $total;
                    }
                } else if ($type == 'color') {
                    $key = $row->p_color;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'p_color' => $row->p_color,
                            'gid_value' => $total,
                            'gid_qty' => $qty,
                        );
                        $gid_qty = $qty;
                        $gid_value = $total;
                    } else {
                        $ns[$key]['gid_value'] = $ns[$key]['gid_value'] + $total;
                        $ns[$key]['gid_qty'] = $ns[$key]['gid_qty'] + $qty;
                        $gid_qty += $qty;
                        $gid_value += $total;
                    }
                } else {
                    $gid_qty += $qty;
                    $gid_value += $total;
                }
            }
        }

        if ($v == 'qty') {
            return $gid_qty;
        } else {
            return $gid_value;
        }
    }

    private function getGIT($v, $type, $start, $end, $id, $st_id, $s) {
        $git_qty = 0;
        $git_value = 0;
        $gt = DB::table('stock_transfer_details')
        ->selectRaw("p_color, p_name, br_name, ts_stock_transfer_details.id as id, stfd_qty, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, poad_total_price, poad_qty, ps_purchase_price, p_purchase_price")
        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
        ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->where('stock_transfers.st_id_start', '=', $st_id)
        ->where(function($w) use ($start, $end, $type, $id, $s) {
            if (!empty($end)) {
                $w->whereDate('stock_transfer_details.created_at', '>=', $start)
                ->whereDate('stock_transfer_details.created_at', '<=', $end);
            } else {
                $w->whereDate('stock_transfer_details.created_at', '=', $start);
            }
            if ($s == 'n') {
                if ($type == 'size') {
                    $w->where('stock_transfer_details.pst_id', '=', $id);
                } else if ($type == 'color') {
                    $w->where('product_stocks.p_id', '=', $id);
                } else {
                    $w->where('products.br_id', '=', $id);
                }
            }
        })
        ->groupBy('stock_transfer_details.id')
        ->where('stock_transfers.stf_status', '=', '1')
        ->where('stock_transfer_details.stfd_status', '=', '1')
        ->get();
        $ns = array();
        if (!empty($gt->first())) {
            foreach ($gt as $row) {
                $trans_qty = $row->stfd_qty;
                $receive_qty = DB::table('stock_transfer_detail_statuses')->where('stfd_id', '=', $row->id)->sum('stfds_qty');
                if ($receive_qty > $trans_qty) {
                    $receive_qty = $trans_qty;
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
                $total = ($trans_qty - $receive_qty) * $purchase;
                $qty = $trans_qty - $receive_qty;

                if ($type == 'brand') {
                    $key = $row->br_name;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'br_name' => $row->br_name,
                            'git_value' => $total,
                            'git_qty' => $qty,
                        );
                        $git_qty = $qty;
                        $git_value = $total;
                    } else {
                        $ns[$key]['git_value'] = $ns[$key]['git_value'] + $total;
                        $ns[$key]['git_qty'] = $ns[$key]['git_qty'] + $qty;
                        $git_qty += $qty;
                        $git_value += $total;
                    }
                } else if ($type == 'color') {
                    $key = $row->p_color;
                    if (!array_key_exists($key, $ns)) {
                        $ns[$key] = array(
                            'p_color' => $row->p_color,
                            'git_value' => $total,
                            'git_qty' => $qty,
                        );
                        $git_qty = $qty;
                        $git_value = $total;
                    } else {
                        $ns[$key]['git_value'] = $ns[$key]['git_value'] + $total;
                        $ns[$key]['git_qty'] = $ns[$key]['git_qty'] + $qty;
                        $git_qty += $qty;
                        $git_value += $total;
                    }
                } else {
                    $git_qty += $qty;
                    $git_value += $total;
                }
            }
        }

        if ($v == 'qty') {
            return $git_qty;
        } else {
            return $git_value;
        }
    }

    public function getSummary(Request $request) {
        $st_id = $request->post('st_id');
        $start = $request->post('start');
        $end = $request->post('end');   
        $beginning_qty = $this->getBeginningQty('', $start, $end, '', $st_id, 'y');
        $beginning_value = $this->getBeginningValue('', $start, $end, '', $st_id, 'y');
        $purchase_qty = $this->getPurchase('qty', '', $start, $end, '', $st_id, 'y');
        $purchase_value = $this->getPurchase('value', '', $start, $end, '', $st_id, 'y');
        $sales_qty = $this->getSales('qty', '', $start, $end, '', $st_id, 'y');
        $sales_value = $this->getSales('value', '', $start, $end, '', $st_id, 'y');
        $profit_qty = $sales_qty;
        $profit_value = $this->getProfit('', $start, $end, '', $st_id, 'y');
        $ending_qty = $this->getEndingQty('', $start, $end, '', $st_id, 'y');
        $ending_value = $this->getEndingValue('', $start, $end, '', $st_id, 'y');
        $cogs_qty = 0;
        if (!empty($profit_value) AND !empty($sales_value)) {
            $cogs_qty = ($profit_value/$sales_value)*100;
        }
        $cogs_value = $sales_value-$profit_value;
        $transin_qty = $this->getTransIn('qty', '', $start, $end, '', $st_id, 'y');
        $transin_value = $this->getTransIn('value', '', $start, $end, '', $st_id, 'y');
        $transout_qty = $this->getTransOut('qty', '', $start, $end, '', $st_id, 'y');
        $transout_value = $this->getTransOut('value', '', $start, $end, '', $st_id, 'y');
        $gid_qty = $this->getGID('qty', '', $start, $end, '', $st_id, 'y');
        $gid_value = $this->getGID('value', '', $start, $end, '', $st_id, 'y');
        $git_qty = $this->getGIT('qty', '', $start, $end, '', $st_id, 'y');
        $git_value = $this->getGIT('value', '', $start, $end, '', $st_id, 'y');

        $r['beginning_qty'] = number_format($beginning_qty);
        $r['beginning_value'] = number_format($beginning_value);
        $r['cogs_qty'] = number_format($cogs_qty);
        $r['cogs_value'] = number_format($cogs_value);
        $r['purchase_qty'] = number_format($purchase_qty);
        $r['purchase_value'] = number_format($purchase_value);
        $r['sales_qty'] = number_format($sales_qty);
        $r['sales_value'] = number_format($sales_value);
        $r['profit_qty'] = number_format($sales_qty);
        $r['profit_value'] = number_format($profit_value);
        $r['ending_qty'] = number_format($ending_qty);
        $r['ending_value'] = number_format($ending_value);
        $r['transin_qty'] = number_format($transin_qty);
        $r['transin_value'] = number_format($transin_value);
        $r['transout_qty'] = number_format($transout_qty);
        $r['transout_value'] = number_format($transout_value);
        $r['gid_qty'] = number_format($gid_qty);
        $r['gid_value'] = number_format($gid_value);
        $r['git_qty'] = number_format($git_qty);
        $r['git_value'] = number_format($git_value);
        $r['status'] = 200;
        return json_encode($r);
    }

    public function getSizeDatatables(Request $request)
    {
        $st_id = $request->get('st_id');
        $start = $request->get('starts');
        $end = $request->get('ends');
        $type = 'size';

        if(request()->ajax()) {
            return datatables()->of(DB::table('product_stocks')
            ->select('product_stocks.id', 'br_name', 'p_name', 'p_color', 'sz_name')
            ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('product_locations.st_id', '=', $st_id)
            ->groupBy('product_stocks.id'))
            ->editColumn('beginning', function($d) use ($start, $end, $st_id, $type) {
                return $this->getBeginningQty($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('beginning_value', function($d) use ($start, $end, $st_id, $type) {
                return $this->getBeginningValue($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('purchase_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getPurchase('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('purchase', function($d) use ($start, $end, $st_id, $type) {
                return $this->getPurchase('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transin_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransIn('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transin', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransIn('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('gid_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGID('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('gid', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGID('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('git_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGIT('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('git', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGIT('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transout_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransOut('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transout', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransOut('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('sales_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getSales('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('sales', function($d) use ($start, $end, $st_id, $type) {
                return $this->getSales('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('profit', function($d) use ($start, $end, $st_id, $type) {
                return $this->getProfit($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('cogs', function($d) use ($start, $end, $st_id, $type) {
                $sales = $this->getSales('value', $type, $start, $end, $d->id, $st_id, 'n');
                $profit = $this->getProfit($type, $start, $end, $d->id, $st_id, 'n');
                $cogs = $sales - $profit;
                return $cogs;
            })
            ->editColumn('ending_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getEndingQty($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('ending', function($d) use ($start, $end, $st_id, $type) {
                return $this->getEndingValue($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw("CONCAT(br_name,' ',p_name,' ',p_color,' ',sz_name) LIKE ?", "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getColorDatatables(Request $request)
    {
        $st_id = $request->get('st_id');
        $start = $request->get('starts');
        $end = $request->get('ends');
        $type = 'color';

        if(request()->ajax()) {
            return datatables()->of(DB::table('products')
            ->select('products.id', 'br_name', 'p_name', 'p_color')
            ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
            ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('product_locations.st_id', '=', $st_id)
            ->groupBy('products.id'))
            ->editColumn('beginning', function($d) use ($start, $end, $st_id, $type) {
                return $this->getBeginningQty($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('beginning_value', function($d) use ($start, $end, $st_id, $type) {
                return $this->getBeginningValue($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('purchase_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getPurchase('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('purchase', function($d) use ($start, $end, $st_id, $type) {
                return $this->getPurchase('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transin_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransIn('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transin', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransIn('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('gid_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGID('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('gid', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGID('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('git_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGIT('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('git', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGIT('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transout_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransOut('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transout', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransOut('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('sales_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getSales('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('sales', function($d) use ($start, $end, $st_id, $type) {
                return $this->getSales('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('profit', function($d) use ($start, $end, $st_id, $type) {
                return $this->getProfit($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('cogs', function($d) use ($start, $end, $st_id, $type) {
                $sales = $this->getSales('value', $type, $start, $end, $d->id, $st_id, 'n');
                $profit = $this->getProfit($type, $start, $end, $d->id, $st_id, 'n');
                $cogs = $sales - $profit;
                return $cogs;
            })
            ->editColumn('ending_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getEndingQty($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('ending', function($d) use ($start, $end, $st_id, $type) {
                return $this->getEndingValue($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw("CONCAT(br_name,' ',p_name,' ',p_color) LIKE ?", "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getBrandDatatables(Request $request)
    {
        $st_id = $request->get('st_id');
        $start = $request->get('starts');
        $end = $request->get('ends');
        $type = 'brand';

        if(request()->ajax()) {
            return datatables()->of(DB::table('brands')
            ->select('brands.id', 'br_name')
            ->leftJoin('products', 'products.br_id', '=', 'brands.id')
            ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
            ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->where('product_locations.st_id', '=', $st_id)
            ->groupBy('brands.id'))
            ->editColumn('beginning', function($d) use ($start, $end, $st_id, $type) {
                return $this->getBeginningQty($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('beginning_value', function($d) use ($start, $end, $st_id, $type) {
                return $this->getBeginningValue($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('purchase_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getPurchase('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transin_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransIn('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transin', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransIn('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('gid_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGID('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('gid', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGID('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('git_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGIT('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('git', function($d) use ($start, $end, $st_id, $type) {
                return $this->getGIT('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transout_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransOut('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('transout', function($d) use ($start, $end, $st_id, $type) {
                return $this->getTransOut('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('purchase', function($d) use ($start, $end, $st_id, $type) {
                return $this->getPurchase('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('sales_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getSales('qty', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('sales', function($d) use ($start, $end, $st_id, $type) {
                return $this->getSales('value', $type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('profit', function($d) use ($start, $end, $st_id, $type) {
                return $this->getProfit($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('cogs', function($d) use ($start, $end, $st_id, $type) {
                $sales = $this->getSales('value', $type, $start, $end, $d->id, $st_id, 'n');
                $profit = $this->getProfit($type, $start, $end, $d->id, $st_id, 'n');
                $cogs = $sales - $profit;
                return $cogs;
            })
            ->editColumn('ending_qty', function($d) use ($start, $end, $st_id, $type) {
                return $this->getEndingQty($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->editColumn('ending', function($d) use ($start, $end, $st_id, $type) {
                return $this->getEndingValue($type, $start, $end, $d->id, $st_id, 'n');
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw("CONCAT(br_name) LIKE ?", "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }
}
