<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\ExceptionLocation;

class ArticleInformationController extends Controller
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
        dd("Menu ini dinonaktifkan karena kurang akurat, silahkan coba Laporan Stok yang lebih akurat");
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
            'pc_id' => DB::table('product_categories')->where('pc_delete', '!=', '1')->orderByDesc('id')->pluck('pc_name', 'id'),
            'st_id' => DB::table('stores')->where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
        ];
        return view('app.report.article_report.article_report', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('article_reports')->selectRaw('ts_article_reports.id as id, ts_article_reports.pst_id as pst_id, ts_article_reports.created_at as created_at, st_name, br_name, p_name, p_color, sz_name, sum(buy) as buy, sum(sales) as sales, sum(transfer) as transfer, sum(transfer_in) as transfer_in, sum(adjustment) as adjustment, sum(refund) as refund, hpp, hj')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'article_reports.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('stores', 'stores.id', '=', 'article_reports.st_id')
            ->groupBy('article_reports.pst_id')
            ->where(function($w) use ($request) {
                if (!empty($request->get('pc_id'))) {
                    $w->where('products.pc_id', '=', $request->get('pc_id'));
                }
                if (!empty($request->get('st_id'))) {
                    $w->where('article_reports.st_id', '=', $request->get('st_id'));
                } else {
                    $w->where('article_reports.st_id', '=', '$%^');
                }
                if (!empty($request->get('report_date'))) {
                    $exp = explode('|', $request->get('report_date'));
                    $start = '';
                    $end = '';
                    if (count($exp) > 1) {
                        $start = $exp[0];
                        $end = $exp[1];
                        $w->whereDate('article_reports.created_at', '>=', $start)
                        ->whereDate('article_reports.created_at', '<=', $end);
                    } else {
                        $start = $request->get('report_date');
                        $w->whereDate('article_reports.created_at', '=', $start);
                    }
                }
            }))
            ->editColumn('past_stock', function($data) use ($request){
                if (!empty($request->get('report_date'))) {
                    $exp = explode('|', $request->get('report_date'));
                    $start = '';
                    $end = '';
                    $stock = '';
                    if (count($exp) > 1) {
                        $start = $exp[0];
                        $end = $exp[1];
                        $get = DB::table('article_reports')->selectRaw('stock')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'article_reports.pst_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->where('article_reports.pst_id', '=', $data->pst_id)
                        ->where(function($w) use ($request) {
                            if (!empty($request->get('pc_id'))) {
                                $w->where('products.pc_id', '=', $request->get('pc_id'));
                            }
                            if (!empty($request->get('st_id'))) {
                                $w->where('article_reports.st_id', '=', $request->get('st_id'));
                            }
                        })
                        ->whereDate('article_reports.created_at', '=', date('Y-m-d', strtotime('-1 day', strtotime($start))))->get()->first();
                        if (!empty($get)) {
                            $stock = $get->stock;
                        }
                    } else {
                        $get = DB::table('article_reports')->selectRaw('stock')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'article_reports.pst_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->where('article_reports.pst_id', '=', $data->pst_id)
                        ->where(function($w) use ($request) {
                            if (!empty($request->get('pc_id'))) {
                                $w->where('products.pc_id', '=', $request->get('pc_id'));
                            }
                            if (!empty($request->get('st_id'))) {
                                $w->where('article_reports.st_id', '=', $request->get('st_id'));
                            }
                        })
                        ->whereDate('article_reports.created_at', '=', date('Y-m-d', strtotime('-1 day', strtotime($request->get('report_date')))))->get()->first();
                        if (!empty($get)) {
                            $stock = $get->stock;
                        }
                    }
                    return $stock;
                }
            })
            ->editColumn('stock', function($data) use ($request){
                if (!empty($request->get('report_date'))) {
                    $exp = explode('|', $request->get('report_date'));
                    $start = '';
                    $end = '';
                    $stock = '';
                    if (count($exp) > 1) {
                        $start = $exp[0];
                        $end = $exp[1];
                        $get = DB::table('article_reports')->selectRaw('stock')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'article_reports.pst_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->where('article_reports.pst_id', '=', $data->pst_id)
                        ->where(function($w) use ($request) {
                            if (!empty($request->get('pc_id'))) {
                                $w->where('products.pc_id', '=', $request->get('pc_id'));
                            }
                            if (!empty($request->get('st_id'))) {
                                $w->where('article_reports.st_id', '=', $request->get('st_id'));
                            }
                        })
                        ->whereDate('article_reports.created_at', '=', $end)->get()->first();
                        if (!empty($get)) {
                            $stock = $get->stock;
                        }
                    } else {
                        $get = DB::table('article_reports')->selectRaw('stock')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'article_reports.pst_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->where('article_reports.pst_id', '=', $data->pst_id)
                        ->where(function($w) use ($request) {
                            if (!empty($request->get('pc_id'))) {
                                $w->where('products.pc_id', '=', $request->get('pc_id'));
                            }
                            if (!empty($request->get('st_id'))) {
                                $w->where('article_reports.st_id', '=', $request->get('st_id'));
                            }
                        })
                        ->whereDate('article_reports.created_at', '=', $request->get('report_date'))->get()->first();
                        if (!empty($get)) {
                            $stock = $get->stock;
                        }
                    }
                    return $stock;
                }
            })
            ->editColumn('profit', function($data){
                $profit = null;
                $profit = $data->sales * ($data->hj - $data->hpp);
                return $profit;
            })
            ->editColumn('created_at', function($data){
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getHistoryDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('a_i_histories')->select('a_i_histories.id as id', 'u_name', 'activity', 'a_i_histories.created_at')
            ->leftJoin('users', 'users.id', '=', 'a_i_histories.u_id'))
            ->editColumn('created_show', function($data){
                return date('d/m/Y H:i:s', strtotime($data->created_at));
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('u_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function updateData(Request $request)
    {
        $st_id = $request->post('st_id');
        $this->deleteCurrent($st_id);
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();
        $data = DB::table('product_stocks')->selectRaw("ts_product_stocks.id as pst_id, br_name, p_name, p_color, sz_name, sum(ts_product_location_setups.pls_qty) as stock")
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
        ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->whereNotIn('product_locations.pl_code', $exception)
        ->where('product_location_setups.pls_qty', '>=', '0')
        ->where('product_locations.st_id', '=', $st_id)
        ->groupBy('product_stocks.id')->get();
        if (!empty($data)) {
            $insert = array();
            foreach ($data as $row) {
                $sales = 0;
                $buy = 0;
                $refund = 0;
                $transfer = 0;
                $transfer_in = 0;
                $adjustment = 0;
                $hpp = 0;
                $hj = 0;

                $buy = DB::table('purchase_order_article_detail_statuses')->select('poads_qty')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
                ->where('purchase_order_article_details.pst_id', '=', $row->pst_id)
                ->where('purchase_orders.st_id', '=', $st_id)
                ->whereDate('purchase_order_article_detail_statuses.created_at', '=', date('Y-m-d'))
                ->sum('poads_qty');

                $poads = DB::table('purchase_order_article_detail_statuses')
                ->select('poads_purchase_price', 'ps_purchase_price')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                ->where('product_stocks.id', '=', $row->pst_id)
                ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                ->orderByDesc('purchase_order_article_detail_statuses.id')
                ->groupBy('poads_purchase_price')
                ->get()->first();
                if (!empty($poads)) {
                    if (!empty($poads->poads_purchase_price)) {
                    $hpp = $poads->poads_purchase_price;
                    } else {
                    $hpp = $poads->ps_purchase_price;
                    }
                }

                $adjust = DB::table('bin_adjustments')->select('ba_adjust', 'ba_adjust_type')
                ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'bin_adjustments.pls_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_location_setups.pst_id', '=', $row->pst_id)
                ->where('product_locations.st_id', '=', $st_id)
                ->whereDate('bin_adjustments.created_at', '=', date('Y-m-d'))
                ->whereNotNull('ba_adjust_type')
                ->get();
                if (!empty($adjust)) {
                    foreach ($adjust as $adj) {
                        if ($adj->ba_adjust_type == '+') {
                            $adjustment += $adj->ba_adjust;
                        } else {
                            if ($adjustment )
                            $adjustment -= $adj->ba_adjust;
                        }
                    }
                }

                $transfer = DB::table('stock_transfer_details')->select('stfd_qty')
                ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
                ->where('stock_transfers.st_id_start', '=', $st_id)
                ->where('stock_transfer_details.pst_id', '=', $row->pst_id)
                ->whereDate('stock_transfer_details.created_at', '=', date('Y-m-d'))
                ->sum('stfd_qty');

                $transfer_in = DB::table('stock_transfer_detail_statuses')->select('stfds_qty')
                ->leftJoin('stock_transfer_details', 'stock_transfer_details.id', '=', 'stock_transfer_detail_statuses.stfd_id')
                ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
                ->where('stock_transfers.st_id_end', '=', $st_id)
                ->where('stock_transfer_details.pst_id', '=', $row->pst_id)
                ->whereDate('stock_transfer_detail_statuses.created_at', '=', date('Y-m-d'))
                ->sum('stfds_qty');
                
                $sales = DB::table('product_location_setup_transactions')->select('plst_qty')
                ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_location_setups.pst_id', '=', $row->pst_id)
                ->where('product_locations.st_id', '=', $st_id)
                ->whereIn('product_location_setup_transactions.plst_status', ['DONE', 'WAITING ONLINE', 'WAITING FOR PACKING', 'WAITING FOR NAMESET'])
                ->whereNotNull('product_location_setup_transactions.pt_id')
                ->whereDate('product_location_setup_transactions.created_at', '=', date('Y-m-d'))
                ->sum('plst_qty');

                $hj = DB::table('product_stocks')->select('ps_price_tag', 'p_price_tag', 'ps_sell_price', 'p_sell_price')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->where('product_stocks.id', '=', $row->pst_id)
                ->get()->first();
                if ($st_id == '5') {
                    if (!empty($hj->ps_price_tag)) {
                        $hj = $hj->ps_price_tag;
                    } else {
                        $hj = $hj->p_price_tag;
                    }
                } else {
                    if (!empty($hj->ps_sell_price)) {
                        $hj = $hj->ps_sell_price;
                    } else {
                        $hj = $hj->p_sell_price;
                    }
                }

                $refund = DB::table('product_location_setup_transactions')->select('plst_qty')
                ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_location_setups.pst_id', '=', $row->pst_id)
                ->where('product_locations.st_id', '=', $st_id)
                ->whereIn('product_location_setup_transactions.plst_status', ['INSTOCK', 'EXCHANGE', 'REFUND'])
                ->whereNotNull('product_location_setup_transactions.pt_id')
                ->whereDate('product_location_setup_transactions.created_at', '=', date('Y-m-d'))
                ->sum('plst_qty');

                $past_stock = 0;
                $stock = 0;
                $d_past_stock = DB::table('article_reports')->selectRaw('sum(stock) as stock')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'article_reports.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->where('article_reports.pst_id', '=', $row->pst_id)
                ->where('article_reports.st_id', '=', $st_id)
                ->whereDate('article_reports.created_at', '=', date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))))
                ->get()->first();
                if (!empty ($d_past_stock)) {
                    $past_stock = $d_past_stock->stock;
                }
                if (!empty($past_stock)) {
                    $stock = $past_stock - $sales + $buy + $refund - $transfer + $transfer_in + $adjustment;
                } else {
                    $stock = $row->stock;
                }
                if ($stock < 0) {
                    $stock = 0;
                }

                $insert[] = [
                    'pst_id' => $row->pst_id,
                    'st_id' => $st_id,
                    'sales' => $sales,
                    'buy' => $buy,
                    'refund' => $refund,
                    'transfer' => $transfer,
                    'transfer_in' => $transfer_in,
                    'adjustment' => $adjustment,
                    'hpp' => $hpp,
                    'hj' => $hj,
                    'stock' => $stock,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                if (count($insert) == 200) {
                    $update = DB::table('article_reports')->insert($insert);
                    $insert = array();
                }
            }
            if (!empty($update)) {
                $this->updateHistory();
                $r['status'] = '200';
            } else {
                $r['status'] = '400';
            }
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function updateDailyData(Request $request)
    {
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();
    }

    private function deleteCurrent($st_id)
    {   
        DB::table('article_reports')
        ->where('st_id', '=', $st_id)
        ->whereDate('created_at', '=', date('Y-m-d'))->delete();
    }

    private function updateHistory()
    {
        DB::table('a_i_histories')->insert([
            'u_id' => Auth::user()->id,
            'activity' => 'Melakukan request data laporan stock terbaru',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function updateHistoryAuto()
    {
        DB::table('a_i_histories')->insert([
            'u_id' => '61',
            'activity' => 'Mengupdate data laporan stock terbaru',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function autoUpdateArticleInformation()
    {
        $st_id_data = DB::table('stores')->select('id')->get();
        $exception = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();
        $st_id = null;
        if (!empty($st_id_data)) {
            foreach ($st_id_data as $strow) {
                $st_id = $strow->id;
                $this->deleteCurrent($st_id);
                $data = DB::table('product_stocks')->selectRaw("ts_product_stocks.id as pst_id, br_name, p_name, p_color, sz_name, sum(ts_product_location_setups.pls_qty) as stock")
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->whereNotIn('product_locations.pl_code', $exception)
                ->where('product_location_setups.pls_qty', '>=', '0')
                ->where('product_locations.st_id', '=', $st_id)
                ->groupBy('product_stocks.id')->get();
                if (!empty($data)) {
                    $insert = array();
                    foreach ($data as $row) {
                        $sales = 0;
                        $buy = 0;
                        $refund = 0;
                        $transfer = 0;
                        $transfer_in = 0;
                        $adjustment = 0;
                        $hpp = 0;
                        $hj = 0;

                        $buy = DB::table('purchase_order_article_detail_statuses')->select('poads_qty')
                        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                        ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                        ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
                        ->where('purchase_order_article_details.pst_id', '=', $row->pst_id)
                        ->where('purchase_orders.st_id', '=', $st_id)
                        ->whereDate('purchase_order_article_detail_statuses.created_at', '=', date('Y-m-07'))
                        ->sum('poads_qty');

                        $hpp = DB::table('purchase_order_article_detail_statuses')
                        ->select('poads_purchase_price', 'ps_purchase_price')
                        ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                        ->where('purchase_order_article_details.pst_id', '=', $row->pst_id)
                        ->whereNotNull('purchase_order_article_detail_statuses.poads_purchase_price')
                        ->get()->first();
                        if (!empty($hpp)) {
                            if (!empty($hpp->poads_purchase_price)) {
                            $hpp = $hpp->poads_purchase_price;
                            } else {
                            $hpp = $hpp->ps_purchase_price;
                            }
                        }

                        $adjust = DB::table('bin_adjustments')->select('ba_adjust', 'ba_adjust_type')
                        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'bin_adjustments.pls_id')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('product_location_setups.pst_id', '=', $row->pst_id)
                        ->where('product_locations.st_id', '=', $st_id)
                        ->whereDate('bin_adjustments.created_at', '=', date('Y-m-07'))
                        ->whereNotNull('ba_adjust_type')
                        ->get();
                        if (!empty($adjust)) {
                            foreach ($adjust as $adj) {
                                $adjustment = 0;
                                if ($adj->ba_adjust_type == '+') {
                                    $adjustment += $adj->ba_adjust;
                                } else {
                                    if ($adjustment )
                                    $adjustment -= $adj->ba_adjust;
                                }
                            }
                        }
                        $transfer = DB::table('stock_transfer_details')->select('stfd_qty')
                        ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
                        ->where('stock_transfers.st_id_start', '=', $st_id)
                        ->where('stock_transfer_details.pst_id', '=', $row->pst_id)
                        ->whereDate('stock_transfer_details.created_at', '=', date('Y-m-07'))
                        ->sum('stfd_qty');

                        $transfer_in = DB::table('stock_transfer_detail_statuses')->select('stfds_qty')
                        ->leftJoin('stock_transfer_details', 'stock_transfer_details.id', '=', 'stock_transfer_detail_statuses.stfd_id')
                        ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
                        ->where('stock_transfers.st_id_end', '=', $st_id)
                        ->where('stock_transfer_details.pst_id', '=', $row->pst_id)
                        ->whereDate('stock_transfer_detail_statuses.created_at', '=', date('Y-m-07'))
                        ->sum('stfds_qty');
                        
                        $sales = DB::table('product_location_setup_transactions')->select('plst_qty')
                        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('product_location_setups.pst_id', '=', $row->pst_id)
                        ->where('product_locations.st_id', '=', $st_id)
                        ->whereIn('product_location_setup_transactions.plst_status', ['DONE', 'WAITING ONLINE', 'WAITING FOR PACKING', 'WAITING FOR NAMESET'])
                        ->whereNotNull('product_location_setup_transactions.pt_id')
                        ->whereDate('product_location_setup_transactions.created_at', '=', date('Y-m-07'))
                        ->sum('plst_qty');

                        $hj = DB::table('product_stocks')->select('ps_price_tag', 'p_price_tag', 'ps_sell_price', 'p_sell_price')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->where('product_stocks.id', '=', $row->pst_id)
                        ->get()->first();
                        if ($st_id == '5') {
                            if (!empty($hj->ps_price_tag)) {
                                $hj = $hj->ps_price_tag;
                            } else {
                                $hj = $hj->p_price_tag;
                            }
                        } else {
                            if (!empty($hj->ps_sell_price)) {
                                $hj = $hj->ps_sell_price;
                            } else {
                                $hj = $hj->p_sell_price;
                            }
                        }

                        $refund = DB::table('product_location_setup_transactions')->select('plst_qty')
                        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
                        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('product_location_setups.pst_id', '=', $row->pst_id)
                        ->where('product_locations.st_id', '=', $st_id)
                        ->whereIn('product_location_setup_transactions.plst_status', ['INSTOCK', 'EXCHANGE', 'REFUND'])
                        ->whereNotNull('product_location_setup_transactions.pt_id')
                        ->whereDate('product_location_setup_transactions.created_at', '=', date('Y-m-07'))
                        ->sum('plst_qty');

                        $past_stock = 0;
                        $stock = 0;
                        $d_past_stock = DB::table('article_reports')->selectRaw('sum(stock) as stock')
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'article_reports.pst_id')
                        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                        ->where('article_reports.pst_id', '=', $row->pst_id)
                        ->where('article_reports.st_id', '=', $st_id)
                        ->whereDate('article_reports.created_at', '=', date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-07')))))
                        ->get()->first();
                        if (!empty ($d_past_stock)) {
                            $past_stock = $d_past_stock->stock;
                        }
                        if (!empty($past_stock)) {
                            $stock = $past_stock - $sales + $buy + $refund - $transfer + $transfer_in + $adjustment;
                        } else {
                            $stock = $row->stock;
                        }
                        if ($stock < 0) {
                           $stock = 0;
                        }

                        $insert[] = [
                            'pst_id' => $row->pst_id,
                            'st_id' => $st_id,
                            'sales' => $sales,
                            'buy' => $buy,
                            'refund' => $refund,
                            'transfer' => $transfer,
                            'transfer_in' => $transfer_in,
                            'adjustment' => $adjustment,
                            'hpp' => $hpp,
                            'hj' => $hj,
                            'stock' => $stock,
                            'created_at' => date('Y-m-07 H:i:s'),
                            'updated_at' => date('Y-m-07 H:i:s'),
                        ];
                        if (count($insert) == 200) {
                            $update = DB::table('article_reports')->insert($insert);
                            $insert = array();
                        }
                    }
                    if (!empty($update)) {
                        $this->updateHistoryAuto();
                        $r['status'] = '200';
                    } else {
                        $r['status'] = '400';
                    }
                } else {
                    $r['status'] = '400';
                }
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function getAutoUpdateArticleInformation()
    {
        return "waiting for fix";
    }
}