<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffDataComment extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id', 'user_id', 'comment'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff() {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
