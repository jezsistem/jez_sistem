<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTraffic extends Model
{
    use HasFactory;

    protected  $table = 'customer_traffic';

    protected $fillable = [
        'type',
        'visit_date',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];
}
