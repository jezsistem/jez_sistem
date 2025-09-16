<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SettlementTransactionExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $start_date;
    protected $end_date;
    protected $st_id;
    protected $pm_id;
    protected $status_trx;
    protected $status_settle;
    protected $status_cogs;

    public function __construct($start_date, $end_date, $st_id, $pm_id, $status_trx, $status_settle = null, $status_cogs = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->st_id = $st_id;
        $this->pm_id = $pm_id;
        $this->status_trx = $status_trx;
        $this->status_settle = $status_settle;
        $this->status_cogs = $status_cogs;
    }

    public function headings(): array
    {
        return [
            'DATE',
            'TIME',
            'TRX TYPE',
            'RECEIPT NUMBER',
            'ORDER NUMBER',
            'TOTAL ITEMS',
            'GROSS SALES',
            'NAMESET',
            'TOTAL DISKON',
            'NET SALES BEFORE ADMIN',
            'COGS',
            'SALES VOUCHER',
            'TOTAL ADMIN FEE',
            'NET SALES AFTER ADMIN',
            'PAYMENT METHOD 1',
            'SUB PAYMENT 1',
            'TOTAL PAYMENT 1',
            'PAYMENT METHOD 2',
            'SUB PAYMENT 2',
            'TOTAL PAYMENT 2',
            'STATUS TRX',
            'STATUS PAYMENT',
            'SETTLEMENT STATUS',
            'NOTE'
        ];
    }

    public function collection()
    {
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
        // Build the query with conditional filters
        $query = DB::table('pos_transactions')
            ->select([
                DB::raw('DATE(ts_pos_transactions.created_at) as date'),
                DB::raw('TIME(ts_pos_transactions.created_at) as time'),
                'stores.st_name as store',
                DB::raw("CASE
                WHEN ts_stores.st_name not like 'ONLINE%' THEN 'OFFLINE STORE'
                WHEN ts_stores.st_name like 'ONLINE%' and pos_invoice not like 'INV%' THEN UPPER(ts_online_transactions.platform_name)
                ELSE UPPER(dv_name)
                END as trx_type"),
                'pos_invoice as receipt_number',
                'pos_transactions.pos_order_number as order_number',
                DB::raw('SUM(pos_td_qty) as total_items'),
                DB::raw('SUM(pos_td_qty * pos_td_item_price_tag) as gross_sales'),
                DB::raw('SUM(pos_td_nameset_price) as nameset'),
                'pos_total_discount as total_discount',
                DB::raw('MAX(final_price) as net_sales_before_admin'),
                DB::raw('SUM(pos_td_item_cogs) as total_cogs'),
                DB::raw('SUM(seller_voucher_discount) as seller_voucher'),
                DB::raw('MAX(total_online_cut) as total_fee_admin'),
                'pos_real_price as net_sales_after_admin',
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
                DB::raw("CASE
                WHEN pos_payment is null THEN pos_real_price
                WHEN ts_pm_main.pm_name = 'CASH' and (ts_pos_transactions.pm_id_partial is null or ts_pos_transactions.pm_id is null) THEN pos_real_price
                ELSE pos_payment END as total_payment_1"),
                'pm_partial.pm_name as payment_method_2',
                DB::raw('null as subpayment_2'),
                'pos_payment_partial as total_payment_2',
                'pos_status as status_trx',
                DB::raw("CASE
                WHEN pos_status = 'DP' THEN 'PARTIAL PAID'
                ELSE 'PAID' END as status_payment"),
                'pos_note as note',
                'is_settle as settlement_status'
            ])
            ->join('stores', 'stores.id', '=', 'st_id')
            ->leftJoin('payment_methods as pm_main', 'pm_main.id', '=', 'pos_transactions.pm_id')
            ->leftJoin('payment_methods as pm_partial', 'pm_partial.id', '=', 'pos_transactions.pm_id_partial')
            ->leftJoin('pos_transaction_details', 'pos_transactions.id', '=', 'pt_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'pst_id')
            ->leftJoin('online_transactions', 'online_transactions.order_number', '=', 'pos_transactions.pos_order_number')
            ->leftJoin('store_type_divisions', 'store_type_divisions.id', '=', 'pos_transactions.std_id')
            ->leftJoin('online_funds', 'online_funds.order_number', '=', 'pos_transactions.pos_order_number')
            ->whereBetween(DB::raw('DATE(ts_pos_transactions.created_at)'), [$this->start_date, $this->end_date]);

        // Apply filters if provided
        if ($this->st_id) {
            $query->where('pos_transactions.st_id', $this->st_id);
        }

        if ($this->pm_id) {
            $query->where(function ($q) {
                $q->where('pm_main.pm_name', $this->pm_id)
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

        $data = $query->groupBy('pos_transactions.id')
            ->orderByDesc(DB::raw('COUNT(pos_invoice)'))
            ->get();

        $export_data = [];
        foreach ($data as $row) {
            $export_data[] = [
                $row->date,
                $row->time,
                $row->trx_type,
                $row->receipt_number,
                $row->order_number,
                $row->total_items,
                $row->gross_sales,
                $row->nameset,
                $row->total_discount,
                $row->net_sales_before_admin,
                $row->total_cogs,
                $row->seller_voucher,
                $row->total_fee_admin,
                $row->net_sales_after_admin,
                $row->payment_method_1,
                $row->subpayment_1,
                $row->total_payment_1,
                $row->payment_method_2,
                $row->subpayment_2,
                $row->total_payment_2,
                $row->status_trx,
                $row->status_payment,
                $row->settlement_status ? 'SETTLED' : 'UNSETTLED',
                $row->note
            ];
        }
        return collect($export_data);
    }
}
