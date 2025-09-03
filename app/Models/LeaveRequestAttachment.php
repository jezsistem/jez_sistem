<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequestAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_request_id',
        'file_path',
        'original_name',
        'file_type',
        'file_size'
    ];

    protected $casts = [
        'file_size' => 'integer'
    ];

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }
}
