<?php

namespace App\Exports;

use App\Models\ArtikelPromo;
use App\Models\PromoRecommendation;
use App\Models\PromoRecommendationDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PromoRecommendationExport implements FromCollection, WithHeadings
{
    protected $search;
    protected $channel;
    protected $date_start;
    protected $date_end;

    public function __construct($search = null, $channel = null, $date_start = null, $date_end = null)
    {
        $this->search = $search;
        $this->channel = $channel;
        $this->date_start = $date_start;
        $this->date_end = $date_end;
    }

    public function collection()
    {
        $query = PromoRecommendationDetail::select(
            'pr_code',
            'article_id',
            'channel',
            'discount',
            'notes'
        )
            ->join('promo_recommendations', 'pr_id', '=', 'promo_recommendations.id')
            ->join('products', 'products.id', '=', 'p_id');

        if ($this->search) {
            $query->where('pr_code', 'like', '%' . $this->search . '%');
        }

        if ($this->channel) {
            $query->where('channel', $this->channel);
        }

        if ($this->date_start && $this->date_end) {
            $query->whereBetween(
                DB::raw('DATE(created_at)'),
                [$this->date_start, $this->date_end]
            );
        } elseif ($this->date_start) {
            $query->whereDate('promo_recommendations.created_at', '>=', $this->date_start);
        } elseif ($this->date_end) {
            $query->whereDate('promo_recommendations.created_at', '<=', $this->date_end);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Promo Recom Code',
            'Article ID',
            'Channel',
            'Discount',
            'Notes'
        ];
    }
}
