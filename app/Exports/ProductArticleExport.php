<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductArticleExport implements FromCollection, WithHeadings
{

    public function headings(): array
    {
        return ['NAMA', 'WARNA', 'BRAND', 'SUPPLIER', 'HARGA BANDEROL', 'HARGA BELI', 'HARGA JUAL'];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): \Illuminate\Support\Collection
    {
        $data = Product::getArticleExport();

        return $data->chunk(100)->flatMap(function ($chunk) {
            // Transform each chunk and return a flat collection
            return $chunk->map(function ($item) {
                return [
                    $item->p_name, $item->p_color, $item->br_name, $item->ps_name,
                    $item->p_price_tag, $item->p_purchase_price, $item->p_sell_price
                ];
            });
        });
    }
}
