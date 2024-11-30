<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRecap extends Model
{
    use HasFactory;

    protected $table = 'delivery_recap';

    protected $fillable = [
        'id',
        'sender_id',
        'courier_id',
        'st_id',
        'expedition',
        'note',
        'created_at',
        'updated_at'
    ];
}
