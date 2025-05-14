<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionOnlineSettleExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $request = new Request($this->filters);

        // Paste dan modifikasi logika query yang sama dari getDatatables()
        $query = app('App\Http\Controllers\CekDanaOnlineController')->getQueryForExport($request);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Order Number', 'Platform', 'Store', 'Final Price', 'Total Cut', 'Voucher Discount', 'Affiliate Cut',
            'Commission Fee', 'Service Fee', 'Xtra Voucher Fee', 'Cashback Fee', 'Cashout Date', 'Created At',
            'Disbursed', 'Jezpro Date', 'Online Print', 'Fee %', 'Seller Voucher %', 'Diff Jezpro & MP'
        ];
    }

    public function map($row): array
    {
        return [
            $row->order_number,
            $row->platform_name,
            $row->st_name,
            $row->final_price,
            $row->total_online_cut,
            $row->seller_voucher_discount,
            $row->affiliate_cut,
            $row->marketplace_commision_fee,
            $row->service_fee,
            $row->voucher_xtra_service_fee,
            $row->cashback_service_fee,
            $row->cashout_date,
            $row->created_at,
            $row->total_disburshed_amount,
            $row->jezpro_transaction_date,
            $row->online_print,
            $row->final_price && $row->total_online_cut ? number_format(($row->total_online_cut / $row->final_price * 100), 2) . '%' : '0.00%',
            $row->final_price && $row->seller_voucher_discount ? number_format(($row->seller_voucher_discount / $row->final_price * 100), 2) . '%' : '0.00%',
            $row->pos_real_price - $row->final_price,
        ];
    }
}
