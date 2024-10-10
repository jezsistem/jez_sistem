<?php

namespace App\Exports;

use App\Models\ProductLocationSetup;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderArticle;
use App\Models\PurchaseOrderArticleDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseOrderArticleExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $po_id;
    protected $st_id;

    function __construct($po_id, $st_id)
    {
        $this->po_id = $po_id;
        $this->st_id = $st_id;
    }

    public function headings(): array
    {
        return [
            'Artikel ID',
            'Item Name',
            'Warna Artikel',
            'Brand',
            'Size',
            'SKU',
            'Order',
            'InStock',
            'Harga Bandrol',
            'Harga beli',
            'Total',
            'Variant Schema',
            'Status'
        ];
    }

    public function collection()
    {
        $check = PurchaseOrder::where('id', $this->po_id)->first();

        if ($check) {
            // get Purchase Order Article
            $poa_data = PurchaseOrderArticle::select(
                'products.article_id as article_id',
                'products.p_name as p_name',
                'products.p_color as p_color',
                'brands.br_name as br_name',
                'sizes.sz_name as sz_name',
                'product_stocks.ps_barcode as ps_barcode',
                'poad_qty',
                'product_stocks.ps_price_tag as ps_price_tag',
                'purchase_order_article_details.poad_purchase_price as poad_purchase_price',
                'purchase_order_article_details.poad_total_price as poad_total_price',
                'sizes.sz_schema as sz_schema',
                'purchase_order_article_details.pst_id as pst_id',
                'product_location_setups.pls_qty as pls_qty'
            )
                ->leftJoin('products', 'products.id', '=', 'purchase_order_articles.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->leftJoin('purchase_order_article_details', 'purchase_order_article_details.poa_id', '=', 'purchase_order_articles.id')
                ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                ->where(['po_id' => $this->po_id])
                ->groupBy('product_stocks.id')
                ->get();


            $export_data = [];
            foreach ($poa_data as $poa) {
                $plsQtyData = ProductLocationSetup::select(
                    DB::raw('SUM(pls_qty) as total_pls_qty'))
                    ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->where(['pst_id' => $poa->pst_id, 'product_locations.st_id' => $this->st_id])
                    ->groupBy('product_location_setups.pst_id')
                    ->first();

                    $instock = 0;
                    if ($plsQtyData != null) {
                        $instock = $plsQtyData->total_pls_qty;
                    }

                    $export_data[] = [
                        'article_id' => $poa->article_id,
                        'p_name' => $poa->p_name,
                        'p_color' => $poa->p_color,
                        'br_name' => $poa->br_name,
                        'sz_name' => $poa->sz_name,
                        'ps_barcode' => $poa->ps_barcode,
                        'poad_qty' => $poa->poad_qty,
                        'in_stock' => $instock,
                        'ps_price_tag' => $poa->ps_price_tag,
                        'poad_purchase_price' => $poa->poad_purchase_price,
                        'poad_total_price' => $poa->poad_total_price,
                        'sz_schema' => $poa->sz_schema,
                    ];

            }

            return collect($export_data);
        }
        return collect([]);
    }

}
