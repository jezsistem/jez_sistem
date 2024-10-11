<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TempMutasi extends Model
{
    use HasFactory;

    protected $table = 'temp_mutasi';

    protected $fillable = [
        'pls_id',
        'pls_qty',
        'ps_barcode',
    ];
}
