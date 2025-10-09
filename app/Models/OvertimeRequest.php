<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_by',
        'submission_date',
        'department',
        'assigned_staff',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'details',
        'attachment',
        'claim',
    ];

    protected $casts = [
        'assigned_staff' => 'array',
    ];

    public function requester()
    {
        return $this->belongsTo(\App\Models\User::class, 'request_by');
    }
}