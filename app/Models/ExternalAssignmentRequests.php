<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalAssignmentRequests extends Model
{
    use HasFactory;

    protected $table = 'external_assignments_requests';

    protected $fillable = [
        'ea_id',
        'ear_date_start',
        'ear_time_start',
        'ear_date_end',
        'ear_time_end',
        'ear_locations',
        'ear_cash_advance',
    ];

    /**
     * Relasi ke master jenis assignment.
     */
    public function type()
    {
        return $this->belongsTo(ExternalAssignment::class, 'ea_id');
    }

    /**
     * Relasi ke detail (rundown).
     */
    public function details()
    {
        return $this->hasMany(ExternalAssignmentRequestDetail::class, 'ear_id');
    }
}