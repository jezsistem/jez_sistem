<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderReceiveImportExcel extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_receive_import_excels';

    protected $fillable = [
        'po_id',
        'barcode',
        'qty',
        'status',
    ];

    public $timestamps = true;
}
