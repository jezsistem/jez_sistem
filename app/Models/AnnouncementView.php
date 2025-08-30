<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementView extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'user_id', 
        'viewed_at',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'viewed_at' => 'datetime'
    ];

    /**
     * Get the announcement that was viewed
     */
    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    /**
     * Get the user who viewed the announcement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get views for a specific announcement
     */
    public function scopeForAnnouncement($query, $announcementId)
    {
        return $query->where('announcement_id', $announcementId);
    }

    /**
     * Scope to get views by a specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get views within a date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('viewed_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get unique viewers (distinct users)
     */
    public function scopeUniqueViewers($query)
    {
        return $query->distinct('user_id');
    }
}