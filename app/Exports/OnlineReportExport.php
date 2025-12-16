<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class OnlineReportExport implements FromCollection, WithHeadings
{
    protected $store_id;
    protected $start_date;
    protected $end_date;

    protected $status_print;
    // status_print: 4 = semua, 3 = sudah cetak nota, 2 = sudah cetak resi, 1 = sudah cetak nota & resi, 0 = belum cetak

    protected $platform;
    protected $courier;
    protected $order_status;
    protected $internal_order_status;

    function __construct($store_id, $start_date, $end_date, $status_print, $platform = null, $courier = null, $order_status = null, $internal_order_status = null)
    {
        $this->store_id = $store_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->status_print = $status_print;
        $this->platform = $platform;
        $this->courier = $courier;
        $this->order_status = $order_status;
        $this->internal_order_status = $internal_order_status;
    }

    public function headings(): array
    {
        return [
            "Order Number",
            "No Resi",
            "Store Name",
            "MP TRX Date",
            "Plaform Name",
            "Courier",
            "Shipping Fee",
            "Shipping Method",
            "Total Payment",
            "Status Jezpro",
            "Status Print Nota",
            "Status Print Resi",
            "Status Marketplace",
            "SKU MP",
            "SKU Jezpro",
            "Article Details",
            "QTY",
            "MP Price",
            "Jezpro Price",
            "Difference Price",
            "Discount Seller",
            "Net Sales Before Admin",
            "Final Price",
            "Warehouse",
            "Manifest Number"
        ];
    }

    public function collection()
    {
        $query = DB::table('online_transaction_details')
            ->join('online_transactions', 'online_transaction_details.to_id', '=', 'online_transactions.id')
            ->join('stores', 'online_transactions.st_id', '=', 'stores.id')
            ->leftJoin('product_stocks', 'product_stocks.ps_barcode', '=', 'online_transaction_details.sku')
            ->leftJoin('products', 'product_stocks.p_id', '=', 'products.id')
            ->leftJoin('brands', 'products.br_id', '=', 'brands.id')
            ->leftJoin('sizes', 'product_stocks.sz_id', '=', 'sizes.id')
            ->leftJoin('delivery_receipts', function($join) {
                $join->on('online_transactions.no_resi', '=', 'delivery_receipts.resi')
                     ->orOn('online_transactions.order_number', '=', 'delivery_receipts.resi');
            })
            ->leftJoin('delivery_recaps', 'delivery_receipts.dr_id', '=', 'delivery_recaps.id')
            ->select(
                'online_transactions.order_number',
                'online_transactions.no_resi',
                'stores.st_name as store_name',
                'online_transactions.order_date_created as mp_trx_date',
                'online_transactions.platform_name',
                'online_transactions.courier',
                'online_transactions.shipping_fee',
                'online_transactions.shipping_method',
                'online_transactions.total_payment',
                'online_transactions.internal_order_status as status_jezpro',
                DB::raw("(CASE WHEN ts_online_transactions.online_print = 1 THEN 'SUDAH DI CETAK NOTA' ELSE 'BELUM DI CETAK NOTA' END) as status_print_nota"),
                DB::raw("(CASE WHEN ts_online_transactions.print_resi = 1 THEN 'SUDAH DI CETAK RESI' ELSE 'BELUM DI CETAK RESI' END) as status_print_resi"),
                'online_transactions.order_status as mp_status',
                'online_transaction_details.sku as sku_mp',
                DB::raw("COALESCE(ts_product_stocks.ps_barcode, 'SKU JEZPRO HILANG / BERUBAH') as sku_jezpro"),
                DB::raw("COALESCE(CONCAT('[', ts_products.p_name, '] ', ts_brands.br_name, ' ', ts_products.p_color, ' [', ts_sizes.sz_name, ']'), 'SKU JEZPRO HILANG / BERUBAH') as article"),
                'online_transaction_details.qty as order_qty',
                'online_transaction_details.original_price as mp_price',
                'product_stocks.ps_price_tag as jezpro_price',
                DB::raw('ts_product_stocks.ps_price_tag - ts_online_transaction_details.original_price as diff_price'),
                'online_transaction_details.discount_seller',
                DB::raw('ts_online_transaction_details.original_price - ts_online_transaction_details.discount_seller as netsale_before'),
                'online_transaction_details.price_after_discount as final_price',
                'online_transaction_details.warehouse',
                'delivery_recaps.manifest_number'
            )
            ->whereNull('online_transaction_details.deleted_at');

        // Apply Store Filter
        if (!empty($this->store_id)) {
            $query->where('online_transactions.st_id', $this->store_id);
        }
        
        // Apply Date Range Filter
        if ($this->start_date && $this->end_date) {
            $query->whereBetween(DB::raw('DATE(ts_online_transactions.order_date_created)'), [$this->start_date, $this->end_date]);
        }
        
        // Apply Status Print Filter
        // status_print: 4 = semua, 3 = sudah cetak nota, 2 = sudah cetak resi, 1 = sudah cetak nota & resi, 0 = belum cetak
        if ($this->status_print !== null && $this->status_print != 4) {
            if ($this->status_print == 3) {
                $query->where('online_transactions.online_print', 1);
            } elseif ($this->status_print == 2) {
                $query->where('online_transactions.print_resi', 1);
            } elseif ($this->status_print == 1) {
                $query->where('online_transactions.online_print', 1)
                ->where('online_transactions.print_resi', 1);
            } elseif ($this->status_print == 0) {
                $query->where('online_transactions.online_print', 0)
                ->where('online_transactions.print_resi', 0);
            }
        }
        
        // Apply Platform Filter
        if (!empty($this->platform)) {
            $query->where('online_transactions.platform_name', $this->platform);
        }
        
        // Apply Courier Filter
        if (!empty($this->courier)) {
            $query->where('online_transactions.courier', $this->courier);
        }
        
        // Apply Order Status Filter
        if (!empty($this->order_status)) {
            $query->where('online_transactions.order_status', $this->order_status);
        }
        
        // Apply Internal Order Status Filter
        if (!empty($this->internal_order_status)) {
            $query->where('online_transactions.internal_order_status', $this->internal_order_status);
        }

        return $query->orderBy('online_transactions.id', 'desc')->get();
    }
}
