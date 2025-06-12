<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoRecommendation extends Model
{
    use HasFactory;

    protected $table = 'promo_recommendations';

    protected $fillable = ['p_id', 'channel', 'discount', 'notes', 'created_at', 'updated_at'];
}
