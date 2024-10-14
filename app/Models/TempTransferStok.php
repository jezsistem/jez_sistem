<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempTransferStok extends Model
{
    use HasFactory;

    protected $table = 'temp_transfer_stocks';

    protected $fillable = [
        'pls_id',
        'pls_qty',
        'ps_barcode',
        'st_id'
    ];
}
