<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDisputeFile extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders_file_dispute';

    protected $fillable = [
        'purchase_order_id',
        'file_dispute',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
