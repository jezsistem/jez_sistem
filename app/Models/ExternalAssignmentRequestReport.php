<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalAssignmentRequestReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'ear_id',
        'earr_detail',
        'earr_date',
        'earr_time_start',
        'earr_time_end',
        'cash_amount',
    ];

    public function ear()
    {
        return $this->belongsTo(ExternalAssignmentRequest::class, 'ear_id');
    }
}