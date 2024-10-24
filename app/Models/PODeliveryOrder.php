<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PODeliveryOrder extends Model
{
    use HasFactory;

    protected $table = 'po_delivery_orders';

    protected $fillable = [
        'purchase_order_id',
        'delivery_orders_image',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
