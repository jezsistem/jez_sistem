<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderTransferImage extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_transfer_images';

    protected $fillable = [
        'purchase_order_id',
        'transfer_image',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
