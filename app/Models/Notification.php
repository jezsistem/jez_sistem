<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['stt_id', 'message', 'is_read'];

    public function storeType()
    {
        return $this->belongsTo(StoreType::class, 'stt_id');
    }
}
