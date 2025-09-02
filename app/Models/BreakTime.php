<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BreakTime extends Model
{
    use HasFactory;

    protected $table = 'break_times';
    protected $fillable = [
        'user_id',
        'daily_schedule_id',
        'bt_date',
        'bt_start_time',
        'bt_end_time',
        'bt_duration_minutes',
        'bt_type',
        'bt_status',
        'bt_notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'bt_date' => 'date',
        'bt_start_time' => 'datetime:H:i',
        'bt_end_time' => 'datetime:H:i',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dailySchedule()
    {
        return $this->belongsTo(DailySchedule::class, 'daily_schedule_id');
    }

    // Methods
    public function checkData($select, $where)
    {
        $affected = DB::table($this->table)
            ->select($select)
            ->where($where)
            ->get()->first();
        return $affected;
    }

    public function storeData($mode, $id, $data)
    {
        $created = [
            'created_at' => date('Y-m-d H:i:s')
        ];
        $updated = [
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($mode == 'add') {
            try {
                // Log the attempt to insert break time data
                \Log::info('BreakTime storeData - INSERT attempt', [
                    'table' => $this->table,
                    'data' => $data,
                    'created' => $created,
                    'merged_data' => array_merge($data, $created),
                    'user_id' => $data['user_id'] ?? null,
                    'bt_date' => $data['bt_date'] ?? null,
                    'bt_type' => $data['bt_type'] ?? null,
                    'timestamp' => now()
                ]);

                // Check for potential duplicate before insert (only ACTIVE breaks)
                $duplicateCheck = $this->checkForDuplicate($data);
                if ($duplicateCheck) {
                    \Log::warning('BreakTime storeData - Active break already exists, cannot start new break', [
                        'table' => $this->table,
                        'data' => $data,
                        'existing_active_record' => $duplicateCheck,
                        'user_id' => $data['user_id'] ?? null,
                        'bt_date' => $data['bt_date'] ?? null,
                        'bt_type' => $data['bt_type'] ?? null,
                        'timestamp' => now()
                    ]);
                    
                    return false;
                }

                // Log that no active duplicate was found
                \Log::info('BreakTime storeData - No active duplicate found, proceeding with insert', [
                    'user_id' => $data['user_id'] ?? null,
                    'bt_date' => $data['bt_date'] ?? null,
                    'bt_type' => $data['bt_type'] ?? null,
                    'timestamp' => now()
                ]);

                $store = DB::table($this->table)->insertGetId(array_merge($data, $created));
                
                \Log::info('BreakTime storeData - INSERT successful', [
                    'table' => $this->table,
                    'inserted_id' => $store,
                    'data' => $data,
                    'timestamp' => now()
                ]);
                
                return $store;
                
            } catch (\Illuminate\Database\QueryException $ex) {
                // Enhanced error logging for duplicate entry violations
                if ($ex->getCode() === '23000') {
                    $errorMessage = $ex->getMessage();
                    $isDuplicateEntry = strpos($errorMessage, 'Duplicate entry') !== false;
                    
                    if ($isDuplicateEntry) {
                        // Gunakan helper logging untuk duplicate entry
                        \App\Helpers\BreakTimeLogger::logDuplicateError($ex, $data, 'storeData_insert');
                    } else {
                        \Log::error('BreakTime storeData - Database constraint violation', [
                            'table' => $this->table,
                            'error_code' => $ex->getCode(),
                            'error_message' => $errorMessage,
                            'data' => $data,
                            'timestamp' => now()
                        ]);
                    }
                    
                    return false;
                } else {
                    \App\Helpers\BreakTimeLogger::logSystemError('storeData_insert', $ex, $data);
                    throw $ex;
                }
            } catch (\Exception $ex) {
                \App\Helpers\BreakTimeLogger::logSystemError('storeData_insert', $ex, $data);
                throw $ex;
            }
        } else if ($mode == 'edit') {
            try {
                \Log::info('BreakTime storeData - UPDATE attempt', [
                    'table' => $this->table,
                    'id' => $id,
                    'data' => $data,
                    'updated' => $updated,
                    'timestamp' => now()
                ]);

                $store = DB::table($this->table)->where('id', $id)->update(array_merge($data, $updated));
                
                \Log::info('BreakTime storeData - UPDATE result', [
                    'table' => $this->table,
                    'id' => $id,
                    'affected_rows' => $store,
                    'timestamp' => now()
                ]);
                
                return $store;
            } catch (\Illuminate\Database\QueryException $ex) {
                \Log::error('BreakTime storeData - UPDATE error', [
                    'table' => $this->table,
                    'id' => $id,
                    'error_code' => $ex->getCode(),
                    'error_message' => $ex->getMessage(),
                    'data' => $data,
                    'timestamp' => now(),
                    'trace' => $ex->getTraceAsString()
                ]);
                
                if($ex->getCode() === '23000') {
                    return false;
                }
                throw $ex;
            }
        } else {
            \Log::warning('BreakTime storeData - Invalid mode', [
                'table' => $this->table,
                'mode' => $mode,
                'data' => $data,
                'timestamp' => now()
            ]);
            return false;
        }
    }

    public function deleteData($id)
    {
        try {
            $delete = DB::table($this->table)->where('id', $id)->delete();
            if ($delete) {
                return true;
            } else {
                return false;
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            if($ex->getCode() === '23000') {
                return false;
            }
        }
    }

    // Get break times by date range with filters
    public function getBreakTimesByDateRange($startDate, $endDate, $userId = null, $divisionId = null, $status = null)
    {
        $query = DB::table($this->table)
            ->select([
                'break_times.*',
                'users.u_name',
                'users.u_nip',
                'user_divisions.ud_name'
            ])
            ->leftJoin('users', 'users.id', '=', 'break_times.user_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->whereBetween('bt_date', [$startDate, $endDate])
            ->orderBy('bt_date', 'desc')
            ->orderBy('bt_start_time', 'desc');

        if ($userId) {
            $query->where('break_times.user_id', $userId);
        }

        if ($divisionId) {
            $query->where('users.ud_id', $divisionId);
        }

        if ($status) {
            $query->where('break_times.bt_status', $status);
        }

        return $query->get();
    }

    // Get break times by filters (alias for getBreakTimesByDateRange)
    public function getBreakTimesByFilters($startDate = null, $endDate = null, $userId = null, $status = null)
    {
        return $this->getBreakTimesByDateRange($startDate, $endDate, $userId, null, $status);
    }

    // Get current user's active break
    public function getCurrentUserActiveBreak($userId)
    {
        return DB::table($this->table)
            ->where('user_id', $userId)
            ->where('bt_date', date('Y-m-d'))
            ->where('bt_status', 'active')
            ->first();
    }

    // Get break allowance based on shift type
    public function getBreakAllowance($shiftType)
    {
        switch ($shiftType) {
            case 'FULL TIME':
                return [
                    'break_1' => ['duration' => 60, 'count' => 1]
                ];
            case 'PART TIME':
                return [
                    'break_1' => ['duration' => 30, 'count' => 1]
                ];
            case 'PART FULL':
                return [
                    'break_1' => ['duration' => 30, 'count' => 1],
                    'break_2' => ['duration' => 30, 'count' => 1]
                ];
            case 'CASUAL':
                return [
                    'break_1' => ['duration' => 30, 'count' => 1]
                ];
            case 'ALL':
            case 'MULTIPLE':
                // For ALL/MULTIPLE types, use default PART TIME allowance
                return [
                    'break_1' => ['duration' => 30, 'count' => 1]
                ];
            default:
                return [
                    'break_1' => ['duration' => 30, 'count' => 1]
                ];
        }
    }

    // Check if user can start break
    public function canStartBreak($userId, $breakType = 'break_1')
    {
        $today = date('Y-m-d');
        
        // Get user's daily schedule
        $dailySchedule = DailySchedule::where('user_id', $userId)
            ->where('ds_date', $today)
            ->with('shiftCode')
            ->first();

        if (!$dailySchedule || !$dailySchedule->shiftCode) {
            return false;
        }

        // Get shift type using new compatibility method
        $shiftType = $dailySchedule->shiftCode->getBreakAllowancePrimaryType();
        $breakAllowance = $this->getBreakAllowance($shiftType);

        // Check if this break type is allowed
        if (!isset($breakAllowance[$breakType])) {
            return false;
        }

        // Check if break already started
        $existingBreak = DB::table($this->table)
            ->where('user_id', $userId)
            ->where('bt_date', $today)
            ->where('bt_type', $breakType)
            ->where('bt_status', 'active')
            ->first();

        if ($existingBreak) {
            return false;
        }

        // Check if break quota is exceeded
        $completedBreaksCount = DB::table($this->table)
            ->where('user_id', $userId)
            ->where('bt_date', $today)
            ->where('bt_type', $breakType)
            ->where('bt_status', 'completed')
            ->count();

        if ($completedBreaksCount >= $breakAllowance[$breakType]['count']) {
            return false;
        }

        return true;
    }

    // Check if user can end break
    public function canEndBreak($userId, $breakType = 'break_1')
    {
        $today = date('Y-m-d');
        
        $activeBreak = DB::table($this->table)
            ->where('user_id', $userId)
            ->where('bt_date', $today)
            ->where('bt_type', $breakType)
            ->where('bt_status', 'active')
            ->first();

        return $activeBreak ? true : false;
    }

    // Check if break has expired automatically
    public function isBreakExpired($userId, $breakType = 'break_1')
    {
        $today = date('Y-m-d');
        
        $expiredBreak = DB::table($this->table)
            ->where('user_id', $userId)
            ->where('bt_date', $today)
            ->where('bt_type', $breakType)
            ->where('bt_status', 'expired')
            ->first();

        return $expiredBreak ? true : false;
    }

    // Start break
    public function startBreak($userId, $breakType = 'break_1')
    {
        if (!$this->canStartBreak($userId, $breakType)) {
            return false;
        }

        $today = date('Y-m-d');
        $now = date('H:i:s');

        $data = [
            'user_id' => $userId,
            'daily_schedule_id' => null, // Will be updated later
            'bt_date' => $today,
            'bt_start_time' => $now,
            'bt_end_time' => null,
            'bt_duration_minutes' => 0,
            'bt_type' => $breakType,
            'bt_status' => 'active',
            'bt_notes' => null,
            'created_by' => auth()->user()->u_name ?? 'system',
        ];

        // Get daily schedule
        $dailySchedule = DailySchedule::where('user_id', $userId)
            ->where('ds_date', $today)
            ->first();

        if ($dailySchedule) {
            $data['daily_schedule_id'] = $dailySchedule->id;
        }

        return $this->storeData('add', null, $data);
    }

    // End break
    public function endBreak($userId, $breakType = 'break_1')
    {
        if (!$this->canEndBreak($userId, $breakType)) {
            return false;
        }

        $today = date('Y-m-d');
        $now = date('H:i:s');

        $activeBreak = DB::table($this->table)
            ->where('user_id', $userId)
            ->where('bt_date', $today)
            ->where('bt_type', $breakType)
            ->where('bt_status', 'active')
            ->first();

        if (!$activeBreak) {
            return false;
        }

        $startTime = Carbon::parse($activeBreak->bt_start_time);
        $endTime = Carbon::parse($now);
        $durationMinutes = $startTime->diffInMinutes($endTime);

        $data = [
            'bt_end_time' => $now,
            'bt_duration_minutes' => $durationMinutes,
            'bt_status' => 'completed',
            'updated_by' => auth()->user()->u_name ?? 'system',
        ];

        return $this->storeData('edit', $activeBreak->id, $data);
    }

    // Get break statistics
    public function getBreakStats($startDate, $endDate, $userId = null)
    {
        $query = DB::table($this->table)
            ->select([
                'bt_status',
                DB::raw('COUNT(*) as total')
            ])
            ->whereBetween('bt_date', [$startDate, $endDate])
            ->groupBy('bt_status');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    /**
     * Check for potential duplicate break time record
     * Only check for ACTIVE breaks, not cancelled or completed ones
     * 
     * @param array $data
     * @return mixed|null
     */
    private function checkForDuplicate($data)
    {
        if (!isset($data['user_id']) || !isset($data['bt_date']) || !isset($data['bt_type'])) {
            return null;
        }

        // Only check for active breaks, not cancelled or completed ones
        return DB::table($this->table)
            ->where('user_id', $data['user_id'])
            ->where('bt_date', $data['bt_date'])
            ->where('bt_type', $data['bt_type'])
            ->where('bt_status', 'active')
            ->first();
    }

    /**
     * Get existing break record that matches the duplicate entry criteria
     * 
     * @param array $data
     * @return mixed|null
     */
    private function getExistingBreakRecord($data)
    {
        if (!isset($data['user_id']) || !isset($data['bt_date']) || !isset($data['bt_type'])) {
            return null;
        }

        return DB::table($this->table)
            ->where('user_id', $data['user_id'])
            ->where('bt_date', $data['bt_date'])
            ->where('bt_type', $data['bt_type'])
            ->first();
    }

    /**
     * Enhanced start break method with comprehensive logging
     * 
     * @param int $userId
     * @param string $breakType
     * @return mixed
     */
    public function startBreakWithLogging($userId, $breakType = 'break_1')
    {
        \Log::info('BreakTime startBreakWithLogging - Starting break attempt', [
            'user_id' => $userId,
            'break_type' => $breakType,
            'timestamp' => now()
        ]);

        // Check if user can start break
        if (!$this->canStartBreak($userId, $breakType)) {
            \Log::warning('BreakTime startBreakWithLogging - Cannot start break', [
                'user_id' => $userId,
                'break_type' => $breakType,
                'reason' => 'Validation failed - check canStartBreak method',
                'timestamp' => now()
            ]);
            return false;
        }

        $today = date('Y-m-d');
        $now = date('H:i:s');

        $data = [
            'user_id' => $userId,
            'daily_schedule_id' => null, // Will be updated later
            'bt_date' => $today,
            'bt_start_time' => $now,
            'bt_end_time' => null,
            'bt_duration_minutes' => 0,
            'bt_type' => $breakType,
            'bt_status' => 'active',
            'bt_notes' => null,
            'created_by' => auth()->user()->u_name ?? 'system',
        ];

        // Get daily schedule
        $dailySchedule = DailySchedule::where('user_id', $userId)
            ->where('ds_date', $today)
            ->first();

        if ($dailySchedule) {
            $data['daily_schedule_id'] = $dailySchedule->id;
            \Log::info('BreakTime startBreakWithLogging - Daily schedule found', [
                'user_id' => $userId,
                'daily_schedule_id' => $dailySchedule->id,
                'timestamp' => now()
            ]);
        } else {
            \Log::warning('BreakTime startBreakWithLogging - No daily schedule found', [
                'user_id' => $userId,
                'bt_date' => $today,
                'timestamp' => now()
            ]);
        }

        $result = $this->storeData('add', null, $data);
        
        if ($result) {
            \Log::info('BreakTime startBreakWithLogging - Break started successfully', [
                'user_id' => $userId,
                'break_type' => $breakType,
                'break_id' => $result,
                'timestamp' => now()
            ]);
        } else {
            \Log::error('BreakTime startBreakWithLogging - Failed to start break', [
                'user_id' => $userId,
                'break_type' => $breakType,
                'data' => $data,
                'timestamp' => now()
            ]);
        }

        return $result;
    }
} 