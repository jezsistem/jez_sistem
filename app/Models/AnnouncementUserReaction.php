<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementUserReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'user_id',
        'reaction_id'
    ];

    // Relationships
    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reaction(): BelongsTo
    {
        return $this->belongsTo(AnnouncementReaction::class, 'reaction_id');
    }
}
