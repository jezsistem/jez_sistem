<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function fetchNotifications()
    {
        $user = Auth::user();
        
        // Support both stt_id and ud_id for compatibility
        $divisionId = $user->ud_id ?? $user->stt_id;
        $userId = $user->id;
        
        // Fetch unread notifications for this user
        $notifications = Notification::where('is_read', false)
            ->where(function($query) use ($divisionId, $userId) {
                $query->where(function($subQuery) use ($divisionId, $userId) {
                    // Case 1: User-specific notifications (most targeted)
                    $subQuery->where('user_id', $userId)
                    // Case 2: Division-level notifications BUT only if no specific user_id is set
                    ->orWhere(function($divQuery) use ($divisionId) {
                        $divQuery->where(function($innerQuery) use ($divisionId) {
                            $innerQuery->where('stt_id', $divisionId)
                                      ->orWhere('ud_id', $divisionId);
                        })
                        ->whereNull('user_id'); // Only division-wide notifications (no specific recipient)
                    });
                });
            })
            ->orderBy('created_at', 'desc');

        $count = $notifications->get()->count();

        return response()->json([
            'notifications' => $notifications->limit(10)->get(),
            'count' => $count
        ]);
    }

    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        $divisionId = $user->ud_id ?? $user->stt_id;
        $userId = $user->id;
        
        // Mark all notifications as read for this user
        Notification::where('is_read', false)
            ->where(function($query) use ($divisionId, $userId) {
                $query->where(function($subQuery) use ($divisionId, $userId) {
                    // Case 1: User-specific notifications
                    $subQuery->where('user_id', $userId)
                    // Case 2: Division-level notifications without specific user_id
                    ->orWhere(function($divQuery) use ($divisionId) {
                        $divQuery->where(function($innerQuery) use ($divisionId) {
                            $innerQuery->where('stt_id', $divisionId)
                                      ->orWhere('ud_id', $divisionId);
                        })
                        ->whereNull('user_id');
                    });
                });
            })
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markSingleAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $divisionId = $user->ud_id ?? $user->stt_id;
        $userId = $user->id;
        
        // Mark single notification as read
        $notification = Notification::where('id', $id)
            ->where('is_read', false)
            ->where(function($query) use ($divisionId, $userId) {
                $query->where(function($subQuery) use ($divisionId, $userId) {
                    // Case 1: User-specific notifications
                    $subQuery->where('user_id', $userId)
                    // Case 2: Division-level notifications without specific user_id
                    ->orWhere(function($divQuery) use ($divisionId) {
                        $divQuery->where(function($innerQuery) use ($divisionId) {
                            $innerQuery->where('stt_id', $divisionId)
                                      ->orWhere('ud_id', $divisionId);
                        })
                        ->whereNull('user_id');
                    });
                });
            })
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }
}