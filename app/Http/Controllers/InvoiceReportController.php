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
use App\Models\PaymentMethod;
use App\Models\CardProvider;

class InvoiceReportController extends Controller
{
    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(PosTransaction::select(
                'pos_transactions.id as pt_id', 'pos_transactions.created_at as pos_created', 'pos_invoice', 'pos_shipping', 'pos_unique_code',
                'pos_admin_cost', 'pos_discount_seller','pos_another_cost', 'dv_name', 'cross_order', 'u_name', 'pos_payment', 'pos_payment_partial',
                'pos_note', 'pm_id', 'pm_id_partial', 'cp_id', 'cp_id_partial', 'cust_name', 'pos_refund', 'pos_status',
                'pos_card_number', 'pos_ref_number', 'pos_card_number_two', 'pos_ref_number_two')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->leftJoin('users', 'users.id', '=', 'pos_transactions.u_id')
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
            ->whereNotIn('pos_status', ['WAITING FOR CONFIRMATION', 'CANCEL']))
            ->editColumn('pos_created', function($data){
                return '<span style="white-space: nowrap;">'.date('d/m/Y H:i:s', strtotime($data->pos_created)).'</span>';
            })
            ->editColumn('u_name', function($data){
                return '<span style="white-space: nowrap;">'.$data->u_name.'</span>';
            })
            ->editColumn('cust_name', function($data){
                return '<span style="white-space: nowrap;">'.$data->cust_name.'</span>';
            })
            ->editColumn('pos_invoice', function($data){
                return '<span style="white-space: nowrap;" class="btn-sm btn-primary" data-pt_id="'.$data->pt_id.'" id="invoice_detail_btn">'.$data->pos_invoice.'</span>';
            })
            ->editColumn('cross', function($data){
                if ($data->cross_order == '1') {
                    return 'Ya';
                } else {
                    return '-';
                }
            })
            ->editColumn('item_qty', function($data){
                $qty = 0;
                $item = PosTransactionDetail::select('pos_transaction_details.pt_id', 'pos_td_qty', 'plst_status')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                ->where('pos_transaction_details.pt_id', '=', $data->pt_id)
                ->groupBy('pos_transaction_details.id')->get();
                foreach ($item as $row) {
                  if ($data->pos_refund == '0' AND $row->plst_status == 'INSTOCK') {
                    continue;
                  } else {
                    $qty += $row->pos_td_qty;
                  }
                }
                return $qty;
            })
            ->editColumn('item_value', function($data){
                $total = 0;
                $ptd = PosTransactionDetail::select('pos_td_qty', 'pos_td_discount_price', 'pos_td_marketplace_price', 'pos_td_nameset_price', 'plst_status')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                ->where('pos_transaction_details.pt_id', '=', $data->pt_id)
                ->groupBy('pos_transaction_details.id')->get();
                if (!empty($ptd)) {
                  foreach($ptd as $row) {
                    if ($data->pos_refund == '0' AND $row->plst_status == 'INSTOCK') {
                      continue;
                    }
                    if (!empty($row->pos_td_marketplace_price)) {
                      $total += $row->pos_td_marketplace_price;
                    } else {
                      $total += $row->pos_td_discount_price;
                    }
                  }
                  return $total;
                } else {
                  return '-';
                }
            })
            ->editColumn('nameset', function($data){
                $item_qty = PosTransactionDetail::select('pos_td_nameset_price')->where('pt_id', '=', $data->pt_id)->sum('pos_td_nameset_price');
                return $item_qty;
            })
            ->editColumn('payment_one', function($data){
                $payment_one = '';
                $card_provider_one = '';
                if (!empty($data->pm_id)) {
                  $payment_one = PaymentMethod::select('pm_name')->where('id', '=', $data->pm_id)->get()->first()->pm_name;
                }
                if (!empty($data->cp_id)) {
                  $card_provider_one = CardProvider::select('cp_name')->where('id', '=', $data->cp_id)->get()->first()->cp_name;
                }
                return $payment_one.' '.$card_provider_one;
            })
            ->editColumn('payment_two', function($data){
                $payment_two = '';
                $card_provider_two = '';
                if (!empty($data->pm_id_partial)) {
                  $payment_two = PaymentMethod::select('pm_name')->where('id', '=', $data->pm_id_partial)->get()->first()->pm_name;
                }
                if (!empty($data->cp_id_partial)) {
                  $card_provider_two = CardProvider::select('cp_name')->where('id', '=', $data->cp_id_partial)->get()->first()->cp_name;
                }
                return $payment_two.' '.$card_provider_two;
            })
            ->editColumn('value_admin', function($data){
                $total = 0;
                $ptd = PosTransactionDetail::select('pos_td_qty', 'pos_td_discount_price', 'pos_td_marketplace_price', 'pos_td_nameset_price', 'plst_status')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                ->where('pos_transaction_details.pt_id', '=', $data->pt_id)
                ->groupBy('pos_transaction_details.id')->get();
                if (!empty($ptd)) {
                  $ptd_total = 0;
                  foreach($ptd as $row) {
                    if ($data->pos_refund == '0' AND $row->plst_status == 'INSTOCK') {
                      continue;
                    }
                    if (!empty($row->pos_td_marketplace_price)) {
                      $ptd_total += $row->pos_td_marketplace_price;
                    } else {
                      $ptd_total += $row->pos_td_discount_price;
                    }
                  }
                  if ($ptd_total > 0) {
                    $total = $ptd_total - $data->pos_admin_cost;
                  } else {
                    $total = $ptd_total;
                  }
                  return $total;
                } else {
                  return '-';
                }
            })
            ->editColumn('total', function($data){
                $total = 0;
                $ptd = PosTransactionDetail::select('pos_td_qty', 'pos_td_discount_price', 'pos_td_marketplace_price', 'pos_td_nameset_price', 'plst_status')
                ->leftJoin('product_location_setup_transactions', 'product_location_setup_transactions.pt_id', '=', 'pos_transaction_details.pt_id')
                ->where('pos_transaction_details.pt_id', '=', $data->pt_id)
                ->groupBy('pos_transaction_details.id')->get();
                if (!empty($ptd)) {
                  $ptd_total = 0;
                  foreach($ptd as $row) {
                    if ($data->pos_refund == '0' AND $row->plst_status == 'INSTOCK') {
                      continue;
                    }
                    if (!empty($row->pos_td_marketplace_price)) {
                      $ptd_total += $row->pos_td_marketplace_price + $row->pos_td_nameset_price;
                    } else {
                      $ptd_total += $row->pos_td_discount_price + $row->pos_td_nameset_price;
                    }
                  }
                  if ($ptd_total > 0) {
                    $total = $ptd_total - $data->pos_admin_cost + $data->pos_another_cost + $data->pos_shipping + $data->pos_unique_code;
                  } else {
                    $total = $ptd_total - $data->pos_admin_cost + $data->pos_another_cost + $data->pos_shipping + $data->pos_unique_code;
                  }
                  return $total;
                } else {
                  return '-';
                }
            })
            ->rawColumns(['pos_created', 'u_name', 'pos_invoice', 'cust_name'])
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
                        $w->orWhere('pos_invoice', 'LIKE', "%$search%")
                        ->orWhere('u_name', 'LIKE', "%$search%")
                        ->orWhere('dv_name', 'LIKE', "%$search%")
                        ->orWhere('cust_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function bandungGlobalSummary(Request $request)
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

        $nett_sales = 0;
        $pos_transaction = PosTransaction::select('id', 'pos_admin_cost')
        ->where('pos_transactions.st_id', '=', $st_id)
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

        $total_transfer = 0;
        $transfer = PosTransaction::select('id', 'pm_id', 'pm_id_partial', 'pos_payment', 'pos_payment_partial')
        ->where('pos_transactions.st_id', '=', $st_id)
        ->where(function($w) use ($start, $end, $range) {
            if ($range == 'true') {
              $w->whereBetween('pos_transactions.created_at', [$start, $end]);
            } else {
              $w->whereDate('pos_transactions.created_at', $start);
            }
        })
        ->get();
        if (!empty($transfer)) {
          foreach ($transfer as $tf) {
              if ($tf->pm_id == '2') {
                  $total_transfer += $tf->pos_payment;
              }
              if ($tf->pm_id_partial == '2') {
                  $total_transfer += $tf->pos_payment_partial;
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
                      if ($db->pm_id_partial == '6' AND $db->cp_id_partial == null) {
                          $total_debit_bca += $sales_total - $db->pos_payment;
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
                      if ($db->pm_id == '1'  || $db->pm_id == null) {
                          $total_cash += $sales_total - $db->pos_payment_partial;
                      }
                  } else {
                      if ($db->pm_id == '6' || $db->pm_id == '7') {
                          $total_debit += $sales_total;
                      }
                      if ($db->pm_id == '6' AND $db->cp_id == null) {
                          $total_debit_bca += $sales_total;
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

        $target = SubSubTarget::select('sstr_amount')
                  ->leftJoin('sub_targets', 'sub_targets.id', '=', 'sub_sub_targets.str_id')
                  ->where('st_id', '=', $st_id)
                  ->whereDate('sstr_date', '>=', date('Y-m-01'))
                  ->whereDate('sstr_date', '<=', date('Y-m-t'))
                  ->sum('sstr_amount');

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
        $r['total_debit'] = $total_debit;
        $r['total_debit_bca'] = $total_debit_bca;
        $r['total_debit_bri'] = $total_debit_bri;
        $r['total_transfer'] = $total_transfer;
        $r['total_cash'] = $total_cash;
        $r['total_omset'] = $total_omset;
        return json_encode($r);
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
