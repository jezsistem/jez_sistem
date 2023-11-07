<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockTransferDetail extends Model
{
    use HasFactory;
    protected $table = 'stock_transfer_details';
    protected $fillable = [
        'stf_id',
        'pst_id',
        'pl_id',
        'stfd_qty',
        'created_at',
        'updated_at',
    ];
}
