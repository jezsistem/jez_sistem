<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrderArticleDetails extends Model
{
    use HasFactory;

    protected $table = 'pre_order_article_details';

    protected $fillable = [
        'poa_id',
        'pst_id',
        'poad_qty',
        'poad_purchase_price',
        'poad_total_price',
        'poad_draft'
    ];
}
