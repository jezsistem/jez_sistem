<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['stt_id', 'ud_id', 'user_id', 'message', 'is_read', 'type', 'data'];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function storeType()
    {
        return $this->belongsTo(StoreType::class, 'stt_id');
    }

    public function userDivision()
    {
        return $this->belongsTo(\App\Models\UserDivision::class, 'ud_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Helper method to create notification for HR features (using ud_id)
    public static function createHRNotification($udId, $userId, $message, $type = 'hr', $data = [])
    {
        return self::create([
            'ud_id' => $udId,
            'stt_id' => $udId, // For compatibility, set both
            'user_id' => $userId,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'is_read' => false
        ]);
    }

    // Helper method to create notification for other features (using stt_id)
    public static function createSystemNotification($sttId, $message, $type = 'system', $data = [])
    {
        return self::create([
            'stt_id' => $sttId,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'is_read' => false
        ]);
    }
}
