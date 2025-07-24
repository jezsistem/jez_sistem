<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrderFile extends Model
{
    use HasFactory;

    protected $table = 'pre_order_file';

    protected $fillable = [
        'pre_order_id',
        'file_pre_orders',
    ];

    public function PreOrder()
    {
        return $this->belongsTo(PreOrder::class);
    }
}
