<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalAssignmentRequestDetail extends Model
{
    use HasFactory;

    protected $table = 'external_assignments_request_details';

    protected $fillable = [
        'ear_id',
        'rundown_date',
        'rundown_time',
        'activity',
        'notes',
    ];

    /**
     * Relasi ke request utama.
     */
    public function request()
    {
        return $this->belongsTo(ExternalAssignmentRequests::class, 'ear_id');
    }
}
