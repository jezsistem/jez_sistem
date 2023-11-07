<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\ExceptionLocation;
use App\Models\ProductLocation;
use App\Exports\StockCardExport;
use Maatwebsite\Excel\Facades\Excel;

class StockCardController extends Controller
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
            'br_id' => DB::table('brands')->where('br_delete', '!=', '1')->orderBy('br_name')->pluck('br_name', 'id'),
            'pc_id' => DB::table('product_categories')->where('pc_delete', '!=', '1')->orderByDesc('id')->pluck('pc_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.stock_card.stock_card', compact('data'));
    }

    private function getBeginningStock($start, $end, $pst_id, $st_id, $current_stock, $exception, $except) {
        $beginning = 0;
        $mode = 'beginning';
        $end = date( "Y-m-d", strtotime($start." -1 day"));
        $start = '2021-01-01';
        $purchase = $this->getPurchase($start, $end, $pst_id, $st_id, $mode);
        $trans_in = $this->getTransIn($start, $end, $pst_id, $st_id, $mode);
        $trans_out = $this->getTransOut($start, $end, $pst_id, $st_id, $mode);
        $cross_setup_in = $this->getCrossSetupIn($start, $end, $pst_id, $st_id, $mode);
        $cross_setup_out = $this->getCrossSetupOut($start, $end, $pst_id, $st_id, $mode);
        $sales = $this->getSales($start, $end, $pst_id, $st_id, $mode);
        $waiting = $this->getWaiting($start, $end, $pst_id, $st_id, $mode);
        $refund = $this->getRefund($start, $end, $pst_id, $st_id, $mode);
        $adj_plus = $this->getAdjustment('adj', '+', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $adj_min = $this->getAdjustment('adj', '-', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $madj_plus = $this->getAdjustment('madj', '+', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $madj_min = $this->getAdjustment('madj', '-', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $sadj_plus = $this->getAdjustment('sadj', '+', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $sadj_min = $this->getAdjustment('sadj', '-', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $beginning = $purchase + $trans_in - $trans_out + $cross_setup_in - $cross_setup_out - $sales - $waiting + $refund + $adj_plus - $adj_min + $madj_plus - $madj_min + $sadj_plus - $sadj_min;
        if ($beginning < 0) {
            $beginning = 0;
        }
        return $beginning;
    }

    private function getPurchase($start, $end, $pst_id, $st_id, $mode) {
        $purchase = 0;
        $purchase = DB::table('purchase_order_article_detail_statuses')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
            ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
            ->where('purchase_order_article_details.pst_id', '=', $pst_id)
            ->where('purchase_orders.st_id', '=', $st_id)
            ->where(function($w) use ($start, $end) {
                if (!empty($end)) {
                    $w->whereDate('purchase_order_article_detail_statuses.created_at', '>=', $start)
                    ->whereDate('purchase_order_article_detail_statuses.created_at', '<=', $end);
                } else {
                    $w->whereDate('purchase_order_article_detail_statuses.created_at', '=', $start);
                }
            })
            ->groupBy('purchase_order_article_details.pst_id')
            ->sum('poads_qty');
        return $purchase;
    }

    private function getTransIn($start, $end, $pst_id, $st_id, $mode) {
        $trans_in = 0;
        $trans_in = DB::table('stock_transfer_detail_statuses')
        ->leftJoin('stock_transfer_details', 'stock_transfer_details.id', '=', 'stock_transfer_detail_statuses.stfd_id')
        ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
        ->where('stock_transfer_details.pst_id', '=', $pst_id)
        ->where('stock_transfers.st_id_end', '=', $st_id)
        ->where(function($w) use ($start, $end) {
            if (!empty($end)) {
                $w->whereDate('stock_transfer_detail_statuses.created_at', '>=', $start)
                ->whereDate('stock_transfer_detail_statuses.created_at', '<=', $end);
            } else {
                $w->whereDate('stock_transfer_detail_statuses.created_at', '=', $start);
            }
        })
        ->groupBy('stock_transfer_details.pst_id')
        ->sum('stfds_qty');
        return $trans_in;
    }

    private function getTransOut($start, $end, $pst_id, $st_id, $mode) {
        $trans_out = 0;
        $trans_out = DB::table('stock_transfer_details')
        ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
        ->where('stock_transfer_details.pst_id', '=', $pst_id)
        ->where('stock_transfers.st_id_start', '=', $st_id)
        ->where(function($w) use ($start, $end) {
            if (!empty($end)) {
                $w->whereDate('stock_transfer_details.created_at', '>=', $start)
                ->whereDate('stock_transfer_details.created_at', '<=', $end);
            } else {
                $w->whereDate('stock_transfer_details.created_at', '=', $start);
            }
        })
        ->groupBy('stock_transfer_details.pst_id')
        ->sum('stfd_qty');
        return $trans_out;
    }

    private function getCrossSetupIn($start, $end, $pst_id, $st_id, $mode) {
        $trans_out = 0;
        $pl_id = ProductLocation::select('id')->where('product_locations.st_id', '=', $st_id)->get()->toArray();
        $trans_out = DB::table('product_mutations')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_mutations.pls_id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->where('product_location_setups.pst_id', '=', $pst_id)
        ->where('product_locations.st_id', '!=', $st_id)
        ->whereIn('product_mutations.pl_id', $pl_id)
        ->where(function($w) use ($start, $end) {
            if (!empty($end)) {
                $w->whereDate('product_mutations.created_at', '>=', $start)
                ->whereDate('product_mutations.created_at', '<=', $end);
            } else {
                $w->whereDate('product_mutations.created_at', '=', $start);
            }
        })
        ->groupBy('product_location_setups.pst_id')
        ->sum('pmt_qty');
        return $trans_out;
    }

    private function getCrossSetupOut($start, $end, $pst_id, $st_id, $mode) {
        $trans_out = 0;
        $pl_id = ProductLocation::select('id')->where('product_locations.st_id', '=', $st_id)->get()->toArray();
        $trans_out = DB::table('product_mutations')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_mutations.pls_id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->where('product_location_setups.pst_id', '=', $pst_id)
        ->where('product_locations.st_id', '=', $st_id)
        ->whereNotIn('product_mutations.pl_id', $pl_id)
        ->where(function($w) use ($start, $end) {
            if (!empty($end)) {
                $w->whereDate('product_mutations.created_at', '>=', $start)
                ->whereDate('product_mutations.created_at', '<=', $end);
            } else {
                $w->whereDate('product_mutations.created_at', '=', $start);
            }
        })
        ->groupBy('product_location_setups.pst_id')
        ->sum('pmt_qty');
        return $trans_out;
    }

    private function getSales($start, $end, $pst_id, $st_id, $mode) {
        $sales = 0;
        $sales_1 = DB::table('pos_transaction_details')
        ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
        ->where('pos_transaction_details.pst_id', '=', $pst_id)
        ->where('pos_transactions.st_id', '=', $st_id)
        ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
        ->where(function($w) use ($start, $end) {
            if (!empty($end)) {
                $w->where('pos_transactions.created_at', '>=', $start)
                ->where('pos_transactions.created_at', '<=', $end);
            } else {
                $w->where('pos_transactions.created_at', '=', $start);
            }
        })
        ->groupBy('pos_transaction_details.pst_id')->sum('pos_td_qty');

        $sales_2 = DB::table('pos_transaction_details')
        ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
        ->where('pos_transaction_details.pst_id', '=', $pst_id)
        ->where('pos_transactions.st_id_ref', '=', $st_id)
        ->whereNotIn('pos_transactions.pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
        ->where(function($w) use ($start, $end) {
            if (!empty($end)) {
                $w->where('pos_transactions.created_at', '>=', $start)
                ->where('pos_transactions.created_at', '<=', $end);
            } else {
                $w->where('pos_transactions.created_at', '=', $start);
            }
        })
        ->groupBy('pos_transaction_details.pst_id')->sum('pos_td_qty');

        $plst_sales = DB::table('product_location_setup_transactions')->select('pt_id', 'plst_qty', 'plst_status')
        ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'product_location_setup_transactions.pt_id')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
        ->whereNotNull('pt_id')
        ->where('plst_status', '!=', 'INSTOCK')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('product_location_setups.pst_id', '=', $pst_id)
        ->where(function($w) use ($start, $end) {
            if (!empty($end)) {
                $w->whereDate('product_location_setup_transactions.created_at', '>=', $start)
                ->whereDate('product_location_setup_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('product_location_setup_transactions.created_at', '=', $start);
            }
        })
        ->groupBy('product_location_setups.pst_id')
        ->sum('plst_qty');
        $sales = $sales_1 + $sales_2;
        if ($plst_sales > $sales) {
            $sales = $plst_sales;
        }
        return $sales;
    }

    private function getWaiting($start, $end, $pst_id, $st_id, $mode) {
        $waiting = 0;
        $waiting = DB::table('product_location_setup_transactions')->select('pt_id', 'plst_qty', 'plst_status')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->whereIn('plst_status', ['WAITING OFFLINE', 'WAITING FOR CHECKOUT', 'WAITING TO TAKE'])
        ->where('product_locations.st_id', '=', $st_id)
        ->where('product_location_setups.pst_id', '=', $pst_id)
        ->where(function($w) use ($start, $end) {
            if (!empty($end)) {
                $w->whereDate('product_location_setup_transactions.created_at', '>=', $start)
                ->whereDate('product_location_setup_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('product_location_setup_transactions.created_at', '=', $start);
            }
        })
        ->groupBy('product_location_setups.pst_id')
        ->sum('plst_qty');
        return $waiting;
    }

    private function getRefund($start, $end, $pst_id, $st_id, $mode) {
        $refund = 0;
        $refund_1 = DB::table('product_location_setup_transactions')->select('pt_id', 'plst_qty', 'plst_status')
        ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'product_location_setup_transactions.pt_id')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
        ->whereNotNull('pt_id')
        ->where('plst_status', '=', 'INSTOCK')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('product_location_setups.pst_id', '=', $pst_id)
        ->where(function($w) use ($start, $end) {
            if (!empty($end)) {
                $w->whereDate('product_location_setup_transactions.created_at', '>=', $start)
                ->whereDate('product_location_setup_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('product_location_setup_transactions.created_at', '=', $start);
            }
        })
        ->groupBy('product_location_setups.pst_id')
        ->sum('plst_qty');

        $refund_2 = DB::table('product_location_setup_transactions')->select('pt_id', 'plst_qty', 'plst_status')
        ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'product_location_setup_transactions.pt_id')
        ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
        ->whereNotNull('pt_id')
        ->where('plst_status', '=', 'INSTOCK')
        ->where('pos_transactions.st_id_ref', '=', $st_id)
        ->where('product_location_setups.pst_id', '=', $pst_id)
        ->where(function($w) use ($start, $end) {
            if (!empty($end)) {
                $w->whereDate('product_location_setup_transactions.created_at', '>=', $start)
                ->whereDate('product_location_setup_transactions.created_at', '<=', $end);
            } else {
                $w->whereDate('product_location_setup_transactions.created_at', '=', $start);
            }
        })
        ->groupBy('product_location_setups.pst_id')
        ->sum('plst_qty');

        $refund = $refund_1 + $refund_2;
        return $refund;
    }

    private function getAdjustment($type, $plus_min, $start, $end, $pst_id, $st_id, $exception, $except, $mode) {
        $adjustment = 0;
        if ($type == 'adj') {
            $adj = DB::table('bin_adjustments')->select('ba_adjust', 'ba_adjust_type', 'ba_old_qty', 'ba_new_qty')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'bin_adjustments.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->where('product_location_setups.pst_id', '=', $pst_id)
            ->where('product_locations.st_id', '=', $st_id)
            ->where(function($w) use ($start, $end, $exception, $except, $plus_min) {
                if (!empty($end)) {
                    $w->whereDate('bin_adjustments.created_at', '>=', $start)
                    ->whereDate('bin_adjustments.created_at', '<=', $end);
                } else {
                    $w->whereDate('bin_adjustments.created_at', '=', $start);
                }
                if ($plus_min == '+') {
                    $w->where("bin_adjustments.ba_adjust_type", '=', '+');
                } else {
                    $w->where("bin_adjustments.ba_adjust_type", '=', '-');
                }
            })
            ->groupBy('bin_adjustments.id')
            ->get();
            if (!empty($adj->first())) {
                foreach ($adj as $row) {
                    $adjustment += $row->ba_adjust;
                }
            }
        } else if ($type == 'madj') {
            $madj = DB::table('mass_adjustment_details')->select('mad_type', 'mad_diff', 'qty_export', 'qty_so')
            ->leftJoin('mass_adjustments', 'mass_adjustments.id', '=', 'mass_adjustment_details.ma_id')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'mass_adjustment_details.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->where('product_location_setups.pst_id', '=', $pst_id)
            ->where('mass_adjustments.st_id', '=', $st_id)
            ->where('mass_adjustments.ma_status', '=', '1')
            ->where(function($w) use ($start, $end, $exception, $except, $plus_min) {
                if (!empty($end)) {
                    $w->whereDate('mass_adjustments.created_at', '>=', $start)
                    ->whereDate('mass_adjustments.created_at', '<=', $end);
                } else {
                    $w->whereDate('mass_adjustments.created_at', '=', $start);
                }
                if ($plus_min == '+') {
                    $w->whereRaw("ts_mass_adjustment_details.qty_so > ts_mass_adjustment_details.qty_export");
                } else {
                    $w->whereRaw("ts_mass_adjustment_details.qty_export > ts_mass_adjustment_details.qty_so");
                }
            })
            ->groupBy('product_location_setups.pst_id')
            ->get();
            if (!empty($madj->first())) {
                foreach ($madj as $row) {
                    if ($plus_min == '+') {
                        $adjustment += ($row->qty_so - $row->qty_export);
                    } else {
                        $adjustment += ($row->qty_export - $row->qty_so);
                    }
                }
            };
        } else {
            $sadj = DB::table('scan_adjustment_details')->select('mad_type', 'mad_diff', 'qty', 'qty_so')
            ->leftJoin('scan_adjustments', 'scan_adjustments.id', '=', 'scan_adjustment_details.sa_id')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'scan_adjustment_details.pls_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->where('product_location_setups.pst_id', '=', $pst_id)
            ->where('scan_adjustments.st_id', '=', $st_id)
            ->where('scan_adjustments.sa_status', '=', '1')
            ->where(function($w) use ($start, $end, $exception, $except, $plus_min) {
                if (!empty($end)) {
                    $w->whereDate('scan_adjustments.created_at', '>=', $start)
                    ->whereDate('scan_adjustments.created_at', '<=', $end);
                } else {
                    $w->whereDate('scan_adjustments.created_at', '=', $start);
                }
                if ($plus_min == '+') {
                    $w->whereRaw("ts_scan_adjustment_details.qty_so > ts_scan_adjustment_details.qty");
                } else {
                    $w->whereRaw("ts_scan_adjustment_details.qty > ts_scan_adjustment_details.qty_so");
                }
            })
            ->groupBy('product_location_setups.pst_id')
            ->get();
            if (!empty($sadj->first())) {
                foreach ($sadj as $row) {
                    if ($plus_min == '+') {
                        $adjustment += ($row->qty_so - $row->qty);
                    } else {
                        $adjustment += ($row->qty - $row->qty_so);
                    }
                }
            };
        }
        return $adjustment;
    }

    private function endingStock($start, $end, $pst_id, $st_id, $current_stock, $exception, $except) {
        $ending = 0;
        $mode = 'ending';
        if (empty($end)) {
            $end = $start;
        }
        $start = '2021-01-01';
        $purchase = $this->getPurchase($start, $end, $pst_id, $st_id, $mode);
        $trans_in = $this->getTransIn($start, $end, $pst_id, $st_id, $mode);
        $trans_out = $this->getTransOut($start, $end, $pst_id, $st_id, $mode);
        $cross_setup_in = $this->getCrossSetupIn($start, $end, $pst_id, $st_id, $mode);
        $cross_setup_out = $this->getCrossSetupOut($start, $end, $pst_id, $st_id, $mode);
        $sales = $this->getSales($start, $end, $pst_id, $st_id, $mode);
        $waiting = $this->getWaiting($start, $end, $pst_id, $st_id, $mode);
        $refund = $this->getRefund($start, $end, $pst_id, $st_id, $mode);
        $adj_plus = $this->getAdjustment('adj', '+', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $adj_min = $this->getAdjustment('adj', '-', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $madj_plus = $this->getAdjustment('madj', '+', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $madj_min = $this->getAdjustment('madj', '-', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $sadj_plus = $this->getAdjustment('sadj', '+', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $sadj_min = $this->getAdjustment('sadj', '-', $start, $end, $pst_id, $st_id, $exception, $except, $mode);
        $ending = $purchase + $trans_in - $trans_out + $cross_setup_in - $cross_setup_out - $sales - $waiting + $refund + $adj_plus - $adj_min + $madj_plus - $madj_min + $sadj_plus - $sadj_min;
        
        if ($ending < 0) {
            $ending = 0;
        }
        return $ending;
    }

    // public function getADatatables(Request $request)
    // {
    //     if(request()->ajax()) {
    //         $date = $request->get('date');
    //         $start = null;
    //         $end = null;
    //         $exp = explode('|', $date);
    //         if (count($exp) > 1) {
    //             if ($exp[0] != $exp[1]) {
    //                 $start = $exp[0];
    //                 $end = $exp[1];
    //             } else {
    //                 $start = $exp[0];
    //             }
    //         } else {
    //             $start = $date;
    //         }
    //         $mode = 'table';
    //         $st_id = $request->get('st_id');
    //         $br_id = $request->get('br_id');
    //         $data = $request->get('data');
    //         $exception = $request->get('exception');
    //         $except = ExceptionLocation::select('pl_code')
    //         ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();
    //         if ($data != 'article') {
    //             $st_id = "!@#$%^&*()";
    //         }
    //         return datatables()->of(DB::table('product_stocks')
    //         ->select("product_stocks.id as id", "br_name", "p_name", "p_color", "sz_name",
    //         "ps_sell_price", "p_sell_price", "ps_purchase_price", "p_purchase_price", DB::raw("sum(ts_product_location_setups.pls_qty) as stock"))
    //         ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
    //         ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
    //         ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
    //         ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
    //         ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
    //         ->where('product_locations.st_id', '=', $st_id)
    //         ->where('products.p_delete', '!=', '1')
    //         ->where(function($w) use ($exception, $except, $br_id) {
    //             if ($exception == 'noexcept') {
    //                 $w->whereNotIn('product_locations.pl_code', $except);
    //             }
    //             if (!empty($br_id)) {
    //                 $w->where('products.br_id', '=', $br_id);
    //             }
    //         })
    //         ->groupBy('product_stocks.id'))
    //         ->editColumn('beginning_stock', function($d) use ($st_id, $start, $end, $exception, $except) {
    //             return $this->getBeginningStock($start, $end, $d->id, $st_id, $d->stock, $exception, $except);
    //         })
    //         ->editColumn('purchase', function($d) use ($st_id, $start, $end, $mode) {
    //             return $this->getPurchase($start, $end, $d->id, $st_id, $mode);
    //         })
    //         ->editColumn('trans_in', function($d) use ($st_id, $start, $end, $mode) {
    //             return $this->getTransIn($start, $end, $d->id, $st_id, $mode);
    //         })
    //         ->editColumn('trans_out', function($d) use ($st_id, $start, $end, $mode) {
    //             return $this->getTransOut($start, $end, $d->id, $st_id, $mode);
    //         })
    //         ->editColumn('cross_setup_in', function($d) use ($st_id, $start, $end, $mode) {
    //             return $this->getCrossSetupIn($start, $end, $d->id, $st_id, $mode);
    //         })
    //         ->editColumn('cross_setup_out', function($d) use ($st_id, $start, $end, $mode) {
    //             return $this->getCrossSetupOut($start, $end, $d->id, $st_id, $mode);
    //         })
    //         ->editColumn('waiting', function($d) use ($st_id, $start, $end, $mode) {
    //             return $this->getWaiting($start, $end, $d->id, $st_id, $mode);
    //         })
    //         ->editColumn('sales', function($d) use ($st_id, $start, $end, $mode) {
    //             return $this->getSales($start, $end, $d->id, $st_id, $mode);
    //         })
    //         ->editColumn('refund', function($d) use ($st_id, $start, $end, $mode) {
    //             return $this->getRefund($start, $end, $d->id, $st_id, $mode);
    //         })
    //         ->editColumn('adj_plus', function($d) use ($st_id, $start, $end, $exception, $except, $mode) {
    //             return $this->getAdjustment('adj', '+', $start, $end, $d->id, $st_id, $exception, $except, $mode);
    //         })
    //         ->editColumn('adj_min', function($d) use ($st_id, $start, $end, $exception, $except, $mode) {
    //             return $this->getAdjustment('adj', '-', $start, $end, $d->id, $st_id, $exception, $except, $mode);
    //         })
    //         ->editColumn('madj_plus', function($d) use ($st_id, $start, $end, $exception, $except, $mode) {
    //             return $this->getAdjustment('madj', '+', $start, $end, $d->id, $st_id, $exception, $except, $mode);
    //         })
    //         ->editColumn('madj_min', function($d) use ($st_id, $start, $end, $exception, $except, $mode) {
    //             return $this->getAdjustment('madj', '-', $start, $end, $d->id, $st_id, $exception, $except, $mode);
    //         })
    //         ->editColumn('sadj_plus', function($d) use ($st_id, $start, $end, $exception, $except, $mode) {
    //             return $this->getAdjustment('sadj', '+', $start, $end, $d->id, $st_id, $exception, $except, $mode);
    //         })
    //         ->editColumn('sadj_min', function($d) use ($st_id, $start, $end, $exception, $except, $mode) {
    //             return $this->getAdjustment('sadj', '-', $start, $end, $d->id, $st_id, $exception, $except, $mode);
    //         })
    //         ->editColumn('ending_stock', function($d) use ($st_id, $start, $end, $exception, $except) {
    //             return $this->endingStock($start, $end, $d->id, $st_id, $d->stock, $exception, $except);
    //         })
    //         ->editColumn('exception', function($d) use ($st_id, $start, $end, $mode, $except) {
    //             $exception = DB::table('product_location_setups')
    //             ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
    //             ->where('product_locations.st_id', '=', $st_id)
    //             ->whereIn('product_locations.pl_code', $except)
    //             ->where('product_location_setups.pst_id', $d->id)
    //             ->groupBy('product_location_setups.pst_id')->sum('pls_qty');
    //             return $exception;
    //         })
    //         ->editColumn('purchase_price', function($d) use ($st_id) {
    //             $purchase = 0;
    //             $poads = DB::table('purchase_order_article_detail_statuses')
    //             ->selectRaw("avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase_1, avg(ts_purchase_order_article_details.poad_purchase_price) as purchase_2")
    //             ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
    //             ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
    //             ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
    //             ->where('purchase_order_article_details.pst_id', '=', $d->id)
    //             ->where('purchase_orders.st_id', '=', $st_id)
    //             ->first();
    //             if (!empty($poads)) {
    //                 if (!empty($poads->purchase_1)) {
    //                     $purchase = $poads->purchase_1;
    //                 } else {
    //                     $purchase = $poads->purchase_2;
    //                 }
    //             } else {
    //                 if (!empty($d->ps_purchase_price)) {
    //                     $purchase = $d->ps_purchase_price;
    //                 } else {
    //                     $purchase = $d->p_purchase_price;
    //                 }
    //             }
    //             return number_format($purchase);
    //         })
    //         ->editColumn('sell_price', function($d){
    //             if (!empty($d->ps_sell_price)) {
    //                 return number_format($d->ps_sell_price);
    //             } else {
    //                 return number_format($d->p_sell_price);
    //             }
    //         })
    //         ->filter(function ($instance) use ($request) {
    //             if (!empty($request->get('search'))) {
    //                 $instance->where(function($w) use($request){
    //                     $search = $request->get('search');
    //                     $w->orWhereRaw('CONCAT(br_name," ",p_name," ",p_color," ",sz_name) LIKE ?', "%$search%");
    //                 });
    //             }
    //         })
    //         ->addIndexColumn()
    //         ->make(true);
    //     }
    // }

    public function getADatatables(Request $request)
    {
        if(request()->ajax()) {
            $date = $request->get('date');
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
            $st_id = $request->get('st_id');
            $br_id = $request->get('br_id');

            return datatables()->of(DB::table('stock_exports')
            ->where(function($w) use ($st_id, $br_id) {
                if (!empty($st_id)) {
                    $w->where('st_id', '=', $st_id);
                } 
                if (!empty($br_id)) {
                    $w->where('br_id', '=', $br_id);
                }
            }))
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(brand," ",article," ",color," ",size) LIKE ?', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getBDatatables(Request $request)
    {
        if(request()->ajax()) {
            $st_id = $request->get('st_id');
            $data = $request->get('data');
            $exception = $request->get('exception');
            $except = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();
            if ($data != 'brand') {
                $st_id = "!@#$%^&*()";
            }
            return datatables()->of(DB::table('product_stocks')
            ->select("product_stocks.id as id", "br_name", "p_name", DB::raw("sum(ts_product_location_setups.pls_qty) as stock"))
            ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->where('product_locations.st_id', '=', $st_id)
            ->where(function($w) use ($exception, $except) {
                if ($exception == 'noexcept') {
                    $w->whereNotIn('product_locations.pl_code', $except);
                }
            })
            ->groupBy('brands.id'))
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

    public function updateStockExport($start, $end, $exception, $st_id, $br_id, $mode)
    {
        $except = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        $stock = DB::table('product_stocks')
        ->select("product_stocks.id as id", "br_name", "p_name", "p_color", "sz_name", "st_name", "ps_barcode",
        "ps_sell_price", "p_sell_price", "ps_purchase_price", "p_purchase_price", DB::raw("sum(ts_product_location_setups.pls_qty) as stock"))
        ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
        ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
        ->leftJoin('stores', 'stores.id', '=', 'product_locations.st_id')
        ->where('products.p_delete', '!=', '1')
        ->where('product_locations.st_id', '=', $st_id)
        ->where(function($w) use ($exception, $except, $br_id) {
            if ($exception == 'noexcept') {
                $w->whereNotIn('product_locations.pl_code', $except);
            }
            if (!empty($br_id)) {
                $w->where('products.br_id', '=', $br_id);
            }
        })
        ->groupBy('product_stocks.id')->get();
        if (!empty($stock->first())) {
            DB::table('stock_exports')->where('u_id', '=', Auth::user()->id)->truncate();
            $datas = array();
            $total_data = count($stock);
            foreach ($stock as $row) {
                $barcode = $row->ps_barcode;
                if (empty($barcode)) {
                    $barcode = '--';
                }
                
                $exception_qty = DB::table('product_location_setups')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where('product_locations.st_id', '=', $st_id)
                ->whereIn('product_locations.pl_code', $except)
                ->where('product_location_setups.pst_id', $row->id)
                ->groupBy('product_location_setups.pst_id')->sum('pls_qty');

                if (!empty($row->ps_sell_price)) {
                    $sell_price = $row->ps_sell_price;
                } else {
                    $sell_price = $row->p_sell_price;
                }
                if (empty($end)) {
                    $end = $start;
                }

                $beginning = $this->getBeginningStock($start, $end, $row->id, $st_id, $row->stock, $exception, $except);

                $datas[] = [
                    'pst_id' => $row->id,
                    'u_id' => Auth::user()->id,
                    'start_date' => $start,
                    'end_date' => $end, 
                    'store' => $row->st_name, 
                    'barcode' => $barcode, 
                    'brand' => $row->br_name,
                    'article' => $row->p_name,
                    'color' => $row->p_color,
                    'size' => $row->sz_name,
                    'beginning_stock' => $beginning,
                    'today_exception' => $exception_qty,
                    'today_stock' => $row->stock,
                    'hj' => $sell_price,
                ];
                $total_temp = count($datas);
                if ($total_data >= 2000 AND $total_temp >= 2000) {
                    $insert = DB::table('stock_exports')->insert($datas);
                    if (!empty($insert)) {
                        $total_data = $total_data - $total_temp;
                        $datas = array();
                    }
                }
            }
            if ($total_data < 2000) {
                $insert = DB::table('stock_exports')->insert($datas);
                if (!empty($insert)) {
                    $datas = array();
                }
            }
        }
    }
    
    public function phase2(Request $request)
    {
        $except = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        $exception = $request->post('exception');
        $data = $request->post('data');
        $st_id = $request->post('st_id');
        $br_id = $request->post('br_id');
        $mode = 'table';

        $stock = DB::table('stock_exports')->where('u_id', '=', Auth::user()->id)->get();

        if (!empty($stock->first())) {
            $datas = array();
            $total_data = count($stock);
            foreach ($stock as $row) {
                $start = $row->start_date;
                $end = $row->end_date; 

                $purchase_price = 0;
                $poads = DB::table('purchase_order_article_detail_statuses')
                ->selectRaw("avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase_1, avg(ts_purchase_order_article_details.poad_purchase_price) as purchase_2")
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                ->leftJoin('purchase_order_articles', 'purchase_order_articles.id', '=', 'purchase_order_article_details.poa_id')
                ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_articles.po_id')
                ->where('purchase_order_article_details.pst_id', '=', $row->pst_id)
                ->where('purchase_orders.st_id', '=', $st_id)
                ->first();
                if (!empty($poads)) {
                    if (!empty($poads->purchase_1)) {
                        $purchase_price = $poads->purchase_1;
                    } else {
                        $purchase_price = $poads->purchase_2;
                    }
                }

                $purchase = $this->getPurchase($start, $end, $row->pst_id, $st_id, $mode);
                $trans_out = $this->getTransIn($start, $end, $row->pst_id, $st_id, $mode);
                $trans_in = $this->getTransIn($start, $end, $row->pst_id, $st_id, $mode);
                $sales = $this->getSales($start, $end, $row->pst_id, $st_id, $mode);
                $refund = $this->getRefund($start, $end, $row->pst_id, $st_id, $mode);
                $adj_min = $this->getAdjustment('adj', '-', $start, $end, $row->pst_id, $st_id, $exception, $except, $mode);
                $adj_plus = $this->getAdjustment('adj', '+', $start, $end, $row->pst_id, $st_id, $exception, $except, $mode);
               
                $datas[] = [
                    'purchase' => $purchase,
                    'trans_out' => $trans_out,
                    'trans_in' => $trans_in,
                    'sales' => $sales,
                    'refund' => $refund,
                    'adj_min' => $adj_min,
                    'adj_plus' => $adj_plus,
                    'hb' => $purchase_price,
                ];
                $total_temp = count($datas);
                if ($total_data >= 2000 AND $total_temp >= 2000) {
                    $insert = DB::table('stock_exports')->where('id', '=', $row->id)->update($datas);
                    if (!empty($insert)) {
                        $total_data = $total_data - $total_temp;
                        $datas = array();
                    }
                }
            }
            if ($total_data < 2000) {
                $insert = DB::table('stock_exports')->where('id', '=', $row->id)->update($datas);
                if (!empty($insert)) {
                    $datas = array();
                }
            }
        }
        $r['status'] = 200;
        return json_encode($r);
    }
    
    public function phase3(Request $request)
    {
        $except = ExceptionLocation::select('pl_code')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')
            ->get()
            ->toArray();

        $exception = $request->post('exception');
        $data = $request->post('data');
        $st_id = $request->post('st_id');
        $br_id = $request->post('br_id');
        $mode = 'table';

        $stock = DB::table('stock_exports')->where('u_id', '=', Auth::user()->id)->get();

        if (!empty($stock->first())) {
            $datas = array();
            $total_data = count($stock);
            foreach ($stock as $row) {

                $start = $row->start_date;
                $end = $row->end_date; 
                $beginning = $row->beginning_stock;
                $purchase = $row->purchase;
                $trans_out = $row->trans_out;
                $trans_in = $row->trans_in;
                $sales = $row->sales;
                $refund = $row->refund;
                $adj_min = $row->adj_min;
                $adj_plus = $row->adj_plus;

                $madj_min = $this->getAdjustment('madj', '-', $start, $end, $row->pst_id, $st_id, $exception, $except, $mode);
                $madj_plus = $this->getAdjustment('madj', '+', $start, $end, $row->pst_id, $st_id, $exception, $except, $mode);
                $sadj_min = $this->getAdjustment('sadj', '-', $start, $end, $row->pst_id, $st_id, $exception, $except, $mode);
                $sadj_plus = $this->getAdjustment('sadj', '+', $start, $end, $row->pst_id, $st_id, $exception, $except, $mode);
                $waiting = $this->getWaiting($start, $end, $row->pst_id, $st_id, $mode);
                $cross_setup_in = $this->getCrossSetupIn($start, $end, $row->pst_id, $st_id, $mode);
                $cross_setup_out = $this->getCrossSetupOut($start, $end, $row->pst_id, $st_id, $mode);

                $ending_stock = $beginning + $purchase - $trans_out + $trans_in - $sales + $refund - $adj_min + $adj_plus - $madj_min + $madj_plus - $sadj_min + $sadj_plus - $waiting - $cross_setup_out + $cross_setup_in;

                $datas[] = [
                    'madj_min' => $madj_min,
                    'madj_plus' => $madj_plus,
                    'sadj_min' => $sadj_min,
                    'sadj_plus' => $sadj_plus,
                    'waiting' => $waiting,
                    'cross_setup_in' => $cross_setup_in,
                    'cross_setup_out' => $cross_setup_out,
                    'ending_stock' => $ending_stock,
                ];
                $total_temp = count($datas);
                if ($total_data >= 2000 AND $total_temp >= 2000) {
                    $insert = DB::table('stock_exports')->where('id', '=', $row->id)->update($datas);
                    if (!empty($insert)) {
                        $total_data = $total_data - $total_temp;
                        $datas = array();
                    }
                }
            }
            if ($total_data < 2000) {
                $insert = DB::table('stock_exports')->where('id', '=', $row->id)->update($datas);
                if (!empty($insert)) {
                    $datas = array();
                }
            }
        }
        $r['status'] = 200;
        return json_encode($r);
    }

    public function fillData(Request $request) {
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
        $exception = $request->post('exception');
        $data = $request->post('data');
        $st_id = $request->post('st_id');
        $br_id = $request->post('br_id');
        $this->updateStockExport($start, $end, $exception, $st_id, $br_id, 'table');
        $r['status'] = 200;
        return json_encode($r);
    }
    
    public function exportData()
    {
        return Excel::download(new StockCardExport(), 'Stock_Report.xlsx');
    }
}
