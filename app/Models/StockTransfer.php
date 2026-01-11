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
        'u_id',
        'u_id_receive',
        'st_id_start',
        'st_id_end',
        'stf_code',
        'stf_status',
        'created_at',
        'updated_at',
    ];

    public const STATUS_HANGING = 0;
    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_DONE = 2;
    public const STATUS_DRAFT = 3;
}
