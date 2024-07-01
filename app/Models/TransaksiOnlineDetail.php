<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiOnlineDetail extends Model
{
    use HasFactory;

    protected $table = 'transaction_online_details';

    protected $fillable = [
        'to_id',
        'order_status',
        'order_substatus',
        'cancel_type',
        'cancel_by',
        'reason_cancellation',
        'payment_date'
    ];

    public function transaksiOnline()
    {
        return $this->belongsTo(TransaksiOnline::class);
    }
}
