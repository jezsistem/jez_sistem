<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalAssignmentRequestDetail extends Model
{
    protected $table = 'external_assignment_request_details';

    protected $fillable = [
        'ear_id',
        'activity',
        'rundown_date',
        'start_time',
        'end_time',
        'notes',
    ];

    public function request()
    {
        return $this->belongsTo(ExternalAssignmentRequest::class, 'ear_id');
    }
}