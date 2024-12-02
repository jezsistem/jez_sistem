<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class OnlineReportExport implements FromCollection, WithHeadings
{
    protected $branch;
    protected $status;
    protected $start;
    protected $end;
    protected $changeplatform;

    function __construct($branch, $start, $end, $status, $changeplatform)
    {
        $this->start = $start;
        $this->end = $end;
        $this->status = $status;
        $this->branch = $branch;
        $this->changeplatform = $changeplatform;
    }

    public function headings(): array
    {
        return [
            "Store", "Nomor Pesanan", "Resi", "Platform", "Sku", 
            "Item Name", "Qty", "Order Date", "Time Transaksi", 
            "Status Transaksi", "Diskon Penjual", "Diskon Platform", 
            "Total Diskon", "Original Price", "Final Price", "Date Import"
        ];
    }

    public function collection()
    {
        $query = DB::table('online_transaction_details')
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
            );

        // Apply Date Range Filter
        if ($this->start && $this->end) {
            $query->whereBetween('online_transactions.time_print', [$this->start, $this->end]);
        }

        // Apply Platform Filter
        if (!empty($this->changeplatform)) {
            $query->where('online_transactions.platform_name', $this->changeplatform);
        }

        // Apply Branch Filter
        if (!empty($this->branch)) {
            $query->where('online_transactions.st_id', $this->branch);
        }

        // Apply Status Filter
        if ($this->status !== null && $this->status != '2') {
            $query->where('online_transactions.online_print', $this->status);
        }

        // Debugging log
        \Log::info("Generated Query: " . $query->toSql());
        \Log::info("Query Bindings: ", $query->getBindings());

        return $query->get();
    }
}
