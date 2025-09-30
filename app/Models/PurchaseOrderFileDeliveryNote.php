<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderFileDeliveryNote extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_file_delivery_note';

    protected $fillable = [
        'purchase_order_id',
        'file_delivery_note',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
