<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';
    protected $fillable = [
        'user_id',
        'daily_schedule_id',
        'at_date',
        'at_time_in',
        'at_time_out',
        'at_status',
        'at_notes',
        'at_source',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'at_date' => 'date',
        'at_time_in' => 'datetime:H:i',
        'at_time_out' => 'datetime:H:i',
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
            $store = DB::table($this->table)->insertGetId(array_merge($data, $created));
            return $store;
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

    // Get attendance by date range with filters
    public function getAttendanceByDateRange($startDate, $endDate, $userId = null, $divisionId = null, $status = null)
    {
        $query = DB::table($this->table)
            ->select([
                'attendance.*',
                'users.u_name',
                'users.u_nip',
                'user_divisions.ud_name',
                'daily_schedules.ds_start_time',
                'daily_schedules.ds_end_time',
                'shift_codes.sc_code',
                'shift_codes.sc_shift_name',
                'leave_requests.lr_status as leave_status',
                'leave_requests.lr_start_date as leave_start_date',
                'leave_requests.lr_end_date as leave_end_date',
                'leave_types.lt_name as leave_type_name',
                'leave_types.lt_code as leave_type_code',
                'leave_types.lt_color as leave_type_color'
            ])
            ->leftJoin('users', 'users.id', '=', 'attendance.user_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->leftJoin('daily_schedules', function($join) {
                $join->on('daily_schedules.user_id', '=', 'attendance.user_id')
                     ->on('daily_schedules.ds_date', '=', 'attendance.at_date');
            })
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->leftJoin('leave_requests', function($join) use ($startDate, $endDate) {
                $join->on('leave_requests.user_id', '=', 'attendance.user_id')
                     ->where('leave_requests.lr_status', '=', 'approved')
                     ->where(function($q) use ($startDate, $endDate) {
                         $q->whereBetween('leave_requests.lr_start_date', [$startDate, $endDate])
                           ->orWhereBetween('leave_requests.lr_end_date', [$startDate, $endDate])
                           ->orWhere(function($subQ) use ($startDate, $endDate) {
                               $subQ->where('leave_requests.lr_start_date', '<=', $startDate)
                                    ->where('leave_requests.lr_end_date', '>=', $endDate);
                           });
                     });
            })
            ->leftJoin('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
            ->whereBetween('at_date', [$startDate, $endDate])
            ->orderBy('at_date', 'desc')
            ->orderBy('users.u_name');

        if ($userId) {
            $query->where('attendance.user_id', $userId);
        }

        if ($divisionId) {
            $query->where('users.ud_id', $divisionId);
        }

        if ($status) {
            if (strpos($status, 'leave_') === 0) {
                $leaveTypeCode = str_replace('leave_', '', $status);
                $query->where('leave_types.lt_code', $leaveTypeCode);
            } else {
                $query->where('attendance.at_status', $status);
            }
        }

        return $query->get();
    }

    public function getAttendanceStats($startDate, $endDate, $userId = null)
    {
        // Get regular attendance stats including leave statuses
        $query = DB::table($this->table)
            ->select([
                DB::raw('COUNT(*) as total_records'),
                DB::raw('COUNT(CASE WHEN at_status = "present" THEN 1 END) as present_count'),
                DB::raw('COUNT(CASE WHEN at_status = "absent" THEN 1 END) as absent_count'),
                DB::raw('COUNT(CASE WHEN at_status = "late" THEN 1 END) as late_count'),
                DB::raw('COUNT(CASE WHEN at_status = "scan_once" THEN 1 END) as scan_once_count'),
                DB::raw('COUNT(CASE WHEN at_status = "early_leave" THEN 1 END) as early_leave_count'),
                DB::raw('COUNT(CASE WHEN at_status LIKE "leave_%" THEN 1 END) as leave_count'),
                DB::raw('COUNT(CASE WHEN at_status = "leave_ANNUAL" THEN 1 END) as leave_annual_count'),
                DB::raw('COUNT(CASE WHEN at_status = "leave_SICK" THEN 1 END) as leave_sick_count'),
                DB::raw('COUNT(CASE WHEN at_status = "leave_MATERNITY" THEN 1 END) as leave_maternity_count'),
                DB::raw('COUNT(CASE WHEN at_status = "leave_PATERNITY" THEN 1 END) as leave_paternity_count'),
                DB::raw('COUNT(CASE WHEN at_status = "leave_SPECIAL" THEN 1 END) as leave_special_count')
            ])
            ->whereBetween('at_date', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $totalStats = $query->first();

        // Convert to array format for view
        $stats = [];
        if ($totalStats->present_count > 0) {
            $stats[] = (object)['at_status' => 'present', 'total' => $totalStats->present_count];
        }
        if ($totalStats->absent_count > 0) {
            $stats[] = (object)['at_status' => 'absent', 'total' => $totalStats->absent_count];
        }
        if ($totalStats->late_count > 0) {
            $stats[] = (object)['at_status' => 'late', 'total' => $totalStats->late_count];
        }
        if ($totalStats->scan_once_count > 0) {
            $stats[] = (object)['at_status' => 'scan_once', 'total' => $totalStats->scan_once_count];
        }
        if ($totalStats->early_leave_count > 0) {
            $stats[] = (object)['at_status' => 'early_leave', 'total' => $totalStats->early_leave_count];
        }

        // Add leave stats from attendance table
        if ($totalStats->leave_annual_count > 0) {
            $stats[] = (object)['at_status' => 'leave_ANNUAL', 'total' => $totalStats->leave_annual_count];
        }
        if ($totalStats->leave_sick_count > 0) {
            $stats[] = (object)['at_status' => 'leave_SICK', 'total' => $totalStats->leave_sick_count];
        }
        if ($totalStats->leave_maternity_count > 0) {
            $stats[] = (object)['at_status' => 'leave_MATERNITY', 'total' => $totalStats->leave_maternity_count];
        }
        if ($totalStats->leave_paternity_count > 0) {
            $stats[] = (object)['at_status' => 'leave_PATERNITY', 'total' => $totalStats->leave_paternity_count];
        }
        if ($totalStats->leave_special_count > 0) {
            $stats[] = (object)['at_status' => 'leave_SPECIAL', 'total' => $totalStats->leave_special_count];
        }

        return $stats;
    }

    public function processAttendanceStatus($attendanceId)
    {
        try {
            $attendance = $this->find($attendanceId);
            if (!$attendance) {
                return false;
            }

            // PRIORITAS 0: Jangan ubah status LEAVE yang sudah ada
            if (strpos($attendance->at_status, 'leave_') === 0) {
                \Log::info('Skipping LEAVE status in model - no changes needed', [
                    'attendance_id' => $attendance->id,
                    'current_status' => $attendance->at_status,
                    'reason' => 'Leave status should not be changed'
                ]);
                return true; // Skip processing, return success
            }

            // Check if user has approved leave for this date
            $leaveRequest = DB::table('leave_requests')
                ->join('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
                ->where('leave_requests.user_id', $attendance->user_id)
                ->where('leave_requests.lr_status', 'approved')
                ->where('leave_requests.lr_start_date', '<=', $attendance->at_date)
                ->where('leave_requests.lr_end_date', '>=', $attendance->at_date)
                ->select('leave_types.lt_code', 'leave_types.lt_name', 'leave_types.lt_color')
                ->first();

            if ($leaveRequest) {
                // Update attendance status to leave
                $attendance->at_status = 'leave_' . $leaveRequest->lt_code;
                $attendance->at_notes = 'Cuti: ' . $leaveRequest->lt_name;
                $attendance->save();
                return true;
            }

            // Check daily schedule
            $dailySchedule = DB::table('daily_schedules')
                ->where('user_id', $attendance->user_id)
                ->where('ds_date', $attendance->at_date)
                ->first();

            if ($dailySchedule) {
                // Update daily_schedule_id
                $attendance->daily_schedule_id = $dailySchedule->id;
                
                // If no time recorded, set status based on schedule
                if (!$attendance->at_time_in && !$attendance->at_time_out) {
                    // Tidak ada time in dan time out, kemungkinan besar LEAVE
                    $leaveRequest = DB::table('leave_requests')
                        ->join('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
                        ->where('leave_requests.user_id', $attendance->user_id)
                        ->where('leave_requests.lr_status', 'approved')
                        ->where('leave_requests.lr_start_date', '<=', $attendance->at_date)
                        ->where('leave_requests.lr_end_date', '>=', $attendance->at_date)
                        ->select('leave_types.lt_code', 'leave_types.lt_name')
                        ->first();
                    
                    if ($leaveRequest) {
                        // Ada leave request yang approved
                        $attendance->at_status = 'leave_' . $leaveRequest->lt_code;
                        $attendance->at_notes = 'Cuti: ' . $leaveRequest->lt_name;
                    } else {
                        // Tidak ada leave request, set ke absent
                        $attendance->at_status = 'absent';
                        $attendance->at_notes = 'Tidak hadir sesuai jadwal';
                    }
                } else {
                    // Check if only one time record (scan once)
                    if (!$attendance->at_time_in || !$attendance->at_time_out) {
                        // Scan once case
                        if ($attendance->at_time_in && $dailySchedule->ds_start_time) {
                            $scheduleStart = \Carbon\Carbon::parse($dailySchedule->ds_start_time);
                            $actualStart = \Carbon\Carbon::parse($attendance->at_time_in);
                            
                            if ($actualStart->gt($scheduleStart)) {
                                // Scan once tapi terlambat - PRIORITAS: LATE
                                $attendance->at_status = 'late';
                                $lateMinutes = $actualStart->diffInMinutes($scheduleStart);
                                $attendance->at_notes = "scan once - terlambat {$lateMinutes} menit";
                            } else {
                                // Scan once dan tepat waktu - STATUS: SCAN_ONCE
                                $attendance->at_status = 'scan_once';
                                $attendance->at_notes = 'scan once - hadir tepat waktu';
                            }
                        } elseif ($attendance->at_time_out && $dailySchedule->ds_end_time) {
                            $scheduleEnd = \Carbon\Carbon::parse($dailySchedule->ds_end_time);
                            $actualEnd = \Carbon\Carbon::parse($attendance->at_time_out);
                            
                            if ($actualEnd->lt($scheduleEnd)) {
                                // Scan once tapi pulang awal - PRIORITAS: EARLY_LEAVE
                                $attendance->at_status = 'early_leave';
                                $earlyMinutes = $scheduleEnd->diffInMinutes($actualEnd);
                                $attendance->at_notes = "scan once - pulang awal {$earlyMinutes} menit";
                            } else {
                                // Scan once dan pulang tepat waktu - STATUS: SCAN_ONCE
                                $attendance->at_status = 'scan_once';
                                $attendance->at_notes = 'scan once - pulang tepat waktu';
                            }
                        } else {
                            // Scan once tanpa schedule yang jelas
                            $attendance->at_status = 'scan_once';
                            $attendance->at_notes = 'scan once - incomplete attendance record';
                        }
                    } else {
                        // Both time records exist, check if late based on schedule start time
                        if ($dailySchedule->ds_start_time && $attendance->at_time_in) {
                            $scheduleStart = \Carbon\Carbon::parse($dailySchedule->ds_start_time);
                            $actualStart = \Carbon\Carbon::parse($attendance->at_time_in);
                            
                            if ($actualStart->gt($scheduleStart->addMinutes(15))) {
                                $attendance->at_status = 'late';
                                $attendance->at_notes = 'Terlambat';
                            } else {
                                $attendance->at_status = 'present';
                                $attendance->at_notes = 'Hadir tepat waktu';
                            }
                        }
                    }
                }
                
                $attendance->save();
                return true;
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Error processing attendance status: ' . $e->getMessage());
            return false;
        }
    }
}
