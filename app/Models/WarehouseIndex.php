<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseIndex extends Model
{
    use HasFactory;

    protected $fillable = [
        'st_id',
        'w_code',
    ];

    protected $table = 'warehouse_index';
}
