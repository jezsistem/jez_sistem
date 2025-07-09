<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModalLock extends Model
{
    protected $fillable = ['user_id', 'lockable_type', 'lockable_id', 'expires_at'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function lockable() {
        return $this->morphTo();
    }
}
