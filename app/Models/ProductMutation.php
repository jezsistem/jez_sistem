<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductMutation extends Model
{
    use HasFactory;
    protected $table = 'product_mutations';
    protected $fillable = [
        'pls_id',
        'pl_id',
        'u_id',
        'pmt_old_qty',
        'pmt_qty',
        'created_at',
    ];
}
