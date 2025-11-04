<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SettlementDetailTransactionExport implements FromCollection, WithHeadings
{
    protected $start_date;
    protected $end_date;
    protected $st_id;
    protected $pm_id;
    protected $status_trx;
    protected $status_settle;
    protected $status_cogs;
    protected $sub_payment;

    public function __construct($start_date, $end_date, $st_id, $pm_id, $status_trx, $status_settle = null, $status_cogs = null, $sub_payment = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->st_id = $st_id;
        $this->pm_id = $pm_id;
        $this->status_trx = $status_trx;
        $this->status_settle = $status_settle;
        $this->status_cogs = $status_cogs;
        $this->sub_payment = $sub_payment;
    }

    public function headings(): array
    {
        return [
            'DATE',
            'TIME',
            'STORE',
            'TRX TYPE',
            'RECEIPT NUMBER',
            'ORDER NUMBER',
            'SKU',
            'ARTICLE NAME',
            'BRAND',
            'QTY',
            'GROSS SALES',
            'NAMESET',
            'DISKON PER ITEM',
            'TOTAL DISKON',
            'TOTAL TRANSACTION DISCOUNT',
            'NETSALES BEFORE ADMIN',
            'COGS',
            'SALES VOUCHER',
            'TOTAL ADMIN FEE',
            'NET SALES AFTER ADMIN',
            'PAYMENT METHOD 1',
            'SUB PAYMENT 1',
            'PAYMENT METHOD 2',
            'SUB PAYMENT 2',
            'STATUS TRX',
            'STATUS PAYMENT',
            'NOTE'
        ];
    }

    public function collection()
    {
        if ($this->status_trx === '0' || $this->status_trx === 0) {
            $this->status_trx = '';
        }
        if ($this->status_settle === '0' || $this->status_settle === 0) {
            $this->status_settle = null;
        }
        if ($this->status_cogs === '0' || $this->status_cogs === 0) {
            $this->status_cogs = null;
        }


        if ($this->status_settle === 'Settled') {
            $status_settle = true;
        } elseif ($this->status_settle === 'Unsettled') {
            $status_settle = false;
        } else {
            $status_settle = null;
        }
        if ($this->status_cogs === 'Calculated') {
            $status_cogs = true;
        } elseif ($this->status_cogs === 'Uncalculated') {
            $status_cogs = false;
        } else {
            $status_cogs = null;
        }

        $query = DB::table('pos_transaction_details')
            ->select([
                DB::raw('DATE(ts_pos_transactions.created_at) as date'),
                DB::raw('TIME(ts_pos_transactions.created_at) as time'),
                'stores.st_name as store',
                DB::raw("CASE
                    WHEN ts_stores.st_name not like 'ONLINE%' THEN 'OFFLINE STORE'
                    WHEN ts_stores.st_name like 'ONLINE%' and pos_invoice not like 'INV%'
                        THEN UPPER(ts_online_transactions.platform_name)
                    ELSE UPPER(dv_name)
                    END as trx_type"),
                'pos_invoice as receipt_number',
                'pos_transactions.pos_order_number as order_number',
                'product_stocks.ps_barcode as sku',
                'p_name as article_name',
                'br_name as brand',
                'pos_td_qty as qty',
                'pos_td_item_price_tag as gross_sales',
                'pos_td_nameset_price as nameset',
                DB::raw('CASE 
                        WHEN (pos_td_discount_number + pos_td_sell_price) > pos_td_sell_price 
                        THEN ((pos_td_discount_number + pos_td_sell_price) - pos_td_sell_price) / pos_td_qty
                        WHEN pos_td_item_price_tag * pos_td_qty > pos_td_sell_price 
                        THEN (pos_td_item_price_tag - pos_td_sell_price) / pos_td_qty
                        ELSE 0 
                    END as diskon_per_item'),
                DB::raw('CASE
                WHEN ts_pos_transactions.pos_total_discount = SUM(CASE 
                        WHEN (pos_td_discount_number + pos_td_sell_price) > pos_td_sell_price 
                        THEN ((pos_td_discount_number + pos_td_sell_price) - pos_td_sell_price) / pos_td_qty
                        WHEN pos_td_item_price_tag * pos_td_qty > pos_td_sell_price 
                        THEN (pos_td_item_price_tag - pos_td_sell_price) / pos_td_qty
                        ELSE 0 
                    END) OVER (PARTITION BY ts_pos_transactions.id) THEN COALESCE(CASE 
                        WHEN (pos_td_discount_number + pos_td_sell_price) > pos_td_sell_price 
                        THEN ((pos_td_discount_number + pos_td_sell_price) - pos_td_sell_price) / pos_td_qty
                        WHEN pos_td_item_price_tag * pos_td_qty > pos_td_sell_price 
                        THEN (pos_td_item_price_tag - pos_td_sell_price) / pos_td_qty
                        ELSE 0 
                    END, 0)
                WHEN ts_pos_transactions.pos_total_discount = 0 THEN pos_td_discount_number
                ELSE
                    ROUND(
                        COALESCE(pos_td_discount_number, 0)
                            + (pos_td_sell_price / NULLIF(SUM(pos_td_sell_price) OVER (PARTITION BY ts_pos_transactions.id), 0) 
                               * COALESCE(ts_pos_transactions.pos_total_discount, 0))
                    , 2) END AS total_diskon'),
                'online_transaction_details.original_price as netsales_before_admin',
                DB::raw('(ts_pos_transaction_details.pos_td_qty * ts_pos_transaction_details.pos_td_item_cogs) as cogs'),
                'discount_seller as sales_voucher',
                DB::raw('null as total_admin_fee'),
                DB::raw("CASE
                            WHEN ts_pos_transactions.pos_status not in('DONE', 'DP', 'NAMESET') THEN pos_td_sell_price
                                WHEN ts_pos_transactions.pos_total_discount = SUM(CASE 
                                        WHEN (pos_td_discount_number + pos_td_sell_price) > pos_td_sell_price 
                                        THEN ((pos_td_discount_number + pos_td_sell_price) - pos_td_sell_price) / pos_td_qty
                                        WHEN pos_td_item_price_tag * pos_td_qty > pos_td_sell_price 
                                        THEN (pos_td_item_price_tag - pos_td_sell_price) / pos_td_qty
                                        ELSE 0
                                    END) OVER (PARTITION BY ts_pos_transactions.id) THEN pos_td_item_price_tag*pos_td_qty - COALESCE(CASE 
                                        WHEN (pos_td_discount_number + pos_td_sell_price) > pos_td_sell_price 
                                        THEN ((pos_td_discount_number + pos_td_sell_price) - pos_td_sell_price) / pos_td_qty
                                        WHEN pos_td_item_price_tag * pos_td_qty > pos_td_sell_price 
                                        THEN (pos_td_item_price_tag - pos_td_sell_price) / pos_td_qty
                                        ELSE 0 
                                    END, 0)* pos_td_qty
                                WHEN ts_pos_transactions.pos_total_discount = 0 THEN pos_td_item_price_tag*pos_td_qty - pos_td_discount_number
                                ELSE
                                    pos_td_total_price - ROUND(
                                        COALESCE(pos_td_discount_number, 0)
                                            + (pos_td_sell_price / NULLIF(SUM(pos_td_sell_price) OVER (PARTITION BY ts_pos_transactions.id), 0) 
                                               * COALESCE(ts_pos_transactions.pos_total_discount, 0))
                                    , 2) END AS net_sales_after_admin"),
                DB::raw("CASE
                    WHEN ts_stores.st_name like 'ONLINE%' and pos_invoice not like 'INV%'
                        THEN CONCAT('DEPOSIT ', UPPER(ts_online_transactions.platform_name))
                    ELSE ts_pm_main.pm_name
                    END as payment_method_1"),
                DB::raw("CASE
                    WHEN ts_pos_transactions.sub_payment = 1 THEN 'CASH'
                    WHEN ts_pos_transactions.sub_payment = 2 THEN 'COD'
                    WHEN ts_pos_transactions.sub_payment = 3 THEN 'ON US'
                    WHEN ts_pos_transactions.sub_payment = 4 THEN 'OFF US'
                    END as subpayment_1"),
                'pm_partial.pm_name as payment_method_2',
                DB::raw('null as subpayment_2'),
                'pos_status as status_trx',
                DB::raw("CASE
                    WHEN pos_status = 'DP' THEN 'PARTIAL PAID'
                    ELSE 'PAID' END as status_payment"),
                'pos_note as note',
                'pos_total_discount as total_transaction_discount',
                'is_settle as settlement_status'
            ])
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_details.pt_id')
            ->leftJoin('stores', 'stores.id', '=', 'st_id')
            ->leftJoin('online_transactions', 'online_transactions.order_number', '=', 'pos_transactions.pos_order_number')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pos_transaction_details.pst_id')
            ->leftJoin('online_transaction_details', function ($join) {
                $join->on('online_transactions.id', '=', 'to_id')
                    ->on('product_stocks.ps_barcode', '=', 'online_transaction_details.sku');
            })
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
            ->leftJoin('products', 'products.id', '=', 'p_id')
            ->leftJoin('brands', 'brands.id', '=', 'br_id')
            ->leftJoin('payment_methods as pm_main', 'pm_main.id', '=', 'pm_id')
            ->leftJoin('payment_methods as pm_partial', 'pm_partial.id', '=', 'pm_id_partial')
            ->whereBetween(DB::raw('DATE(ts_pos_transactions.created_at)'), [$this->start_date, $this->end_date])
            ->where('pos_transactions.pos_status', '!=', 'REJECTED');

        // Apply filters if provided
        if ($this->st_id) {
            $query->where('pos_transactions.st_id', $this->st_id);
        }

        if ($this->pm_id) {
            $query->where(function ($q) {
                $q->when($this->pm_id == 'DEPOSIT SHOPEE', function ($query) {
                    return $query->where('online_transactions.platform_name', 'shopee');
                })
                    ->when($this->pm_id == 'DEPOSIT TIKTOK', function ($query) {
                        return $query->where('online_transactions.platform_name', 'tiktok');
                    })
                    ->orWhere('pm_main.pm_name', $this->pm_id)
                    ->orWhere('pm_partial.pm_name', $this->pm_id);
            });
        }

        if ($this->status_trx) {
            $query->where('pos_transactions.pos_status', $this->status_trx);
        }

        if (!is_null($status_settle)) {
            $query->where('pos_transactions.is_settle', $status_settle);
        }

        $query->when(!is_null($status_cogs), function ($query) use ($status_cogs) {
            if ($status_cogs) {
                return $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('pos_transaction_details')
                        ->whereRaw('ts_pos_transaction_details.pt_id = ts_pos_transactions.id')
                        ->where(function ($subQuery) {
                            $subQuery->where('pos_transaction_details.pos_td_item_cogs', '>', 0)
                                ->orWhereNull('pos_transaction_details.pos_td_item_cogs');
                        });
                });
            } else {
                return $query->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('pos_transaction_details')
                        ->whereRaw('ts_pos_transaction_details.pt_id = ts_pos_transactions.id')
                        ->where(function ($subQuery) {
                            $subQuery->where('pos_transaction_details.pos_td_item_cogs', '>', 0)
                                ->orWhereNull('pos_transaction_details.pos_td_item_cogs');
                        });
                });
            }
        });

        $query->when(($this->sub_payment != 0), function ($query) {
            return $query->where(function ($q) {
            $q->where('pos_transactions.sub_payment', $this->sub_payment)
              ->orWhere('pos_transactions.sub_payment_partial', $this->sub_payment);
            });
        });

        $data = $query->get();

        $export_data = [];
        foreach ($data as $row) {
            $export_data[] = [
                $row->date,
                $row->time,
                $row->store,
                $row->trx_type,
                $row->receipt_number,
                $row->order_number,
                $row->sku,
                $row->article_name,
                $row->brand,
                $row->qty,
                $row->gross_sales,
                $row->nameset,
                $row->diskon_per_item,
                $row->total_diskon,
                $row->total_transaction_discount,
                $row->netsales_before_admin,
                $row->cogs,
                $row->sales_voucher,
                $row->total_admin_fee,
                $row->net_sales_after_admin,
                $row->payment_method_1,
                $row->subpayment_1,
                $row->payment_method_2,
                $row->subpayment_2,
                $row->status_trx,
                $row->status_payment,
                $row->settlement_status ? 'SETTLED' : 'UNSETTLED',
                $row->note
            ];
        }
        return collect($export_data);
    }
}
