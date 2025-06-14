<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoRecommendationDetail extends Model
{
    use HasFactory;

    protected $table = 'promo_recommendation_details';

    protected $fillable = [
        'pr_id',
        'p_id',
        'discount',
        'notes',
        'created_at',
        'updated_at',
    ];

    public function promoRecommendation()
    {
        return $this->belongsTo(PromoRecommendation::class, 'pr_id');
    }
}
