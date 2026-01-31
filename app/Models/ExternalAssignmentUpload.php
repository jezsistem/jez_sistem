<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalAssignmentUpload extends Model
{
    use HasFactory;

    protected $table = 'external_assigment_requester_uploads';

    protected $fillable = [
        'ear_id',
        'file_path',
    ];

    public function request()
    {
        return $this->belongsTo(ExternalAssignmentRequest::class, 'ear_id');
    }
}
