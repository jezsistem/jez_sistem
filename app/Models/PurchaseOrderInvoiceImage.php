<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderInvoiceImage extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders_invoice_image';

    protected $fillable = [
        'purchase_order_id',
        'invoice_image',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
