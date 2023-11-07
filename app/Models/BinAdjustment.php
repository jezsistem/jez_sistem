<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BinAdjustment extends Model
{
    use HasFactory;
    protected $table = 'bin_adjustments';
    protected $fillable = [
        'pls_id',
        'u_id',
        'ba_code',
        'ba_old_qty',
        'ba_new_qty',
        'ba_adjust',
        'ba_adjust_type',
        'ba_note',
        'created_at',
    ];
}
