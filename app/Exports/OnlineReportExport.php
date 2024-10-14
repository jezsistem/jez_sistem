<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class OnlineReportExport implements FromCollection, withHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $branch;
    protected $status;
    protected $start;
    protected $end;

    function __construct($branch, $start, $end, $status)
    {
        $this->start = $start;
        $this->end = $end;
        $this->status = $status;
        $this->branch = $branch;
    }

    public function headings(): array
    {
        return ["Store", "Store", "Resi", "Platform", "Sku", "Item Name", "Qty", "Order Date", "Time Transaksi", "Status Transaksi", "Diskon Penjual", "Diskon Platform", "Total Diskon", "Original Price", "Final Price", "Date Import"];
    }

    public function collection()
    {
        $data = DB::table('online_transaction_details')
            ->join('online_transactions', 'online_transaction_details.to_id', '=', 'online_transactions.id')
            ->join('stores', 'online_transactions.st_id', '=', 'stores.id')
            ->join('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
            ->join('products', 'product_stocks.p_id', '=', 'products.id')
            ->select(
                'stores.st_name',
                'online_transaction_details.order_number',
                'online_transactions.no_resi',
                'online_transactions.platform_name',
                'online_transaction_details.sku',
                'products.p_name',
                'online_transaction_details.qty',
                'online_transactions.order_date_created',
                'online_transactions.time_print',
                'online_transactions.online_print',
                'online_transaction_details.discount_seller',
                'online_transaction_details.discount_platform',
                'online_transaction_details.total_discount',
                'online_transaction_details.original_price',
                'online_transaction_details.price_after_discount',
                'online_transaction_details.created_at'
            )
            ->where(function($w) {
                if (!empty($this->end)) {
                    $w->whereBetween('online_transactions.time_print', [$this->start . ' 00:00:01', $this->end . ' 23:59:59']);
                } else {
                    $w->whereBetween('online_transactions.time_print', [$this->start . ' 00:00:01', $this->start . ' 23:59:59']);
                }
            })
            ->where('online_transactions.online_print', '=', 1)
            ->where('online_transactions.st_id', '=', $this->branch) // Filter by branch
            ->get();

        return $data;
    }
}
