<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnnouncementReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'emoji',
        'color',
        'hide_announcement',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'hide_announcement' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function userReactions(): HasMany
    {
        return $this->hasMany(AnnouncementUserReaction::class, 'reaction_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orderBy('sort_order');
    }
}
