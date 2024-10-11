<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineTransactions extends Model
{
    use HasFactory;

    protected $table = 'online_transactions';

    protected $fillable = [
        'id',
        'order_number',
        'order_status',
        'reason_cancellation',
        'platform_name',
        'no_resi',
        'shipping_method',
        'shipping_fee',
        'order_date_created',
        'payment_date',
        'payment_method',
        'total_payment',
        'city',
        'province'
    ];
}
