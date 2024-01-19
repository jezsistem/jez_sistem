<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrder extends Model
{
    use HasFactory;

    protected $table = 'pre_orders';

    protected $fillable = [
        'st_id',
        'br_id',
        'ss_id',
        'ps_id',
        'pre_order_code',
        'po_delete',
        'po_draft',
    ];
}
