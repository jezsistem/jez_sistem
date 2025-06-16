<?php

namespace App\Exports;

use App\Models\ArtikelPromo;
use App\Models\PromoRecommendation;
use App\Models\PromoRecommendationDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PromoRecommendationDetailExport implements FromCollection, WithHeadings
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        return PromoRecommendationDetail::select(
            'article_id',
            'channel',
            'discount',
            'notes'
        )
        ->join('promo_recommendations', 'pr_id', '=', 'promo_recommendations.id')
        ->join('products', 'products.id', '=', 'p_id')
        ->where('promo_recommendations.id', $this->id)
        ->get();
    }

    public function headings(): array
    {
        return [
            'Article ID',
            'Channel',
            'Discount',
            'Notes'
        ];
    }
}
