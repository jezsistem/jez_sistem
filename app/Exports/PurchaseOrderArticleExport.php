<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderArticle;
use App\Models\PurchaseOrderArticleDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseOrderArticleExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $po_id;

    function __construct($po_id)
    {
        $this->po_id = $po_id;
    }

    public function collection()
    {
        $check = PurchaseOrder::where('id', $this->po_id)->exists();

        if ($check) {
            $poa_data = PurchaseOrderArticle::select(
                'purchase_order_articles.id as poa_id',
                'po_id',
                'products.id as pid',
                'br_name',
                'p_price_tag',
                'p_purchase_price',
                'p_name',
                'p_color',
                'poa_discount',
                'poa_extra_discount',
                'poa_reminder'
            )
                ->leftJoin('products', 'products.id', '=', 'purchase_order_articles.p_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                ->where(['po_id' => $this->po_id])->get();

            if (!empty($poa_data)) {
                $get_product = [];
                foreach ($poa_data as $poa) {
                    $poad_data = PurchaseOrderArticleDetail::select(
                        'purchase_order_article_details.id as poad_id',
                        'sz_name',
                        'ps_qty',
                        'ps_running_code',
                        'ps_sell_price',
                        'ps_price_tag',
                        'ps_purchase_price',
                        'poad_qty',
                        'poad_purchase_price',
                        'poad_total_price'
                    )
                        ->leftJoin('product_stocks', 'product_stocks.id', '=', 'purchase_order_article_details.pst_id')
                        ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                        ->where(['poa_id' => $poa->poa_id])->get();
                    if (!empty($poad_data)) {
                        $poa->subitem = $poad_data->all(); // Convert the subitem to an array
                        array_push($get_product, $poa);
                    } else {
                        $get_product = null;
                    }
                }
            } else {
                $get_product = null;
            }
        }

        return collect($get_product);
    }

    public function headings(): array
    {
        return [
            'POA ID',
            'PO ID',
            'Product ID',
            'Brand Name',
            'Price Tag',
            'Purchase Price',
            'Product Name',
            'Product Color',
            'POA Discount',
            'POA Extra Discount',
            'POA Reminder',
        ];
    }
}
