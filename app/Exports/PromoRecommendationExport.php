<?php

namespace App\Exports;

use App\Models\ArtikelPromo;
use App\Models\PromoRecommendation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PromoRecommendationExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return PromoRecommendation::select(
            'article_id',
            'p_name',
            'channel',
            'discount',
            'notes'
        )
        ->join('products', 'products.id', '=', 'p_id')
        ->get();
    }

    public function headings(): array
    {
        return [
            'Article ID',
            'Product Name',
            'Channel',
            'Discount',
            'Notes'
        ];
    }
}
