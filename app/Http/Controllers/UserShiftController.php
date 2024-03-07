<?php

namespace App\Http\Controllers;

use App\Models\UserShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserShiftController extends Controller
{
    public function startShift(Request $request)
    {
        $user = Auth::user();

        // check if user has already started shift
//        $shift = UserShift::where('user_id', $user->id)
//            ->where('date', now()->format('Y-m-d'))
//            ->whereNull('end_time')
//            ->first();
//
//        if ($shift) {
//            return response()->json([
//                'status' => '400',
//                'message' => 'Shift already started',
//            ], 400);
//        }

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
            ], 200);
        }

        return response()->json([
            'status' => '404',
            'message' => 'Shift not started',
        ], 404);
    }
}
