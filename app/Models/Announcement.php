<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content', 
        'category_id',
        'created_by',
        'target_type',
        'is_pinned',
        'status',
        'published_at'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'published_at' => 'datetime'
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(AnnouncementCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(AnnouncementRecipient::class);
    }

    public function userReactions(): HasMany
    {
        return $this->hasMany(AnnouncementUserReaction::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AnnouncementAttachment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    // Check if user can see this announcement
    public function isVisibleTo($userId, $userDivisionId = null)
    {
        // If announcement is for all users
        if ($this->target_type === 'all') {
            return true;
        }

        // Check if user has specific access
        $hasAccess = $this->recipients()
            ->where(function($query) use ($userId, $userDivisionId) {
                $query->where('recipient_type', 'user')
                      ->where('recipient_id', $userId);
                
                if ($userDivisionId) {
                    $query->orWhere(function($q) use ($userDivisionId) {
                        $q->where('recipient_type', 'division')
                          ->where('recipient_id', $userDivisionId);
                    });
                }
            })
            ->exists();

        return $hasAccess;
    }

    // Check if user has reacted with "Done" or "OK"
    public function isHiddenForUser($userId)
    {
        $userReaction = $this->userReactions()
            ->where('user_id', $userId)
            ->with('reaction')
            ->first();

        if (!$userReaction) {
            return false;
        }

        return $userReaction->reaction->hide_announcement;
    }

    /**
     * Get the views for the announcement
     */
    public function views(): HasMany
    {
        return $this->hasMany(AnnouncementView::class);
    }

    /**
     * Get unique viewers count
     */
    public function getUniqueViewersCountAttribute()
    {
        return $this->views()->distinct('user_id')->count('user_id');
    }

    /**
     * Get total views count
     */
    public function getTotalViewsCountAttribute()
    {
        return $this->views()->count();
    }

    /**
     * Check if user has viewed this announcement
     */
    public function isViewedByUser($userId)
    {
        return $this->views()->where('user_id', $userId)->exists();
    }
}
