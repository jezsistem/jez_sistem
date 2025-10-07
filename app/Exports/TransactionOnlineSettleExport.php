<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionOnlineSettleExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Order Number', 'Platform', 'Store', 'Final Price', 'Total Fee', 'Voucher Discount', 'Affiliate Cut',
            'Commission Fee', 'Service Fee', 'Xtra Voucher Fee', 'Cashback Fee', 'Cashout Date',
            'Total Settlement', 'Jezpro Date', 'Fee %', 'Seller Voucher %', 'Net Sales Jezpro','Diff Jezpro & MP', 'Status TRX', 'Status Refund'
        ];
    }

    public function map($row): array
    {
        return [
            $row->order_number,
            $row->platform_name,
            $row->st_name,
            $row->revenue,
            $row->total_fee,
            $row->seller_discount,
            $row->affiliate_cut,
            $row->marketplace_commision_fee,
            $row->service_fee,
            $row->voucher_xtra_service_fee,
            $row->cashback_service_fee,
            $row->settle_date,
            $row->total_settle,
            $row->trx_date,
            $row->fee_persentage,
            $row->seller_voucher_persentage,
            $row->jezpro_price,
            $row->diff_jezpro_mp,
            $row->status,
            $row->status_refund,
        ];
    }
}
