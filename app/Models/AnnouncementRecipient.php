<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'recipient_type',
        'recipient_id'
    ];

    // Relationships
    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    // Dynamic relationship based on recipient_type
    public function recipient()
    {
        if ($this->recipient_type === 'user') {
            return $this->belongsTo(User::class, 'recipient_id');
        } elseif ($this->recipient_type === 'division') {
            return $this->belongsTo(\App\Models\UserDivision::class, 'recipient_id');
        }
        
        return null;
    }
}
