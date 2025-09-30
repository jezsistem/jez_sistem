<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BreakTimeDebugger
{
    /**
     * Debug break time status untuk user tertentu
     *
     * @param int $userId
     * @param string $date
     * @return array
     */
    public static function debugUserBreakStatus($userId, $date = null)
    {
        $date = $date ?? date('Y-m-d');
        
        // Get user info
        $user = DB::table('users')
            ->select('id', 'u_name', 'u_nip')
            ->where('id', $userId)
            ->first();

        // Get all break records for the date
        $breakRecords = DB::table('break_times')
            ->where('user_id', $userId)
            ->where('bt_date', $date)
            ->orderBy('bt_type')
            ->orderBy('created_at')
            ->get();

        // Get daily schedule
        $dailySchedule = DB::table('daily_schedules')
            ->where('user_id', $userId)
            ->where('ds_date', $date)
            ->first();

        // Get shift code info
        $shiftCode = null;
        if ($dailySchedule && $dailySchedule->sc_id) {
            $shiftCode = DB::table('shift_codes')
                ->where('id', $dailySchedule->sc_id)
                ->first();
        }

        $debugInfo = [
            'user_info' => $user,
            'date' => $date,
            'daily_schedule' => $dailySchedule,
            'shift_code' => $shiftCode,
            'break_records' => $breakRecords,
            'break_summary' => [
                'total_breaks' => $breakRecords->count(),
                'active_breaks' => $breakRecords->where('bt_status', 'active')->count(),
                'completed_breaks' => $breakRecords->where('bt_status', 'completed')->count(),
                'cancelled_breaks' => $breakRecords->where('bt_status', 'cancelled')->count(),
                'break_1_count' => $breakRecords->where('bt_type', 'break_1')->count(),
                'break_2_count' => $breakRecords->where('bt_type', 'break_2')->count(),
            ]
        ];

        // Log debug info
        Log::info('🔍 BREAK TIME DEBUG INFO', $debugInfo);

        return $debugInfo;
    }

    /**
     * Check if user can start break dengan detail
     *
     * @param int $userId
     * @param string $breakType
     * @param string $date
     * @return array
     */
    public static function canStartBreakDetailed($userId, $breakType = 'break_1', $date = null)
    {
        $date = $date ?? date('Y-m-d');
        
        $breakTime = new \App\Models\BreakTime();
        $canStart = $breakTime->canStartBreak($userId, $breakType);
        
        // Get detailed info
        $dailySchedule = DB::table('daily_schedules')
            ->where('user_id', $userId)
            ->where('ds_date', $date)
            ->first();

        $existingActiveBreak = DB::table('break_times')
            ->where('user_id', $userId)
            ->where('bt_date', $date)
            ->where('bt_type', $breakType)
            ->where('bt_status', 'active')
            ->first();

        $completedBreaksCount = DB::table('break_times')
            ->where('user_id', $userId)
            ->where('bt_date', $date)
            ->where('bt_type', $breakType)
            ->where('bt_status', 'completed')
            ->count();

        $allBreaksForType = DB::table('break_times')
            ->where('user_id', $userId)
            ->where('bt_date', $date)
            ->where('bt_type', $breakType)
            ->get();

        $debugInfo = [
            'user_id' => $userId,
            'break_type' => $breakType,
            'date' => $date,
            'can_start' => $canStart,
            'daily_schedule_exists' => !is_null($dailySchedule),
            'daily_schedule' => $dailySchedule,
            'existing_active_break' => $existingActiveBreak,
            'completed_breaks_count' => $completedBreaksCount,
            'all_breaks_for_type' => $allBreaksForType,
            'break_allowance' => $dailySchedule ? $breakTime->getBreakAllowance($dailySchedule->sc_type ?? 'PART TIME') : null
        ];

        Log::info('🔍 CAN START BREAK DEBUG', $debugInfo);

        return $debugInfo;
    }

    /**
     * Log semua break time yang ada untuk debugging
     *
     * @param int $userId
     * @param string $date
     * @return void
     */
    public static function logAllBreakTimes($userId, $date = null)
    {
        $date = $date ?? date('Y-m-d');
        
        $allBreaks = DB::table('break_times')
            ->where('user_id', $userId)
            ->where('bt_date', $date)
            ->orderBy('bt_type')
            ->orderBy('created_at')
            ->get();

        Log::info('📋 ALL BREAK TIMES FOR USER', [
            'user_id' => $userId,
            'date' => $date,
            'total_records' => $allBreaks->count(),
            'records' => $allBreaks->toArray()
        ]);
    }

    /**
     * Check constraint violations untuk break time
     *
     * @param array $data
     * @return array
     */
    public static function checkConstraintViolations($data)
    {
        $violations = [];

        // Check unique constraint: user_id + bt_date + bt_type
        if (isset($data['user_id']) && isset($data['bt_date']) && isset($data['bt_type'])) {
            $existingRecord = DB::table('break_times')
                ->where('user_id', $data['user_id'])
                ->where('bt_date', $data['bt_date'])
                ->where('bt_type', $data['bt_type'])
                ->first();

            if ($existingRecord) {
                $violations[] = [
                    'type' => 'unique_constraint',
                    'constraint' => 'ts_break_times_user_id_bt_date_bt_type_unique',
                    'message' => "Duplicate entry '{$data['user_id']}-{$data['bt_date']}-{$data['bt_type']}'",
                    'existing_record' => $existingRecord
                ];
            }
        }

        // Check foreign key constraints
        if (isset($data['user_id'])) {
            $userExists = DB::table('users')->where('id', $data['user_id'])->exists();
            if (!$userExists) {
                $violations[] = [
                    'type' => 'foreign_key',
                    'constraint' => 'break_times_user_id_foreign',
                    'message' => "User ID {$data['user_id']} does not exist"
                ];
            }
        }

        if (isset($data['daily_schedule_id']) && $data['daily_schedule_id']) {
            $scheduleExists = DB::table('daily_schedules')->where('id', $data['daily_schedule_id'])->exists();
            if (!$scheduleExists) {
                $violations[] = [
                    'type' => 'foreign_key',
                    'constraint' => 'break_times_daily_schedule_id_foreign',
                    'message' => "Daily schedule ID {$data['daily_schedule_id']} does not exist"
                ];
            }
        }

        Log::info('🔍 CONSTRAINT VIOLATIONS CHECK', [
            'data' => $data,
            'violations' => $violations
        ]);

        return $violations;
    }
}
