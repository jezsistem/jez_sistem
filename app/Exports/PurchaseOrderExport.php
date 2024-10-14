<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseOrderExport implements FromCollection, WithHeadings
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
        return DB::table('purchase_order_article_details')
            ->join('purchase_order_articles', 'purchase_order_article_details.poa_id', '=', 'purchase_order_articles.id')
            ->join('purchase_orders', 'purchase_order_articles.po_id', '=', 'purchase_orders.id')
            ->join('purchase_order_article_detail_statuses', 'purchase_order_article_details.id', '=', 'purchase_order_article_detail_statuses.poad_id')
            ->join('product_stocks', 'purchase_order_article_details.pst_id', '=', 'product_stocks.id')
            ->join('products AS products', 'purchase_order_articles.p_id', '=', 'products.id')
            ->join('product_sub_categories', 'products.psc_id', '=', 'product_sub_categories.id')
            ->join('sizes', 'product_stocks.sz_id', '=', 'sizes.id')
            ->where('purchase_orders.id', $this->po_id)
            ->select(
                DB::raw("CONCAT(ts_products.article_id, ' ', ts_products.p_name, ' ', ts_products.p_color) as `Rumus`"),
                'product_sub_categories.psc_name as Category',
                'sizes.sz_name',
                'product_stocks.ps_price_tag',
                'product_stocks.ps_barcode',
                'purchase_order_article_details.poads_qty',
                DB::raw("'Yes' as YesColumn"),
                DB::raw("'No' as NoColumn"),
                DB::raw("0 as ZeroColumn"),
                DB::raw("'Yes' as AnotherYesColumn")
            )
            ->get();
    }

    /**
     * Return headings for the columns
     */
    public function headings(): array
    {
        return [
            'Rumus',
            'Category',
            'Size',
            'Price Tag',
            'Barcode',
            'Quantity',
            'Yes Column',
            'No Column',
            'Zero Column',
            'Another Yes Column',
        ];
    }
}