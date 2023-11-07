<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Target;
use App\Models\SubSubTarget;
use App\Models\Store;
use App\Models\StoreType;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\ProductCategory;

class PosSummaryController extends Controller
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
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'pc_id' => ProductCategory::where('pc_delete', '!=', '1')->orderByDesc('id')->pluck('pc_name', 'id'),
            'segment' => request()->segment(1),
        ];
        return view('app.pos_summary.pos_summary', compact('data'));
    }

    public function onlineDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(User::select('users.id as u_id', 'u_name', 'stt_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'users.stt_id')
            ->leftJoin('user_groups', 'user_groups.user_id', '=', 'users.id')
            ->leftJoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->where('users.u_delete', '!=', '1')
            ->where('stt_name', '=', 'online')
            ->where('users.st_id', '=', $request->st_id)
            ->whereIn('g_name', ['sales', 'head']))
            ->editColumn('sales', function($data) use ($request){
                if (!empty($request->pos_summary_date)) {
                    $range = $request->pos_summary_date;
                    $exp = explode('|', $range);
                    $transaction = 0;
                    $item = 0;
                    if (count($exp) > 1) {
                        $start = $exp[0];
                        $end = $exp[1];
                        $total_transaction = PosTransaction::select('id', 'pos_status')
                        ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                        ->where('u_id', '=', $data->u_id)
                        ->whereDate('created_at', '>=', $start)
                        ->whereDate('created_at', '<=', $end)->get();
                        if (!empty($total_transaction)) {
                            foreach ($total_transaction as $row) {
                                $transaction += 1;
                                $total_item = PosTransactionDetail::select('id')
                                ->where('pt_id', '=', $row->id)
                                ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                                ->whereDate('created_at', '>=', $start)
                                ->whereDate('created_at', '<=', $end)->get();
                                if (!empty($total_item)) {
                                    foreach ($total_item as $trow) {
                                        $item += 1;
                                    }
                                }
                            }
                        }
                    } else {
                        $start = $request->pos_summary_date;
                        $total_transaction = PosTransaction::select('id', 'pos_status')
                        ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                        ->where('u_id', '=', $data->u_id)
                        ->whereDate('created_at', $start)->get();
                        if (!empty($total_transaction)) {
                            foreach ($total_transaction as $row) {
                                $transaction += 1;
                                $total_item = PosTransactionDetail::select('id')
                                ->where('pt_id', '=', $row->id)
                                ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                                ->whereDate('created_at', $start)->get();
                                if (!empty($total_item)) {
                                    foreach ($total_item as $trow) {
                                        $item += 1;
                                    }
                                }
                            }
                        }
                    }
                    return '<span class="btn btn-sm btn-primary" style="white-space: nowrap;" data-u_name="'.$data->u_name.'" data-u_id="'.$data->u_id.'" id="sales_detail_btn">['.$transaction.'] INV ['.$item.'] Artikel</span>';
                } else {
                    return '-';
                }
            })
            ->editColumn('sales_total', function($data) use ($request){
                if (!empty($request->pos_summary_date)) {
                    $range = $request->pos_summary_date;
                    $exp = explode('|', $range);
                    $total_price = 0;
                    if (count($exp) > 1) {
                        $start = $exp[0];
                        $end = $exp[1];
                        $total_transaction = PosTransaction::select('id', 'pos_status', 'pos_admin_cost', 'pos_refund')
                        ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                        ->where('u_id', '=', $data->u_id)
                        ->whereDate('created_at', '>=', $start)
                        ->whereDate('created_at', '<=', $end)->get();
                        if (!empty($total_transaction)) {
                            foreach ($total_transaction as $row) {
                                $total_item = PosTransactionDetail::select('pos_td_discount_price', 'pos_td_marketplace_price', 'plst_status')
                                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                                ->groupBy('pos_transaction_details.id')
                                ->where('pos_transaction_details.pt_id', '=', $row->id)
                                ->where('pos_transaction_details.pos_td_reject', '!=', '1')->get();
                                if (!empty($total_item)) {
                                    foreach ($total_item as $trow) {
                                    if ($row->pos_refund == '0' AND $trow->plst_status == 'INSTOCK') {
                                        continue;
                                    }
                                    if (!empty($trow->pos_td_marketplace_price)) {
                                        $total_price += $trow->pos_td_marketplace_price;
                                    } else {
                                        $total_price += $trow->pos_td_total_price;
                                    }
                                    }
                                }
                                $total_price = $total_price - $row->pos_admin_cost;
                            }
                        }
                    } else {
                        $start = $request->pos_summary_date;
                        $total_transaction = PosTransaction::select('id', 'pos_status', 'pos_admin_cost', 'pos_refund')
                        ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                        ->where('u_id', '=', $data->u_id)
                        ->whereDate('created_at', $start)->get();
                        if (!empty($total_transaction)) {
                            foreach ($total_transaction as $row) {
                                $total_item = PosTransactionDetail::select('pos_td_total_price', 'pos_td_marketplace_price', 'plst_status')
                                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                                ->groupBy('pos_transaction_details.id')
                                ->where('pos_transaction_details.pt_id', '=', $row->id)
                                ->where('pos_transaction_details.pos_td_reject', '!=', '1')->get();
                                if (!empty($total_item)) {
                                    foreach ($total_item as $trow) {
                                    if ($row->pos_refund == '0' AND $trow->plst_status == 'INSTOCK') {
                                        continue;
                                    }
                                    if (!empty($trow->pos_td_marketplace_price)) {
                                        $total_price += $trow->pos_td_marketplace_price;
                                    } else {
                                        $total_price += $trow->pos_td_total_price;
                                    }
                                    }
                                }
                                $total_price = $total_price - $row->pos_admin_cost;
                            }
                        }
                    }
                    return '<span class="btn btn-sm btn-success" style="white-space: nowrap;">Rp. '.number_format($total_price).'</span>';
                } else {
                    return '-';
                }
            })
            ->rawColumns(['sales', 'sales_total'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function offlineDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(User::select('users.id as u_id', 'u_name', 'stt_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'users.stt_id')
            ->leftJoin('user_groups', 'user_groups.user_id', '=', 'users.id')
            ->leftJoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->where('users.u_delete', '!=', '1')
            ->where('stt_name', '=', 'offline')
            ->where('users.st_id', '=', $request->st_id)
            ->whereIn('g_name', ['sales', 'head']))
            ->editColumn('sales', function($data) use ($request){
                if (!empty($request->pos_summary_date)) {
                    $range = $request->pos_summary_date;
                    $exp = explode('|', $range);
                    $transaction = 0;
                    $item = 0;
                    if (count($exp) > 1) {
                        $start = $exp[0];
                        $end = $exp[1];
                        $total_transaction = PosTransaction::select('id', 'pos_status')

                        ->where('u_id', '=', $data->u_id)
                        ->whereDate('created_at', '>=', $start)
                        ->whereDate('created_at', '<=', $end)->get();
                        if (!empty($total_transaction)) {
                            foreach ($total_transaction as $row) {
                                $transaction += 1;
                                $total_item = PosTransactionDetail::select('id')
                                ->where('pt_id', '=', $row->id)
                                ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                                ->whereDate('created_at', '>=', $start)
                                ->whereDate('created_at', '<=', $end)->get();
                                if (!empty($total_item)) {
                                    foreach ($total_item as $trow) {
                                        $item += 1;
                                    }
                                }
                            }
                        }
                    } else {
                        $start = $request->pos_summary_date;
                        $total_transaction = PosTransaction::select('id', 'pos_status')

                        ->where('u_id', '=', $data->u_id)
                        ->whereDate('created_at', $start)->get();
                        if (!empty($total_transaction)) {
                            foreach ($total_transaction as $row) {
                                $transaction += 1;
                                $total_item = PosTransactionDetail::select('id')
                                ->where('pt_id', '=', $row->id)
                                ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                                ->whereDate('created_at', $start)->get();
                                if (!empty($total_item)) {
                                    foreach ($total_item as $trow) {
                                        $item += 1;
                                    }
                                }
                            }
                        }
                    }
                    return '<span class="btn btn-sm btn-primary" style="white-space: nowrap;" data-u_name="'.$data->u_name.'" data-u_id="'.$data->u_id.'" id="sales_detail_btn">['.$transaction.'] INV ['.$item.'] Artikel</span>';
                } else {
                    return '-';
                }
            })
            ->editColumn('sales_total', function($data) use ($request){
                if (!empty($request->pos_summary_date)) {
                    $range = $request->pos_summary_date;
                    $exp = explode('|', $range);
                    $total_price = 0;
                    if (count($exp) > 1) {
                        $start = $exp[0];
                        $end = $exp[1];
                        $total_transaction = PosTransaction::select('id', 'pos_status')

                        ->where('u_id', '=', $data->u_id)
                        ->whereDate('created_at', '>=', $start)
                        ->whereDate('created_at', '<=', $end)->get();
                        if (!empty($total_transaction)) {
                            foreach ($total_transaction as $row) {
                                $total_item = PosTransactionDetail::select('pos_td_discount_price')
                                ->where('pt_id', '=', $row->id)
                                ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                                ->whereDate('created_at', '>=', $start)
                                ->whereDate('created_at', '<=', $end)->get();
                                if (!empty($total_item)) {
                                    foreach ($total_item as $trow) {
                                        $total_price += $trow->pos_td_discount_price;
                                    }
                                }
                            }
                        }
                    } else {
                        $start = $request->pos_summary_date;
                        $total_transaction = PosTransaction::select('id', 'pos_status')

                        ->where('u_id', '=', $data->u_id)
                        ->whereDate('created_at', $start)->get();
                        if (!empty($total_transaction)) {
                            foreach ($total_transaction as $row) {
                                $total_item = PosTransactionDetail::select('pos_td_discount_price')
                                ->where('pt_id', '=', $row->id)
                                ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                                ->whereDate('created_at', $start)->get();
                                if (!empty($total_item)) {
                                    foreach ($total_item as $trow) {
                                        $total_price += $trow->pos_td_discount_price;
                                    }
                                }
                            }
                        }
                    }
                    return '<span class="btn btn-sm btn-success" style="white-space: nowrap;">Rp. '.number_format($total_price).'</span>';
                } else {
                    return '-';
                }
            })
            ->rawColumns(['sales', 'sales_total'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function salesDetailDatatables(Request $request)
    {
        if(request()->ajax()) {
            $range = $request->pos_summary_date;
            $u_id = $request->u_id;
            $exp = explode('|', $range);
            if (count($exp) > 1) {
                $start = $exp[0];
                $end = $exp[1];
                $item = 0;
                $sales = 0;
                return datatables()->of(PosTransaction::select('pos_transactions.id as pt_id', 'pos_invoice', 'created_at', 'pos_status')
                ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                ->where('pos_transactions.st_id', '=', $request->st_id)
                ->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end)
                ->where('u_id', $u_id))
                ->editColumn('sales', function($data) use ($request, $start, $end, $item, $u_id){
                    $total_item = PosTransactionDetail::select('id', 'pos_td_qty')
                    ->where('pt_id', '=', $data->pt_id)
                    ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                    ->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end)->get();
                    if (!empty($total_item)) {
                        foreach ($total_item as $row) {
                            $item += $row->pos_td_qty;
                        }
                    }
                    return '<span class="btn btn-sm btn-primary" data-invoice="'.$data->pos_invoice.'" data-u_id="'.$u_id.'" data-pt_id="'.$data->pt_id.'" id="sales_item_detail_btn">'.$item.'</span>';
                })
                ->editColumn('sales_total', function($data) use ($request, $start, $end, $sales){
                    $total_sales = PosTransactionDetail::select('pos_td_total_price')
                    ->where('pt_id', '=', $data->pt_id)
                    ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                    ->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end)->get();
                    if (!empty($total_sales)) {
                        foreach ($total_sales as $row) {
                            $sales += $row->pos_td_total_price;
                        }
                    }
                    return number_format($sales);
                })
                ->editColumn('created_at', function($data) {
                    return date('d-m-Y H:i:s', strtotime($data->created_at));
                })
                ->rawColumns(['sales', 'sales_total'])
                ->addIndexColumn()
                ->make(true);
            } else {
                $start = $request->pos_summary_date;
                $u_id = $request->u_id;
                $item = 0;
                $sales = 0;
                return datatables()->of(PosTransaction::select('pos_transactions.id as pt_id', 'pos_invoice', 'created_at', 'pos_status')
                ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                ->where('pos_transactions.st_id', '=', $request->st_id)
                ->whereDate('created_at', $start)
                ->where('u_id', $u_id))
                ->editColumn('sales', function($data) use ($request, $start, $item, $u_id){
                    $total_item = PosTransactionDetail::select('id', 'pos_td_qty')
                    ->where('pt_id', '=', $data->pt_id)
                    ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                    ->whereDate('created_at', $start)->get();
                    if (!empty($total_item)) {
                        foreach ($total_item as $row) {
                            $item += $row->pos_td_qty;
                        }
                    }
                    return '<span class="btn btn-sm btn-primary" data-invoice="'.$data->pos_invoice.'" data-u_id="'.$u_id.'" data-pt_id="'.$data->pt_id.'" id="sales_item_detail_btn">'.$item.'</span>';
                })
                ->editColumn('sales_total', function($data) use ($request, $start, $sales){
                    $total_sales = PosTransactionDetail::select('pos_td_total_price')
                    ->where('pt_id', '=', $data->pt_id)
                    ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                    ->whereDate('created_at', $start)->get();
                    if (!empty($total_sales)) {
                        foreach ($total_sales as $row) {
                            $sales += $row->pos_td_total_price;
                        }
                    }
                    return number_format($sales);
                })
                ->editColumn('created_at', function($data) {
                    return date('d-m-Y H:i:s', strtotime($data->created_at));
                })
                ->rawColumns(['sales', 'sales_total'])
                ->addIndexColumn()
                ->make(true);
            }
        }
    }

    public function salesItemDetailDatatables(Request $request)
    {
        if(request()->ajax()) {
                return datatables()->of(PosTransactionDetail::select('pos_transaction_details.id as ptd_id', 'br_name', 'p_name', 'p_color', 'sz_name', 'pos_td_qty', 'pos_td_total_price', 'pos_transaction_details.created_at as pos_created')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
                ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->where('pt_id', $request->pt_id)
                ->where('pos_transaction_details.pos_td_reject', '!=', '1'))
                ->editColumn('article', function($data){
                    return '['.$data->br_name.'] '.$data->p_name.' '.$data->p_color.' ['.$data->sz_name.']';
                })
                ->editColumn('created_at', function($data) {
                    return date('d-m-Y H:i:s', strtotime($data->pos_created));
                })
                ->editColumn('pos_td_total_price', function($data) {
                    return number_format($data->pos_td_total_price);
                })
                ->rawColumns(['article'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function targetChart(Request $request)
    {
        $type = $request->_type;
        $date = $request->_range;
        $label = $request->_label;
        $st_id = $request->_st_id;
        $exp = explode('|', $date);
        $stt_id = 0;
        if (count($exp) > 1) {
            $start = $exp[0];
            $end = $exp[1];
        } else {
            $start = $exp[0];
            $end = null;
        }
        $exp_target = explode('-', $start);
        $year = $exp_target[0];
        $month = $exp_target[1];
        $tr_date = $year.'-'.$month;
        if ($type == 'online') {
            $stt_id = StoreType::select('id')->where('stt_name', '=', 'ONLINE')->get()->first()->id;
        } else {
            $stt_id = StoreType::select('id')->where('stt_name', '=', 'OFFLINE')->get()->first()->id;
        }
        $target = 0;
        $get = 0;
        if (!empty($end)) {
            if ($start == $end) {
                $target = SubSubTarget::select('sstr_amount')
                          ->leftJoin('sub_targets', 'sub_targets.id', '=', 'sub_sub_targets.str_id')
                          ->where('st_id', '=', $st_id)
                          ->where('stt_id', '=', $stt_id)
                          ->whereDate('sstr_date', $exp[0])
                          ->sum('sstr_amount');
                $pos_transaction = PosTransaction::select('id', 'pos_admin_cost', 'stt_id', 'pos_status', 'pos_refund')
                ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                
                ->where('stt_id', '=', $stt_id)
                ->where('st_id', '=', $st_id)
                ->whereDate('pos_transactions.created_at', $exp[0])
                ->get();
                if (!empty($pos_transaction)) {
                    foreach ($pos_transaction as $pt) {
                        $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status')
                        ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                        ->where('pos_transaction_details.pt_id', $pt->id)
                        ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                        ->groupBy('pos_transaction_details.id')
                        ->get();
                        if (!empty($pos_transaction_detail)) {
                            $ptd_total = 0;
                            foreach ($pos_transaction_detail as $ptd) {
                                if ($pt->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                                  continue;
                                }
                                if (!empty($ptd->pos_td_marketplace_price)) {
                                    $ptd_total += $ptd->pos_td_marketplace_price;
                                } else {
                                    $ptd_total += $ptd->pos_td_discount_price;
                                }
                            }
                            if ($ptd_total > 0) {
                                $get += $ptd_total - $pt->pos_admin_cost;
                            } else {
                                $get += $ptd_total;
                            }
                        }
                    }
                }
            } else {
                $target = SubSubTarget::select('sstr_amount')
                        ->leftJoin('sub_targets', 'sub_targets.id', '=', 'sub_sub_targets.str_id')
                        ->where('st_id', '=', $st_id)
                        ->where('stt_id', '=', $stt_id)
                        ->whereDate('sstr_date', '>=', $exp[0])
                        ->whereDate('sstr_date', '<=', $exp[1])
                        ->sum('sstr_amount');
                $pos_transaction = PosTransaction::select('id', 'pos_admin_cost', 'stt_id', 'pos_status', 'pos_refund')
                ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
                
                ->where('stt_id', '=', $stt_id)
                ->where('st_id', '=', $st_id)
                ->whereDate('pos_transactions.created_at', '>=', $exp[0])
                ->whereDate('pos_transactions.created_at', '<=', $exp[1])
                ->get();
                if (!empty($pos_transaction)) {
                    foreach ($pos_transaction as $pt) {
                        $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status')
                        ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                        ->where('pos_transaction_details.pt_id', $pt->id)
                        ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                        ->groupBy('pos_transaction_details.id')
                        ->get();
                        if (!empty($pos_transaction_detail)) {
                            $ptd_total = 0;
                            foreach ($pos_transaction_detail as $ptd) {
                                if ($pt->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                                  continue;
                                }
                                if (!empty($ptd->pos_td_marketplace_price)) {
                                    $ptd_total += $ptd->pos_td_marketplace_price;
                                } else {
                                    $ptd_total += $ptd->pos_td_discount_price;
                                }
                            }
                            if ($ptd_total > 0) {
                                $get += $ptd_total - $pt->pos_admin_cost;
                            } else {
                                $get += $ptd_total;
                            }
                        }
                    }
                }
            }
        } else {
            $target = SubSubTarget::select('sstr_amount')
                    ->leftJoin('sub_targets', 'sub_targets.id', '=', 'sub_sub_targets.str_id')
                    ->where('st_id', '=', $st_id)
                    ->where('stt_id', '=', $stt_id)
                    ->whereDate('sstr_date', $start)
                    ->sum('sstr_amount');
            $pos_transaction = PosTransaction::select('id', 'pos_admin_cost', 'stt_id', 'pos_status', 'pos_refund')
            ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
            
            ->where('stt_id', '=', $stt_id)
            ->where('st_id', '=', $st_id)
            ->whereDate('pos_transactions.created_at', $start)
            ->get();
            if (!empty($pos_transaction)) {
                foreach ($pos_transaction as $pt) {
                    $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status')
                    ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                    ->where('pos_transaction_details.pt_id', $pt->id)
                    ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                    ->groupBy('pos_transaction_details.id')
                    ->get();
                    if (!empty($pos_transaction_detail)) {
                        $ptd_total = 0;
                        foreach ($pos_transaction_detail as $ptd) {
                            if ($pt->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                              continue;
                            }
                            if (!empty($ptd->pos_td_marketplace_price)) {
                                $ptd_total += $ptd->pos_td_marketplace_price;
                            } else {
                                $ptd_total += $ptd->pos_td_discount_price;
                            }
                        }
                        if ($ptd_total > 0) {
                            $get += $ptd_total - $pt->pos_admin_cost;
                        } else {
                            $get += $ptd_total;
                        }
                    }
                }
            }
        }
        $r['status'] = '200';
        $r['target'] = round($target);
        $r['get'] = $get;
        return json_encode($r);
    }

    public function crossChart(Request $request)
    {
        $type = $request->_type;
        $date = $request->_range;
        $st_id = $request->_st_id;
        $exp = explode('|', $date);
        $stt_id = 0;
        if (count($exp) > 1) {
            $start = $exp[0];
            $end = $exp[1];
        } else {
            $start = $exp[0];
            $end = null;
        }
        $exp_target = explode('-', $start);
        $year = $exp_target[0];
        $month = $exp_target[1];
        $tr_date = $year.'-'.$month;
        if ($type == 'online') {
            $stt_id = StoreType::select('id')->where('stt_name', '=', 'ONLINE')->get()->first()->id;
        } else {
            $stt_id = StoreType::select('id')->where('stt_name', '=', 'OFFLINE')->get()->first()->id;
        }
        $target = 0;
        $get = 0;
        if (!empty($end)) {
            if ($start == $end) {
                $pos_transaction = PosTransaction::select('id', 'pos_admin_cost', 'stt_id', 'pos_status', 'pos_refund')
                ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION', 'CANCEL', 'REFUND', 'EXCHANGE', 'UNPAID'])
                
                ->where('stt_id', '=', $stt_id)
                ->where('st_id_ref', '=', $st_id)
                ->whereDate('pos_transactions.created_at', $exp[0])
                ->get();
                if (!empty($pos_transaction)) {
                    foreach ($pos_transaction as $pt) {
                        $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status')
                        ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                        ->where('pos_transaction_details.pt_id', $pt->id)
                        ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                        ->groupBy('pos_transaction_details.id')
                        ->get();
                        if (!empty($pos_transaction_detail)) {
                            $ptd_total = 0;
                            foreach ($pos_transaction_detail as $ptd) {
                                if ($pt->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                                  continue;
                                }
                                if (!empty($ptd->pos_td_marketplace_price)) {
                                    $ptd_total += $ptd->pos_td_marketplace_price;
                                } else {
                                    $ptd_total += $ptd->pos_td_discount_price;
                                }
                            }
                            if ($ptd_total > 0) {
                                $get += $ptd_total - $pt->pos_admin_cost;
                            } else {
                                $get += $ptd_total;
                            }
                        }
                    }
                }
            } else {
                $pos_transaction = PosTransaction::select('id', 'pos_admin_cost', 'stt_id', 'pos_status', 'pos_refund')
                ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION', 'CANCEL', 'REFUND', 'EXCHANGE', 'UNPAID'])
                
                ->where('stt_id', '=', $stt_id)
                ->where('st_id_ref', '=', $st_id)
                ->whereDate('pos_transactions.created_at', '>=', $exp[0])
                ->whereDate('pos_transactions.created_at', '<=', $exp[1])
                ->get();
                if (!empty($pos_transaction)) {
                    foreach ($pos_transaction as $pt) {
                        $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status')
                        ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                        ->where('pos_transaction_details.pt_id', $pt->id)
                        ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                        ->groupBy('pos_transaction_details.id')
                        ->get();
                        if (!empty($pos_transaction_detail)) {
                            $ptd_total = 0;
                            foreach ($pos_transaction_detail as $ptd) {
                                if ($pt->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                                  continue;
                                }
                                if (!empty($ptd->pos_td_marketplace_price)) {
                                    $ptd_total += $ptd->pos_td_marketplace_price;
                                } else {
                                    $ptd_total += $ptd->pos_td_discount_price;
                                }
                            }
                            if ($ptd_total > 0) {
                                $get += $ptd_total - $pt->pos_admin_cost;
                            } else {
                                $get += $ptd_total;
                            }
                        }
                    }
                }
            }
        } else {
            $pos_transaction = PosTransaction::select('id', 'pos_admin_cost', 'stt_id', 'pos_status', 'pos_refund')
            ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION', 'CANCEL', 'REFUND', 'EXCHANGE', 'UNPAID'])
            
            ->where('stt_id', '=', $stt_id)
            ->where('st_id_ref', '=', $st_id)
            ->whereDate('pos_transactions.created_at', $start)
            ->get();
            if (!empty($pos_transaction)) {
                foreach ($pos_transaction as $pt) {
                    $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status')
                    ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                    ->where('pos_transaction_details.pt_id', $pt->id)
                    ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                    ->groupBy('pos_transaction_details.id')
                    ->get();
                    if (!empty($pos_transaction_detail)) {
                        $ptd_total = 0;
                        foreach ($pos_transaction_detail as $ptd) {
                            if ($pt->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                              continue;
                            }
                            if (!empty($ptd->pos_td_marketplace_price)) {
                                $ptd_total += $ptd->pos_td_marketplace_price;
                            } else {
                                $ptd_total += $ptd->pos_td_discount_price;
                            }
                        }
                        if ($ptd_total > 0) {
                            $get += $ptd_total - $pt->pos_admin_cost;
                        } else {
                            $get += $ptd_total;
                        }
                    }
                }
            }
        }
        $r['status'] = '200';
        $r['get'] = $get;
        return json_encode($r);
    }
}
