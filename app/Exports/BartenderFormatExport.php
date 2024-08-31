<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BartenderFormatExport implements FromCollection, WithHeadings
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
            ->join('products', 'purchase_order_articles.p_id', '=', 'products.id')
            ->join('product_sub_categories', 'products.psc_id', '=', 'product_sub_categories.id')
            ->join('sizes', 'product_stocks.sz_id', '=', 'sizes.id')
//            ->where('purchase_orders.id', $this->po_id)
            ->where('purchase_orders.po_invoice', $this->po_id)
            ->select(
                DB::raw("CONCAT(ts_products.article_id, ' ', ts_products.p_name, ' ', ts_products.p_color) as `Name`"),
                DB::raw("
                CASE 
                    WHEN LENGTH(CONCAT(ts_products.p_name, ' ', ts_products.p_color)) < 38 
                    THEN CONCAT(ts_products.p_name, ' ', ts_products.p_color)
                    ELSE CONCAT(
                        LEFT(SUBSTRING(CONCAT(ts_products.p_name, ' ', ts_products.p_color), 
                        LOCATE(' ', CONCAT(ts_products.p_name, ' ', ts_products.p_color)) + 1), 30),
                        '..',
                        RIGHT(CONCAT(ts_products.p_name, ' ', ts_products.p_color), 5)
                    )
                END as `Rumus`
            "),
                'psc_name',
                'sz_name',
                'ps_price_tag',
                'ps_barcode',
                'poads_qty',
                DB::raw("'Yes'"),
                DB::raw("'No'"),
                DB::raw("CAST(0 AS UNSIGNED) as `Variant 1 - Stock Alert`"),
                DB::raw("'Yes' as `Variant 1 - Track Cost`")
            )
            ->get();
    }

    /**
     * Return headings for the columns
     */
    public function headings(): array
    {
        return [
            'Name',
            'Name Label',
            'Category',
            'Variant 1 - Name',
            'Variant 1 - Price',
            'Variant 1 - SKU',
            'Variant 1 - In Stock (DO NOT EDIT)',
            'Variant 1 - Track Stock',
            'Variant 1 - Alert',
            'Variant 1 - Stock Alert',
            'Variant 1 - Track Cost',
        ];
    }
}