<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRecap extends Model
{
    use HasFactory;

    protected $table = 'delivery_recaps';

    protected $fillable = [
        'expedition_id',
        'courier_name',
        'courier_phone',
        'import_file',
        'signature_pic',
        'signature_courier',
        'recap_date',
        'created_by',
    ];

    /**
     * Relasi ke user (opsional)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke courier (opsional)
     */
    public function courier()
    {
        return $this->belongsTo(Courier::class, 'expedition_id');
    }

    /**
     * Relasi ke tabel detail (delivery_receipts)
     */
    public function receipts()
    {
        return $this->hasMany(DeliveryReceipt::class, 'dr_id');
    }
}
