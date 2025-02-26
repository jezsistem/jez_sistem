<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class StdExportDraft implements FromCollection , withHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $start;
    protected $end;

    function __construct($stf_code)
    {
        $this->stf_code = $stf_code;
    }

    public function headings(): array
    {
        return ["Tf Code", "Store Asal", "Store Tujuan", "SKU", "Item Name", "Color", "Variant", "Qty Transfer", "Bin Asal" ];
    }

    public function collection()
    {
        $export = array();
        $data = DB::table('stock_transfer_details')
            ->select([
                'stock_transfer_details.id as stfd_id',
                'store_start.st_name as st_name_start',
                'store_end.st_name as st_name_end',
                'stf_code',
                'br_name',
                'p_name',
                'p_color',
                'sz_name',
                'stfd_qty',
                'pl_code',
                'product_stocks.ps_barcode as ps_barcode'
            ])
            ->leftJoin('stock_transfers', 'stock_transfers.id', '=', 'stock_transfer_details.stf_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'stock_transfer_details.pst_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'stock_transfer_details.pl_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('stores as store_start', 'stock_transfers.st_id_start', '=', 'store_start.id')
            ->leftJoin('stores as store_end', 'stock_transfers.st_id_end', '=', 'store_end.id')
            ->where('stf_code', '=', $this->stf_code)
            ->get();
        if (!empty($data)) {
            foreach ($data as $row) {

                $export[] = [$row->stf_code, $row->st_name_start, $row->st_name_end, $row->ps_barcode, $row->p_name, $row->p_color, $row->sz_name, $row->stfd_qty, $row->pl_code];
            }
        }
        return collect($export);
    }
}
