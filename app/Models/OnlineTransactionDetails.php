<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineTransactionDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'online_transaction_details';

    protected $fillable = [
        'id',
        'to_id',
        'order_number',
        'sku',
        'original_price',
        'price_after_discount',
        'qty',
        'return_qty',
        'total_discount',
        'discount_seller',
        'discount_platform',
        'ns_before_admin',
        'platform_name',
        'warehouse',
        'delivery_insurance',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];
}
