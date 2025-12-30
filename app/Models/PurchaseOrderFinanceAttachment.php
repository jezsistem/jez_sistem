<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderFinanceAttachment extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_finance_attachment';

    protected $fillable = [
        'po_id',
        'file_path',
        'description',
    ];
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }
}
