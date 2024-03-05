<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiOnline extends Model
{
    use HasFactory;
    protected $table = 'transaction_online';

    protected $fillable = [
        'pls_id',
        'pst_id',
        'sz_name',
        'pls_qty',
        'ps_barcode',
        'st_id',
        'platform_type',
        'order_number',
        'order_status',
        'order_substatus',
        'cancel_type',
        'cancel_by',
        'reason_cancellation',
        'pre_order',
        'resi_number',
        'shipping_method',
        'ship_deadline',
        'ship_delivery_date',
        'order_date_created',
        'payment_date',
        'payment_method',
        'SKU',
        'original_price',
        'price_after_discount',
        'quantity',
        'return_quantity',
        'seller_note',
        'total_price',
        'total_discount',
        'shipping_fee',
        'voucher_seller',
        'cashback_coin',
        'voucher',
        'voucher_platform',
        'discount_seller',
        'discount_platform',
        'shopee_coin_pieces',
        'credit_card_discounts',
        'shipping_costs',
        'total_payment',
        'city',
        'province',
        'order_complete_at',
    ];
}
