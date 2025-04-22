<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockTrackingExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DB::table('product_location_setup_transactions')
            ->select(
                'pos_invoice',
                'ps_barcode',

                'br_name',
                'p_name',
                'p_color',
                'sz_name',
                'pl_code',
                'pl_name',
                'pl_description',
                'plst_status',
                'plst_qty',
                'product_location_setup_transactions.created_at as plst_created',
                'product_location_setup_transactions.move_store_time as move_created',
                'product_location_setup_transactions.in_stock_time as instock_time',
                DB::raw('TIMESTAMPDIFF(MINUTE, ts_product_location_setup_transactions.created_at, ts_product_location_setup_transactions.move_store_time) as lead_time_minutes'),
                DB::raw('TIMESTAMPDIFF(MINUTE, ts_product_location_setup_transactions.move_store_time, ts_product_location_setup_transactions.updated_at) as lead_time_instock')
            )
            ->leftJoin('pos_transactions', 'pos_transactions.id', '=', 'product_location_setup_transactions.pt_id')
            ->leftJoin('product_location_setups', 'product_location_setups.id', '=', 'product_location_setup_transactions.pls_id')
            ->leftJoin('product_stocks', 'product_stocks.id', '=', 'product_location_setups.pst_id')
            ->leftJoin('products', 'products.id', '=', 'product_stocks.p_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('customers', 'customers.id', '=', 'pos_transactions.cust_id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->leftJoin('users', 'users.id', '=', 'product_location_setup_transactions.u_id')
            ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
            ->where('product_location_setup_transactions.st_id' , $this->filters['st_id']);

        // Apply filters
        if (!empty($this->filters['br_id'])) {
            $query->where('products.br_id', $this->filters['br_id']);
        }

        if (!empty($this->filters['psc_id'])) {
            $query->where('products.psc_id', $this->filters['psc_id']);
        }

        if (!empty($this->filters['std_id'])) {
            $query->where('pos_transactions.std_id', $this->filters['std_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('plst_status', $this->filters['status']);
        }

        // You can also add date range filters here if needed

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Invoice', 'Barcode',
            'Brand', 'Product Name', 'Color',
            'Size', 'Location Code', 'Location Name', 'Location Desc',
            'Status', 'Qty', 'Created / Req Time', 'Move Time', 'Instock Time', 'Lead time Move Store', 'Lead Time Instock'
        ];
    }
}
