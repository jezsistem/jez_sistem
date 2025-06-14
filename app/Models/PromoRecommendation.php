<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoRecommendation extends Model
{
    use HasFactory;

    protected $table = 'promo_recommendations';

    protected $fillable = ['u_id', 'pr_code', 'channel', 'created_at', 'updated_at'];

    public function details()
    {
        return $this->hasMany(PromoRecommendationDetail::class, 'pr_id');
    }
}
