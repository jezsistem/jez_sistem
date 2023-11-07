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
use App\Models\ProductDiscountDetail;

class ArticleReportController extends Controller
{
    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PosTransactionDetail::select('pos_transaction_details.id as ptd_id', 'pos_transaction_details.created_at as ptd_created', 'pos_transaction_details.pst_id as pst_id', 'pos_invoice',
            'br_name', 'pc_name', 'psc_name', 'pssc_name', 'p_name', 'p_color', 'sz_name', 'pos_td_qty', 'ps_price_tag', 'p_price_tag', 'ps_sell_price', 'p_sell_price', 'pos_td_discount_price', 'pos_td_marketplace_price', 'pos_status', 'pos_refund')
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('product_categories', 'products.pc_id', '=', 'product_categories.id')
            ->leftJoin('product_sub_categories', 'products.psc_id', '=', 'product_sub_categories.id')
            ->leftJoin('product_sub_sub_categories', 'products.pssc_id', '=', 'product_sub_sub_categories.id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION', 'CANCEL', 'UNPAID'])
            ->where(function($w) use ($request) {
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
                  $w->whereDate('pos_transactions.created_at', '>=', $exp[0])
                  ->whereDate('pos_transactions.created_at', '<=', $exp[1]);
                } else {
                  $w->whereDate('pos_transactions.created_at', $start);
                }
              }
            })
            ->groupBy('pos_transaction_details.id'))
            ->editColumn('ptd_created', function($data){
                return date('d/m/Y H:i:s', strtotime($data->ptd_created));
            })
            ->editColumn('price_tag', function($data){
                if (!empty($data->ps_price_tag)) {
                  return $data->ps_price_tag;
                } else {
                  return $data->p_price_tag;
                }
            })
            ->editColumn('sell_price', function($data){
                if (!empty($data->ps_sell_price)) {
                  return $data->ps_sell_price;
                } else {
                  return $data->p_sell_price;
                }
            })
            ->editColumn('total_price', function($data){
                $total = 0;
                if (!empty($data->pos_td_marketplace_price)) {
                  $total = $data->pos_td_marketplace_price;
                } else {
                  $total = $data->pos_td_discount_price;
                }
                return $total;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('stt_id'))) {
                    $instance->where('pos_transactions.stt_id', $request->get('stt_id'));
                }
                if (!empty($request->st_id)) {
                  $instance->where('pos_transactions.st_id', '=', $request->st_id);
                }
                if (!empty($request->pt_id)) {
                  $instance->where('pos_transaction_details.pt_id', '=', $request->pt_id);
                }
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%")
                        ->orWhere('pos_invoice', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }
    
    public function getCrossDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PosTransactionDetail::select('pos_transaction_details.id as ptd_id', 'pos_transaction_details.created_at as ptd_created', 'pos_transaction_details.pst_id as pst_id', 'pos_invoice',
            'br_name', 'pc_name', 'psc_name', 'pssc_name','p_name', 'p_color', 'sz_name', 'pos_td_qty', 'ps_price_tag', 'p_price_tag', 'ps_sell_price', 'p_sell_price', 'pos_td_discount_price', 'pos_td_marketplace_price', 'pos_status')
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('product_categories', 'products.pc_id', '=', 'product_categories.id')
            ->leftJoin('product_sub_categories', 'products.psc_id', '=', 'product_sub_categories.id')
            ->leftJoin('product_sub_sub_categories', 'products.pssc_id', '=', 'product_sub_sub_categories.id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION', 'CANCEL', 'UNPAID'])
            ->where(function($w) use ($request) {
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
                  $w->whereDate('pos_transactions.created_at', '>=', $exp[0])
                  ->whereDate('pos_transactions.created_at', '<=', $exp[1]);
                } else {
                  $w->whereDate('pos_transactions.created_at', $start);
                }
              }
            })
            ->groupBy('pos_transaction_details.id'))
            ->editColumn('ptd_created', function($data){
                return date('d/m/Y H:i:s', strtotime($data->ptd_created));
            })
            ->editColumn('price_tag', function($data){
                if (!empty($data->ps_price_tag)) {
                  return $data->ps_price_tag;
                } else {
                  return $data->p_price_tag;
                }
            })
            ->editColumn('sell_price', function($data){
                if (!empty($data->ps_sell_price)) {
                  return $data->ps_sell_price;
                } else {
                  return $data->p_sell_price;
                }
            })
            ->editColumn('total_price', function($data){
                $total = 0;
                if (!empty($data->pos_td_marketplace_price)) {
                  $total = $data->pos_td_marketplace_price;
                } else {
                  $total = $data->pos_td_discount_price;
                }
                return $total;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('stt_id'))) {
                    $instance->where('pos_transactions.stt_id', $request->get('stt_id'));
                }
                if (!empty($request->st_id)) {
                  $instance->where('pos_transactions.st_id_ref', '=', $request->st_id);
                }
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(br_name," ", p_name," ", p_color," ", sz_name) LIKE ?', "%$search%")
                        ->orWhere('pos_invoice', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function depokOnlineSummary(Request $request)
    {
        $st_id = $request->_st_id;
        $stt_id = $request->_stt_id;
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
                  ->where('stt_id', '=', $stt_id)
                  ->whereDate('sstr_date', '>=', date('Y-m-01'))
                  ->whereDate('sstr_date', '<=', date('Y-m-t'))
                  ->sum('sstr_amount');

        $nett_sales = 0;
        $pos_transaction = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
        if (!empty($pos_transaction)) {
            foreach ($pos_transaction as $pt) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $pt->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $nett_sales += $ptd->pos_td_marketplace_price;
                        } else {
                            $nett_sales += $ptd->pos_td_discount_price;
                        }
                    }
                    $nett_sales = $nett_sales - $pt->pos_admin_cost;
                }
            }
        }

        $total_reseller = 0;
        $reseller = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where('pos_transactions.std_id', '=', '8')
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
        if (!empty($reseller)) {
            foreach ($reseller as $pt) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $pt->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $total_reseller += $ptd->pos_td_marketplace_price;
                        } else {
                            $total_reseller += $ptd->pos_td_discount_price;
                        }
                    }
                    $total_reseller = $total_reseller - $pt->pos_admin_cost;
                }
            }
        }

        $total_dropshipper = 0;
        $dropshipper = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where('pos_transactions.std_id', '=', '15')
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
        if (!empty($dropshipper)) {
            foreach ($dropshipper as $pt) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $pt->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $total_dropshipper += $ptd->pos_td_marketplace_price;
                        } else {
                            $total_dropshipper += $ptd->pos_td_discount_price;
                        }
                    }
                    $total_dropshipper = $total_dropshipper - $pt->pos_admin_cost;
                }
            }
        }

        $total_jdid = 0;
        $jdid = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where('pos_transactions.std_id', '=', '16')
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
        if (!empty($jdid)) {
            foreach ($jdid as $pt) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $pt->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $total_jdid += $ptd->pos_td_marketplace_price;
                        } else {
                            $total_jdid += $ptd->pos_td_discount_price;
                        }
                    }
                    $total_jdid = $total_jdid - $pt->pos_admin_cost;
                }
            }
        }

        $total_whatsapp = 0;
        $whatsapp = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where('pos_transactions.std_id', '=', '14')
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
        if (!empty($whatsapp)) {
            foreach ($whatsapp as $pt) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $pt->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $total_whatsapp += $ptd->pos_td_marketplace_price;
                        } else {
                            $total_whatsapp += $ptd->pos_td_discount_price;
                        }
                    }
                    $total_whatsapp = $total_whatsapp - $pt->pos_admin_cost;
                }
            }
        }

        $total_lazada = 0;
        $lazada = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where('pos_transactions.std_id', '=', '12')
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
        if (!empty($lazada)) {
            foreach ($lazada as $pt) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $pt->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $total_lazada += $ptd->pos_td_marketplace_price;
                        } else {
                            $total_lazada += $ptd->pos_td_discount_price;
                        }
                    }
                    $total_lazada = $total_lazada - $pt->pos_admin_cost;
                }
            }
        }

        $total_bukalapak = 0;
        $bukalapak = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where('pos_transactions.std_id', '=', '11')
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
        if (!empty($bukalapak)) {
            foreach ($bukalapak as $pt) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $pt->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $total_bukalapak += $ptd->pos_td_marketplace_price;
                        } else {
                            $total_bukalapak += $ptd->pos_td_discount_price;
                        }
                    }
                    $total_bukalapak = $total_bukalapak - $pt->pos_admin_cost;
                }
            }
        }

        $total_tokopedia = 0;
        $tokopedia = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where('pos_transactions.std_id', '=', '10')
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
        if (!empty($tokopedia)) {
            foreach ($tokopedia as $pt) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $pt->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $total_tokopedia += $ptd->pos_td_marketplace_price;
                        } else {
                            $total_tokopedia += $ptd->pos_td_discount_price;
                        }
                    }
                    $total_tokopedia = $total_tokopedia - $pt->pos_admin_cost;
                }
            }
        }

        $total_shopee = 0;
        $shopee = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where('pos_transactions.std_id', '=', '9')
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
        if (!empty($shopee)) {
            foreach ($shopee as $pt) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $pt->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $total_shopee += $ptd->pos_td_marketplace_price;
                        } else {
                            $total_shopee += $ptd->pos_td_discount_price;
                        }
                    }
                    $total_shopee = $total_shopee - $pt->pos_admin_cost;
                }
            }
        }

        $total_website = 0;
        $website = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where('pos_transactions.std_id', '=', '13')
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
        if (!empty($website)) {
            foreach ($website as $pt) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $pt->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $total_shopee += $ptd->pos_td_marketplace_price;
                        } else {
                            $total_shopee += $ptd->pos_td_discount_price;
                        }
                    }
                    $total_shopee = $total_shopee - $pt->pos_admin_cost;
                }
            }
        }

        $total_omset = 0;
        $omset = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', '>=', date('Y-m-01', strtotime($start)))
              ->whereDate('pos_transactions.created_at', '<=', $end);
            }
        })
        ->get();
        if (!empty($omset)) {
            foreach ($omset as $om) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $om->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $total_omset += $ptd->pos_td_marketplace_price;
                        } else {
                            $total_omset += $ptd->pos_td_discount_price;
                        }
                    }
                    $total_omset = $total_omset - $pt->pos_admin_cost;
                }
            }
        }

        $r['status'] = '200';
        $r['target'] = $target;
        $r['omset_global'] = $nett_sales;
        $r['reseller'] = $total_reseller;
        $r['dropshipper'] = $total_dropshipper;
        $r['jdid'] = $total_jdid;
        $r['whatsapp'] = $total_whatsapp;
        $r['website'] = $total_website;
        $r['lazada'] = $total_lazada;
        $r['bukalapak'] = $total_bukalapak;
        $r['tokopedia'] = $total_tokopedia;
        $r['shopee'] = $total_shopee;
        $r['total_omset'] = $total_omset;
        return json_encode($r);
    }

    public function depokOfflineSummary(Request $request)
    {
        $st_id = $request->_st_id;
        $stt_id = $request->_stt_id;
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
                  ->where('stt_id', '=', $stt_id)
                  ->whereDate('sstr_date', '>=', date('Y-m-01'))
                  ->whereDate('sstr_date', '<=', date('Y-m-t'))
                  ->sum('sstr_amount');

        $nett_sales = 0;
        $pos_transaction = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where('pos_transactions.stt_id', '=', $stt_id)
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })->get();
        if (!empty($pos_transaction)) {
            foreach ($pos_transaction as $pt) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $pt->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $nett_sales += $ptd->pos_td_marketplace_price;
                        } else {
                            $nett_sales += $ptd->pos_td_discount_price;
                        }
                    }
                    $nett_sales = $nett_sales - $pt->pos_admin_cost;
                }
            }
        }

        $total_debit = 0;
        $total_cash = 0;
        $total_retur = 0;
        $total_debit_bca = 0;
        $total_debit_bri = 0;
        $total_debit_bni = 0;
        $debit = PosTransaction::select('id', 'pm_id', 'pm_id_partial', 'pos_payment', 'pos_payment_partial', 'cp_id', 'cp_id_partial')
        ->where('pos_transactions.st_id', '=', $st_id)
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
              $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_discount_price', 'plst_status', 'pos_refund')
              ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
              ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
              ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
              ->where('pos_transactions.id', $db->id)
              ->groupBy('pos_transaction_details.id')
              ->get();
              if (!empty($pos_transaction_detail)) {
                  $sales_total = 0;
                  foreach ($pos_transaction_detail as $ptd) {
                      if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                        continue;
                      }
                      $sales_total += $ptd->pos_td_discount_price;
                      if ($ptd->pos_td_discount_price < 0) {
                          $total_retur += $ptd->pos_td_discount_price;
                      }
                  }
                  if (!empty($db->pm_id_partial)) {
                      if ($db->pm_id_partial == '6' || $db->pm_id_partial == '7') {
                          $total_debit += $sales_total - $db->pos_payment;
                      }
                      if ($db->pm_id_partial == '6' AND $db->cp_id_partial == '2') {
                          $total_debit_bca += $sales_total - $db->pos_payment;
                      }
                      if ($db->pm_id_partial == '6' AND $db->cp_id_partial == '3') {
                          $total_debit_bri += $sales_total - $db->pos_payment;
                      }
                      if ($db->pm_id_partial == '6' AND $db->cp_id_partial == '1') {
                          $total_debit_bni += $sales_total - $db->pos_payment;
                      }
                      if ($db->pm_id_partial == '7' AND $db->cp_id_partial == '2') {
                          $total_debit_bca += $sales_total - $db->pos_payment;
                      }
                      if ($db->pm_id_partial == '7' AND $db->cp_id_partial == '3') {
                          $total_debit_bri += $sales_total - $db->pos_payment;
                      }
                      if ($db->pm_id_partial == '7' AND $db->cp_id_partial == '1') {
                          $total_debit_bni += $sales_total - $db->pos_payment;
                      }
                      if ($db->pm_id_partial == '1') {
                          $total_cash += $sales_total - $db->pos_payment;
                      }
                      if ($db->pm_id == '1' || $db->pm_id == null) {
                          $total_cash += $sales_total - $db->pos_payment_partial;
                      }
                  } else {
                      if ($db->pm_id == '6' || $db->pm_id == '7') {
                          $total_debit += $sales_total;
                      }
                      if ($db->pm_id == '6' AND $db->cp_id == '2') {
                          $total_debit_bca += $sales_total;
                      }
                      if ($db->pm_id == '6' AND $db->cp_id == '3') {
                          $total_debit_bri += $sales_total;
                      }
                      if ($db->pm_id == '6' AND $db->cp_id == '1') {
                          $total_debit_bni += $sales_total;
                      }
                      if ($db->pm_id == '7' AND $db->cp_id == '2') {
                          $total_debit_bca += $sales_total;
                      }
                      if ($db->pm_id == '7' AND $db->cp_id == '3') {
                          $total_debit_bri += $sales_total;
                      }
                      if ($db->pm_id == '7' AND $db->cp_id == '1') {
                          $total_debit_bni += $sales_total;
                      }
                      if ($db->pm_id == '1' || $db->pm_id == null) {
                          $total_cash += $sales_total;
                      }
                  }
              }
          }
        }

        $total_omset = 0;
        $omset = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', '>=', date('Y-m-01', strtotime($start)))
              ->whereDate('pos_transactions.created_at', '<=', $end);
            }
        })
        ->get();
        if (!empty($omset)) {
            foreach ($omset as $om) {
                $pos_transaction_detail = PosTransactionDetail::select('pos_td_qty', 'pos_td_marketplace_price', 'pos_td_discount_price', 'plst_status', 'pos_refund')
                ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transactions.id')
                ->whereIn('plst_status', ['DONE', 'WAITING FOR PACKING', 'WAITING ONLINE', 'WAITING FOR NAMESET', 'INSTOCK'])
                ->where('pos_transactions.id', $om->id)
                ->groupBy('pos_transaction_details.id')
                ->get();
                if (!empty($pos_transaction_detail)) {
                    foreach ($pos_transaction_detail as $ptd) {
                        if ($ptd->pos_refund == '0' AND $ptd->plst_status == 'INSTOCK') {
                          continue;
                        }
                        if (!empty($ptd->pos_td_marketplace_price)) {
                            $total_omset += $ptd->pos_td_marketplace_price;
                        } else {
                            $total_omset += $ptd->pos_td_discount_price;
                        }
                    }
                    $total_omset = $total_omset - $pt->pos_admin_cost;
                }
            }
        }

        $r['status'] = '200';
        $r['target'] = $target;
        $r['omset_global'] = $nett_sales;
        $r['total_omset'] = $total_omset;
        $r['total_debit'] = $total_debit;
        $r['total_retur'] = $total_retur;
        $r['total_debit_bca'] = $total_debit_bca;
        $r['total_debit_bri'] = $total_debit_bri;
        $r['total_debit_bni'] = $total_debit_bni;
        $r['total_cash'] = $total_cash;
        return json_encode($r);
    }
}
