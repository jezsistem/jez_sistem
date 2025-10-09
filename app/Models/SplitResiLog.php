<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SplitResiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_file',
        'split_file',
        'uploaded_by',
    ];

    // Relasi opsional (jika kamu pakai auth)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}