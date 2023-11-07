<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockTransfer extends Model
{
    use HasFactory;
    protected $table = 'stock_transfers';
    protected $fillable = [
        'st_id_start',
        'st_id_end',
        'stf_code',
        'stf_status',
        'created_at',
        'updated_at',
    ];
}
