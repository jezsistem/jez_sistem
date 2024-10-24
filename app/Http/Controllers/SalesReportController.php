<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\Store;
use App\Models\StoreType;
use App\Models\SubSubTarget;
use App\Exports\ArticleReportExport;
use Maatwebsite\Excel\Facades\Excel;


class SalesReportController extends Controller
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
            'segment' => request()->segment(1),
            'st_id' => Store::where('st_delete', '!=', '1')->orderByDesc('id')->pluck('st_name', 'id'),
            'stt_id' => StoreType::where('stt_delete', '!=', '1')->whereIn('stt_name', ['ONLINE', 'OFFLINE'])->orderByDesc('id')->pluck('stt_name', 'id'),
        ];
        return view('app.report.sales_report.sales_report', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PosTransactionDetail::select(
                'pos_transaction_details.id as ptd_id', 'pos_refund', 'stkt_name', 'cross_order', 'cp_id',
                'cp_id_partial', 'pos_payment', 'pos_payment_partial', 'pc_name', 'pm_id', 'pm_id_partial',
                'psc_name', 'pssc_name', 'product_stocks.id as pst_id', 'pos_note', 'plst_status', 'p_price_tag',
                'ps_price_tag', 'p_sell_price', 'ps_sell_price', 'pos_transactions.created_at as pos_created',
                'pos_td_qty', 'pos_invoice', 'pos_td_discount', 'pos_td_discount_price', 'pos_another_cost',
                'pos_td_marketplace_price', 'pos_td_nameset_price', 'pos_shipping', 'pos_unique_code', 'pos_real_price',
                'pos_admin_cost', 'u_name', 'stt_name', 'dv_name', 'p_name', 'br_name', 'sz_name', 'p_color', 'pos_status')
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('product_categories', 'products.pc_id', '=', 'product_categories.id')
            ->leftJoin('product_sub_categories', 'products.psc_id', '=', 'product_sub_categories.id')
            ->leftJoin('product_sub_sub_categories', 'products.pssc_id', '=', 'product_sub_sub_categories.id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'pos_transactions.stt_id')
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->leftJoin('stock_types', 'purchase_order_article_detail_statuses.stkt_id', '=', 'stock_types.id')
            ->groupBy('pos_transaction_details.id')
            ->where('pos_transaction_details.pos_td_reject', '!=', '1')
            ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK']))
            ->editColumn('pos_created', function($data){
                return '<span style="white-space: nowrap;">'.date('d/m/Y H:i:s', strtotime($data->pos_created)).'</span>';
            })
            ->editColumn('cross', function($data){
                if ($data->cross_order == '1') {
                    return 'Ya';
                } else {
                    return '-';
                }
            })
            ->editColumn('u_name', function($data){
                return '<span style="white-space: nowrap;">'.$data->u_name.'</span>';
            })
            ->editColumn('p_name', function($data){
                return '<span style="white-space: nowrap;">'.$data->p_name.'</span>';
            })
            ->editColumn('br_name', function($data){
                return '<span style="white-space: nowrap;">'.$data->br_name.'</span>';
            })
            ->editColumn('p_color', function($data){
                return '<span style="white-space: nowrap;">'.$data->p_color.'</span>';
            })
            ->editColumn('sz_name', function($data){
                return '<span style="white-space: nowrap;">'.$data->sz_name.'</span>';
            })
            ->editColumn('pm_one', function($data){
                $payment_method = '';
                $card_provider = '';
                if (!empty($data->pm_id)) {
                  $payment_method = DB::table('payment_methods')->select('pm_name')->where('id', '=', $data->pm_id)->get()->first()->pm_name;
                }
                if (!empty($data->cp_id)) {
                  $card_provider = DB::table('card_providers')->select('cp_name')->where('id', '=', $data->cp_id)->get()->first()->cp_name;
                }
                return $payment_method.' '.$card_provider;
            })
            ->editColumn('pm_two', function($data){
                $payment_method = '';
                $card_provider = '';
                if (!empty($data->pm_id_partial)) {
                  $payment_method = DB::table('payment_methods')->select('pm_name')->where('id', '=', $data->pm_id_partial)->get()->first()->pm_name;
                }
                if (!empty($data->cp_id_partial)) {
                  $card_provider = DB::table('card_providers')->select('cp_name')->where('id', '=', $data->cp_id_partial)->get()->first()->cp_name;
                }
                if (!empty($data->pos_payment_partial)) {
                  if (empty($payment_method)) {
                    return 'CASH';
                  } else {
                    return $payment_method.' '.$card_provider;
                  }
                } else {
                  return '';
                }
            })
            ->editColumn('price_tag', function($data){
                if (!empty($data->ps_price_tag)) {
                    return number_format($data->ps_price_tag);
                } else {
                    return number_format($data->p_price_tag);
                }
            })
            ->editColumn('sell_price', function($data){
                if (!empty($data->ps_sell_price)) {
                    return number_format($data->ps_sell_price);
                } else {
                    return number_format($data->p_sell_price);
                }
            })
            ->editColumn('hpp', function($data){
                $poads = DB::table('purchase_order_article_detail_statuses')
                ->select('poads_purchase_price', 'ps_purchase_price')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                ->where('product_stocks.id', '=', $data->pst_id)
                ->whereNotNull('purchase_order_article_detail_statuses.poad_id')
                ->groupBy('poads_purchase_price')
                ->get()->first();
                if (!empty($poads->poads_purchase_price)) {
                    return number_format($poads->poads_purchase_price);
                } else if (!empty($poads->ps_purchase_price)) {
                    return number_format($poads->ps_purchase_price);
                } else {
                    return '-';
                }
            })
            ->editColumn('pos_td_discount_price', function($data){
                $discount_price = null;
                if ($data->plst_status == 'INSTOCK' AND $data->pos_refund == '0') {
                    $discount_price = '';
                } else {
                    $discount_price = $data->pos_td_discount_price;
                }
                return number_format($discount_price);
            })
            ->editColumn('total_price', function($data){
                $real_price = null;
                if (strtolower($data->dv_name) == 'offline') {
                  $real_price = $data->pos_td_qty * $data->pos_td_discount_price;
                } else {
                  $real_price = $data->pos_real_price;
                }
                return number_format($real_price);
            })
            ->editColumn('plst_status', function($data){
                if ($data->plst_status == 'WAITING OFFLINE') {
                    $btn = 'btn-warning';
                } else if ($data->plst_status == 'REJECT') {
                    $btn = 'btn-danger';
                } else if ($data->plst_status == 'WAITING ONLINE') {
                    $btn = 'btn-light-warning';
                } else if ($data->plst_status == 'WAITING FOR PACKING') {
                    $btn = 'btn-info';
                } else if ($data->plst_status == 'WAITING FOR CHECKOUT') {
                    $btn = 'btn-info';
                } else if ($data->plst_status == 'WAITING TO TAKE') {
                    $btn = 'btn-info';
                } else if ($data->plst_status == 'WAITING FOR CONFIRMATION') {
                    $btn = 'btn-warning';
                } else if ($data->plst_status == 'WAITING FOR NAMESET') {
                    $btn = 'btn-info';
                } else if ($data->plst_status == 'DRAFT OFFLINE') {
                    $btn = 'btn-warning';
                } else if ($data->plst_status == 'COMPLAINT') {
                    $btn = 'btn-danger';
                } else if ($data->plst_status == 'EXCHANGE') {
                    $btn = 'btn-danger';
                }  else if ($data->plst_status == 'DONE') {
                    $btn = 'btn-success';
                } else if ($data->plst_status == 'REFUND') {
                    $btn = 'btn-danger';
                } else if ($data->plst_status == 'INSTOCK') {
                    $btn = 'btn-primary';
                } else if ($data->pos_status == 'CANCEL') {
                    $btn = 'btn-danger';
                }
                return '<span style="white-space: nowrap;" class="btn btn-sm '.$btn.'">'.$data->plst_status.'</span>';
            })

            ->rawColumns(['pos_created', 'u_name', 'beli', 'p_name', 'br_name', 'p_color', 'sz_name', 'purchase_price', 'nameset', 'plst_status'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('stt_id'))) {
                    $instance->where('pos_transactions.stt_id', $request->get('stt_id'));
                }
                if (!empty($request->sales_date)) {
                  $range = $request->sales_date;
                  $exp = explode('|', $range);
                  if (count($exp) > 1) {
                    $start = $exp[0];
                    $end = $exp[1];
                  } else {
                    $start = $request->sales_date;
                    $end = $request->sales_date;
                  }
                  if ($start != $end) {
                    $instance->whereDate('pos_transactions.created_at', '>=', $exp[0])
                    ->whereDate('pos_transactions.created_at', '<=', $exp[1]);
                  } else {
                    $instance->whereDate('pos_transactions.created_at', $start);
                  }
                }
                if (!empty($request->st_id)) {
                  $instance->where('pos_transactions.st_id', '=', $request->st_id);
                }
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('pos_invoice', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }
    
    public function cabangSummary(Request $request)
    {
        $st_id = $request->_st_id;
        $date = $request->_date;
        $start = null;
        $end = null;
        $range = null;
        if (!empty($date)) {
          $exp = explode('|', $date);
          if (count($exp) > 1) {
            $start = $exp[0];
            $end = $exp[1];
          } else {
            $start = $date;
            $end = $date;
          }
        }

        if ($start != $end) {
          $range = 'true';
        } else {
          $range = 'false';
        }

        $target = SubSubTarget::select('sstr_amount')
                  ->leftJoin('sub_targets', 'sub_targets.id', '=', 'sub_sub_targets.str_id')
                  ->where('st_id', '=', $st_id)
                  ->whereDate('sstr_date', '>=', date('Y-m-01'))
                  ->whereDate('sstr_date', '<=', date('Y-m-t'))
                  ->sum('sstr_amount');

        $nett_sales = 0;
        $cross = 0;

        $pos_transaction = PosTransaction::select('id', 'pos_admin_cost', 'pos_status', 'pos_refund')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
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
                        $nett_sales += $ptd_total - $pt->pos_admin_cost;
                    } else {
                        $nett_sales += $ptd_total;
                    }
                }
            }
        }
        if ($st_id != '1') {
            $pos_cross = PosTransaction::select('id', 'pos_admin_cost', 'pos_status', 'pos_refund')
            ->where('pos_transactions.st_id_ref', '=', $st_id)
            ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
            ->where(function($w) use ($start, $end, $range) {
                if ($range == 'true') {
                $w->whereBetween('pos_transactions.created_at', [$start, $end]);
                } else {
                $w->whereDate('pos_transactions.created_at', $start);
                }
            })->get();
            if (!empty($pos_cross)) {
                foreach ($pos_cross as $pt) {
                    $pos_transaction_cross = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status')
                    ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                    ->where('pos_transaction_details.pt_id', $pt->id)
                    ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                    ->groupBy('pos_transaction_details.id')
                    ->get();
                    if (!empty($pos_transaction_cross)) {
                        $ptd_total = 0;
                        foreach ($pos_transaction_cross as $ptd) {
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
                            $cross += $ptd_total - $pt->pos_admin_cost;
                        } else {
                            $cross += $ptd_total;
                        }
                    }
                }
            }
        }

        $total_debit = 0;
        $total_cash = 0;
        $total_debit_bca = 0;
        $total_debit_bri = 0;
        $total_debit_bni = 0;
        $total_debit_mandiri = 0;
        $total_qr = 0;
        $total_transfer = 0;

        $debit = PosTransaction::select('id', 'pm_id', 'pm_id_partial', 'pos_payment', 'pos_payment_partial', 'cp_id', 'cp_id_partial', 'pos_status', 'pos_refund')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION','CANCEL', 'UNPAID'])
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })
        ->get();
        if (!empty($debit)) {
            foreach ($debit as $db) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_discount_price', 'plst_status')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                ->where('pos_transaction_details.pt_id', $db->id)
                    ->where('pos_transaction_details.pos_td_reject', '!=', '1')
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    $sales_total = 0;
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($db->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                            continue;
                        }
                        $sales_total += $ptd->pos_td_discount_price;
                    }
                    if (!empty($db->pm_id_partial)) {
                        if ($db->pm_id_partial != '1') {
                            $total_debit += $db->pos_payment_partial;
                        }
                        if (!in_array($db->pm_id_partial, ['1', '5']) AND $db->cp_id_partial == '2') {
                            $total_debit_bca += $db->pos_payment_partial;
                        }
                        if (!in_array($db->pm_id_partial, ['1', '5']) AND $db->cp_id_partial == '3') {
                            $total_debit_bri += $db->pos_payment_partial;
                        }
                        if (!in_array($db->pm_id_partial, ['1', '5']) AND $db->cp_id_partial == '1') {
                            $total_debit_bni += $db->pos_payment_partial;
                        }
                        if (!in_array($db->pm_id_partial, ['1', '5']) AND $db->cp_id_partial == '4') {
                            $total_debit_mandiri += $db->pos_payment_partial;
                        }
                        if ($db->pm_id_partial == '5') {
                            $total_qr += $sales_total;
                        }
                        if ($db->pm_id_partial == '1') {
                            $total_cash += $db->pos_payment_partial;
                        }
                        if ($db->pm_id_partial == '2' AND $db->cp_id_partial == null) {
                            $total_transfer += $sales_total;
                        }

                        if ($db->pm_id != '1') {
                            $total_debit += $db->pos_payment;
                        }
                        if (!in_array($db->pm_id, ['1', '5']) AND $db->cp_id == '2') {
                            $total_debit_bca += $db->pos_payment;
                        }
                        if (!in_array($db->pm_id, ['1', '5']) AND $db->cp_id == '3') {
                            $total_debit_bri += $db->pos_payment;
                        }
                        if (!in_array($db->pm_id, ['1', '5']) AND $db->cp_id == '1') {
                            $total_debit_bni += $db->pos_payment;
                        }
                        if (!in_array($db->pm_id, ['1', '5']) AND $db->cp_id == '4') {
                            $total_debit_mandiri += $db->pos_payment;
                        }
                        if ($db->pm_id == '5') {
                            $total_qr += $sales_total;
                        }
                        if ($db->pm_id == '1') {
                            $total_cash += $sales_total;
                        }
                        if ($db->pm_id == '2' AND $db->cp_id == null) {
                            $total_transfer += $sales_total;
                        }

                    } else {
                        if ($db->pm_id != '1') {
                            $total_debit += $sales_total;
                        }
                        if (!in_array($db->pm_id, ['1', '5']) AND $db->cp_id == '2') {
                            $total_debit_bca += $sales_total;
                        }
                        if (!in_array($db->pm_id, ['1', '5']) AND $db->cp_id == '3') {
                            $total_debit_bri += $sales_total;
                        }
                        if (!in_array($db->pm_id, ['1', '5']) AND $db->cp_id == '1') {
                            $total_debit_bni += $sales_total;
                        }
                        if (!in_array($db->pm_id, ['1', '5']) AND $db->cp_id == '4') {
                            $total_debit_mandiri += $sales_total;
                        }
                        if ($db->pm_id == '1' || $db->pm_id == null) {
                            $total_cash += $sales_total;
                        }
                        if ($db->pm_id == '5') {
                            $total_qr += $sales_total;
                        }
                        if ($db->pm_id == '2' AND $db->cp_id == null) {
                            $total_transfer += $sales_total;
                        }
                    }
                }
            }
        }

        $r['status'] = '200';
        $r['omset_global'] = ($nett_sales+$cross);
        $r['total_debit'] = $total_debit;
        $r['total_debit_bca'] = $total_debit_bca;
        $r['total_debit_bri'] = $total_debit_bri;
        $r['total_debit_bni'] = $total_debit_bni;
        $r['total_debit_mandiri'] = $total_debit_mandiri;
        $r['total_cash'] = $total_cash;
        $r['total_qr'] = $total_qr;
        $r['total_transfer'] = $total_transfer;
        $r['cross_order'] = $cross;
        return json_encode($r);
    }
    
    public function exportData(Request $request)
    {
        try {
            $type = $request->get('type');
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
            $stt_id = $request->get('stt_id');
            $st_id = $request->get('st_id');
            $dp_id = $request->get('dp_id');


        // Mendapatkan tanggal dan waktu saat ini
        $now = new \DateTime();
        $timestamp = $now->format('d-m-Y_H.i');

        // Membuat nama file dengan format yang diinginkan
        $fileName = 'Export_Laporan_' . $timestamp . '.xlsx';



            return Excel::download(new ArticleReportExport($type, $start, $end, $stt_id, $st_id, $dp_id), $fileName);

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    public function hbhjDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(DB::table('product_stocks')
            ->selectRaw("ts_product_stocks.id as id, br_name, p_name, p_color, sz_name, psc_name, avg(ts_purchase_order_article_detail_statuses.poads_purchase_price) as purchase, avg(ts_purchase_order_article_details.poad_purchase_price) as purchase2, ps_purchase_price, p_purchase_price, ps_sell_price, p_sell_price, p_price_tag, ps_price_tag")
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_sub_categories', 'product_sub_categories.id', '=', 'products.psc_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
            ->leftJoin('purchase_order_article_detail_statuses', 'purchase_order_article_detail_statuses.poad_id', '=', 'purchase_order_article_details.id')
            ->groupBy('product_stocks.id'))
            ->editColumn('hb', function($data){
                $purchase = 0;
                if (!empty ($data->purchase)) {
                    $purchase = round($data->purchase);
                } else if (!empty($data->purchase2)) {
                    $purchase = round($data->purchase2);
                } else {
                    if (!empty($row->ps_purchase_price)) {
                        $purchase = $data->ps_purchase_price;
                    } else {
                        $purchase = $data->p_purchase_price;
                    }
                }
                return number_format($purchase);
            })
            ->editColumn('hj', function($data){
                $sell_price = 0;
                if (!empty($data->p_sell_price)) {
                    $sell_price = $data->p_sell_price;
                } else {
                    $sell_price = $data->ps_sell_price;
                }
                return number_format($sell_price);
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
}
