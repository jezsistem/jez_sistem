<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function fetchNotifications()
    {
        // Fetch unread notifications
        $notifications = Notification::where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $count = $notifications->count();

        return response()->json([
            'notifications' => $notifications,
            'count' => $count
        ]);
    }
}
