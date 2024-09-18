<?php

namespace App\Exports;

use App\Models\StockTransferDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;

class StockTransferExport implements FromCollection
{
    protected $po_id;

    public function __construct($po_id)
    {
        $this->po_id = $po_id;
    }
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        return DB::table('stock_transfer_details as stock_transfer_details')
            ->leftJoin('product_stocks as product_stocks', 'stock_transfer_details.pst_id', '=', 'product_stocks.id')
            ->leftJoin('stock_transfers as stock_transfers', 'stock_transfer_details.stf_id', '=', 'stock_transfers.id')
            ->leftJoin('stock_transfer_detail_statuses', 'stock_transfer_detail_statuses.stfd_id', '=', 'stock_transfer_details.id')
            ->select('product_stocks.ps_barcode', 'stock_transfer_details.stfd_qty',  DB::raw('SUM(ts_stock_transfer_detail_statuses.stfds_qty) as qty_transfer'))
            ->where('stock_transfers.stf_code', $this->po_id)
            ->groupBy('product_stocks.ps_barcode', 'stock_transfer_details.stfd_qty')
            ->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Qty',
            'Qty transfer',
        ];
    }
}
