<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalAssignmentRequestCashDetail extends Model
{
    protected $fillable = [
        'ear_id',
        'cash_purpose',
        'cash_amount',
    ];

    public function request()
    {
        return $this->belongsTo(ExternalAssignmentRequest::class, 'ear_id');
    }
}