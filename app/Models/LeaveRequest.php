<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    protected $table = 'leave_requests';
    protected $fillable = [
        'user_id', 'leave_type_id', 'lr_start_date', 'lr_end_date',
        'lr_start_time', 'lr_end_time', 'lr_total_days', 'lr_total_hours',
        'lr_unit', 'lr_reason', 'lr_status', 'lr_admin_notes',
        'lr_approved_by', 'lr_approved_at', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'lr_start_date' => 'date',
        'lr_end_date' => 'date',
        'lr_start_time' => 'datetime',
        'lr_end_time' => 'datetime',
        'lr_approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'lr_approved_by');
    }

    // Get leave requests with filters
    public function getLeaveRequestsByFilters($startDate = null, $endDate = null, $userId = null, $status = null, $leaveTypeId = null)
    {
        $query = DB::table($this->table)
            ->select([
                'leave_requests.*',
                'users.u_name',
                'users.u_nip',
                'user_divisions.ud_name',
                'leave_types.lt_name',
                'leave_types.lt_code',
                'leave_types.lt_color',
                'approvers.u_name as approver_name'
            ])
            ->leftJoin('users', 'users.id', '=', 'leave_requests.user_id')
            ->leftJoin('daily_schedules', function($join) {
                $join->on('daily_schedules.user_id', '=', 'leave_requests.user_id')
                     ->on('daily_schedules.ds_date', '=', 'leave_requests.lr_start_date');
            })
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->leftJoin('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
            ->leftJoin('users as approvers', 'approvers.id', '=', 'leave_requests.lr_approved_by')
            ->orderBy('leave_requests.created_at', 'desc');

        if ($startDate) {
            $query->where('leave_requests.lr_start_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('leave_requests.lr_start_date', '<=', $endDate);
        }

        if ($userId) {
            $query->where('leave_requests.user_id', $userId);
        }

        if ($status) {
            $query->where('leave_requests.lr_status', $status);
        }

        if ($leaveTypeId) {
            $query->where('leave_requests.leave_type_id', $leaveTypeId);
        }

        return $query->get();
    }

    // Calculate total days/hours
    public function calculateTotal()
    {
        $startDate = Carbon::parse($this->lr_start_date);
        $endDate = $this->lr_end_date ? Carbon::parse($this->lr_end_date) : $startDate;
        
        if ($this->lr_unit == 'hours') {
            // Calculate hours
            $startTime = $this->lr_start_time ? Carbon::parse($this->lr_start_time) : Carbon::parse('00:00:00');
            $endTime = $this->lr_end_time ? Carbon::parse($this->lr_end_time) : Carbon::parse('23:59:59');
            
            $totalHours = $startDate->diffInDays($endDate) * 24;
            $totalHours += $startTime->diffInHours($endTime);
            
            return $totalHours;
        } else {
            // Calculate days
            return $startDate->diffInDays($endDate) + 1;
        }
    }

    // Check if user can request leave
    public function canRequestLeave($userId, $startDate, $endDate = null)
    {
        $endDate = $endDate ?: $startDate;
        
        // Check if there's already a leave request for this period
        $existingRequest = DB::table($this->table)
            ->where('user_id', $userId)
            ->where('lr_status', '!=', 'cancelled')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('lr_start_date', [$startDate, $endDate])
                      ->orWhereBetween('lr_end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('lr_start_date', '<=', $startDate)
                            ->where('lr_end_date', '>=', $endDate);
                      });
            })
            ->first();

        return !$existingRequest;
    }

    // Store data
    public function storeData($mode, $id, $data)
    {
        try {
            if ($mode == 'add') {
                $data['created_by'] = auth()->user()->u_name ?? 'system';
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['updated_at'] = date('Y-m-d H:i:s');
                $insert = DB::table($this->table)->insert($data);
                return $insert;
            } elseif ($mode == 'edit') {
                $data['updated_by'] = auth()->user()->u_name ?? 'system';
                $data['updated_at'] = date('Y-m-d H:i:s');
                $update = DB::table($this->table)->where('id', $id)->update($data);
                return $update;
            }
            return false;
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::error('LeaveRequest storeData error', [
                'mode' => $mode,
                'id' => $id,
                'data' => $data,
                'error' => $ex->getMessage()
            ]);
            return false;
        }
    }

    // Delete data
    public function deleteData($id)
    {
        try {
            $delete = DB::table($this->table)->where('id', $id)->delete();
            return $delete;
        } catch (\Illuminate\Database\QueryException $ex) {
            return false;
        }
    }

    // Approve leave request
    public function approveLeaveRequest($id, $adminNotes = null)
    {
        try {
            $data = [
                'lr_status' => 'approved',
                'lr_admin_notes' => $adminNotes,
                'lr_approved_by' => auth()->user()->id,
                'lr_approved_at' => date('Y-m-d H:i:s'),
                'updated_by' => auth()->user()->u_name ?? 'system',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $update = DB::table($this->table)->where('id', $id)->update($data);
            return $update;
        } catch (\Illuminate\Database\QueryException $ex) {
            return false;
        }
    }

    // Reject leave request
    public function rejectLeaveRequest($id, $adminNotes = null)
    {
        try {
            $data = [
                'lr_status' => 'rejected',
                'lr_admin_notes' => $adminNotes,
                'lr_approved_by' => auth()->user()->id,
                'lr_approved_at' => date('Y-m-d H:i:s'),
                'updated_by' => auth()->user()->u_name ?? 'system',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $update = DB::table($this->table)->where('id', $id)->update($data);
            return $update;
        } catch (\Illuminate\Database\QueryException $ex) {
            return false;
        }
    }
}
