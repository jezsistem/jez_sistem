<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function fetchNotifications()
    {
        // Fetch unread notifications
        $notifications = Notification::where('is_read', false)->where('stt_id', '=', Auth::user()->stt_id)
            ->orderBy('created_at', 'desc');

        $count = $notifications->get()->count();

        return response()->json([
            'notifications' => $notifications->limit(5)->get(),
            'count' => $count
        ]);
    }
}