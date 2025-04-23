<?php

namespace App\Exports;

use App\Models\ArtikelPromo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArtikelPromoExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return ArtikelPromo::select(
            'article_id',
            'p_name',
            'stores.st_code as st_code',
            'promo_name',
            'date_start',
            'date_end',
            'promo_disc',
            'promo_note'
        )
        ->join('stores', 'stores.id', '=', 'articles_promo.st_id')
        ->join('products', 'products.id', '=', 'articles_promo.p_id')
        ->get();
    }

    public function headings(): array
    {
        return [
            'Article ID',
            'Product Name',
            'Store Code',
            'Promo Name',
            'Start Date',
            'End Date',
            'Discount',
            'Notes'
        ];
    }
}
