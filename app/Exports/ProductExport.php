<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection , withHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return ["NAMA_PRODUK", "QTY", "KATEGORI", "SUB_KATEGORI", "SUB_SUB_KATEGORI", "BRAND", "SUPPLIER", "UNIT", "GENDER", "SEASON", "AGING", "WARNA", "WARNA_ARTIKEL", "HARGA_BANDEROL", "HARGA_BELI", "HARGA_JUAL", "SIZE", "BARCODE"];
    }
    
    public function collection()
    {
//        return collect(Product::getExport());
        $data = Product::getExport();

        return $data->chunk(100)->flatMap(function ($chunk) {
            // Transform each chunk and return a flat collection
            return $chunk->map(function ($item) {
                return [
                    $item->p_name, $item->ps_qty,$item->pc_name, $item->psc_name, $item->pssc_name,
                    $item->br_name, $item->ps_name, $item->pu_name, $item->gn_name,
                    $item->ss_name, $item->p_aging, $item->mc_name, $item->p_color,
                    $item->p_price_tag, $item->p_purchase_price, $item->p_sell_price,
                    $item->sz_name, $item->ps_barcode
                ];
            });
        });
    }
}
