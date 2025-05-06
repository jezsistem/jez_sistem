<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CekDanaOnline extends Model
{
    use HasFactory;

    protected $table = 'online_funds';

    protected $fillable = [
        'st_id',
        'platform_name',
        'order_number',
        'total_disburshed_amount',
        'final_price',
        'total_online_cut',
        'seller_voucher_discount',
        'affiliate_cut',
        'marketplace_commision_fee',
        'service_fee',
        'voucher_xtra_service_fee',
        'cashback_service_fee',
        'cashout_date',
        'transaction_date'
    ];
}
