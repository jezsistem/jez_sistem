<?php

namespace App\Http\Controllers;

use App\Models\UserShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class UserShiftController extends Controller
{
    public function startShift(Request $request)
    {
        $user = Auth::user();

        //create shift
        UserShift::insert([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'), // '2021-01-01
            'start_time' => now(),
            'end_time' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        return response()->json([
            'message' => 'Shift started',
        ]);
    }

    public function endShift(Request $request)
    {
        $laba = $request->_laba_;
        try {
            $user = Auth::user();

            DB::table('user_shifts')
                ->where('user_id', $user->id)
                ->where('date', now()->format('Y-m-d'))
                ->whereNull('end_time')
                ->update([
                    'end_time'      => now(),
                    'laba_shift'    => $laba
                ]);

            // After update success, broadcast to all bot users
            $this->broadcastShiftEnd('🚨 Shift has ended! New laba: ' . number_format($laba, 0, ',', '.'));

            return response()->json([
                'status' => '200',
                'message' => 'Shift ended',
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'status' => '404',
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function broadcastShiftEnd($message)
    {
        $token = '7237510272:AAFThytZvx6iXPbeWMX-MoUK6C6hD96c8nI';

        $chatIds = DB::table('telegram_users')->pluck('chat_id');

        foreach ($chatIds as $chatId) {
            $url = "https://api.telegram.org/bot$token/sendMessage";

            $response = Http::post($url, [
                'chat_id' => $chatId,
                'text'    => $message,
                'parse_mode' => 'Markdown',
            ]);

            if ($response->successful()) {
                Log::info("✅ Message sent successfully to chat_id: {$chatId}");
            } else {
                Log::error("❌ Failed to send message to chat_id: {$chatId}. Response: " . $response->body());
            }
        }
    }

    public function checkUserShift(Request $request)
    {
        $user = Auth::user();

        //get shift
        $shift = UserShift::where('user_id', $user->id)
            ->where('date', now()->format('Y-m-d'))
            ->whereNull('end_time')
            ->first();

        if ($shift) {
            return response()->json([
                'status' => '200',
                'message' => 'Shift started',
                'shiftStatus' => 1
            ], 200);
        }

        return response()->json([
            'status' => '404',
            'message' => 'Shift not started',
            'shiftStatus' => 0
        ], 404);
    }
}
