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
        'province',
        'internal_order_status',
        'courier',
        'scan_manifest',
        'print_manifest',
        'time_print',
        'online_print',
        'print_resi',
        'time_print_resi',
    ];

    public static function getCourierAttribute($courier)
    {
        $shippingMethod = $courier;
        
        if (stripos($shippingMethod, 'SPX') !== false) {
            return 'SPX';
        } elseif (stripos($shippingMethod, 'J&T') !== false || stripos($shippingMethod, 'JNT') !== false) {
            return 'J&T';
        } elseif (stripos($shippingMethod, 'Anteraja') !== false) {
            return 'Anteraja';
        } elseif (stripos($shippingMethod, 'JNE') !== false) {
            return 'JNE';
        } elseif (stripos($shippingMethod, 'Gosend') !== false || stripos($shippingMethod, 'JNT') !== false) {
            return 'Gojek';
        } elseif (stripos($shippingMethod, 'Grab') !== false) {
            return 'Grab';
        }
        
        return $shippingMethod;
    }
}
