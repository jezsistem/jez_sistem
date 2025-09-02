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
                // Cek apakah ada record yang sama (SEMUA STATUS)
                $existingRecord = DB::table($this->table)
                    ->where('user_id', $data['user_id'])
                    ->where('bt_date', $data['bt_date'])
                    ->where('bt_type', $data['bt_type'])
                    ->first();

                if ($existingRecord) {
                    // Jika ada record cancelled/completed, update menjadi active
                    if (in_array($existingRecord->bt_status, ['cancelled', 'completed'])) {
                        $updateData = [
                            'bt_start_time' => $data['bt_start_time'],
                            'bt_end_time' => null,
                            'bt_duration_minutes' => 0,
                            'bt_status' => 'active',
                            'bt_notes' => null,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];

                        $result = DB::table($this->table)
                            ->where('id', $existingRecord->id)
                            ->update($updateData);

                        if ($result) {
                            \Log::info('BreakTime storeData - Updated existing record to active', [
                                'record_id' => $existingRecord->id,
                                'old_status' => $existingRecord->bt_status,
                                'new_status' => 'active',
                                'user_id' => $data['user_id'],
                                'bt_date' => $data['bt_date'],
                                'bt_type' => $data['bt_type']
                            ]);
                            return $existingRecord->id;
                        }
                    } else if ($existingRecord->bt_status === 'active') {
                        // Jika ada record active, return false
                        \Log::warning('BreakTime storeData - Active break already exists', [
                            'existing_record_id' => $existingRecord->id,
                            'user_id' => $data['user_id'],
                            'bt_date' => $data['bt_date'],
                            'bt_type' => $data['bt_type']
                        ]);
                        return false;
                    }
                }

                // Normal insert jika tidak ada record yang sama
                $store = DB::table($this->table)->insertGetId(array_merge($data, $created));
                
                \Log::info('BreakTime storeData - New record inserted', [
                    'inserted_id' => $store,
                    'user_id' => $data['user_id'],
                    'bt_date' => $data['bt_date'],
                    'bt_type' => $data['bt_type']
                ]);
                
                return $store;

            } catch (\Illuminate\Database\QueryException $ex) {
                if($ex->getCode() === '23000') {
                    \Log::error('BreakTime storeData - Duplicate entry error', [
                        'error' => $ex->getMessage(),
                        'data' => $data
                    ]);
                    return false;
                }
                throw $ex;
            }
        } else if ($mode == 'edit') {
            try {
                $store = DB::table($this->table)->where('id', $id)->update(array_merge($data, $updated));
                return $store;
            } catch (\Illuminate\Database\QueryException $ex) {
                if($ex->getCode() === '23000') {
                    return false;
                }
            }
        } else {
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


} 