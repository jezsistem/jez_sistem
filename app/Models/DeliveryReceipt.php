<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'dr_id',
        'resi',
        'marketplace_name',
        'item_qty',
        'city_destinations',
        'note',
    ];

    public function confirmation()
    {
        return $this->belongsTo(DeliveryRecap::class, 'dr_id');
    }
}
