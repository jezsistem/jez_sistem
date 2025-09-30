<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LeaveBalance extends Model
{
    protected $table = 'leave_balances';
    protected $fillable = [
        'user_id', 'leave_type_id', 'lb_year', 'lb_initial_balance',
        'lb_used_balance', 'lb_remaining_balance', 'lb_notes',
        'created_by', 'updated_by'
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

    // Get leave balance for user and leave type
    public function getLeaveBalance($userId, $leaveTypeId, $year = null)
    {
        $year = $year ?: date('Y');
        
        return DB::table($this->table)
            ->where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('lb_year', $year)
            ->first();
    }

    // Create or update leave balance
    public function updateLeaveBalance($userId, $leaveTypeId, $year, $initialBalance, $usedBalance = 0)
    {
        $remainingBalance = $initialBalance - $usedBalance;
        
        $data = [
            'user_id' => $userId,
            'leave_type_id' => $leaveTypeId,
            'lb_year' => $year,
            'lb_initial_balance' => $initialBalance,
            'lb_used_balance' => $usedBalance,
            'lb_remaining_balance' => $remainingBalance,
            'created_by' => auth()->user()->u_name ?? 'system',
            'updated_by' => auth()->user()->u_name ?? 'system',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return DB::table($this->table)->updateOrInsert(
            ['user_id' => $userId, 'leave_type_id' => $leaveTypeId, 'lb_year' => $year],
            $data
        );
    }

    // Use leave balance (when leave request is approved)
    public function useLeaveBalance($userId, $leaveTypeId, $year, $days)
    {
        $balance = $this->getLeaveBalance($userId, $leaveTypeId, $year);
        
        if (!$balance) {
            return false; // No balance found
        }

        if ($balance->lb_remaining_balance < $days) {
            return false; // Insufficient balance
        }

        $newUsedBalance = $balance->lb_used_balance + $days;
        $newRemainingBalance = $balance->lb_remaining_balance - $days;

        return DB::table($this->table)
            ->where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('lb_year', $year)
            ->update([
                'lb_used_balance' => $newUsedBalance,
                'lb_remaining_balance' => $newRemainingBalance,
                'updated_by' => auth()->user()->u_name ?? 'system',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    // Get all leave balances for user
    public function getUserLeaveBalances($userId, $year = null)
    {
        $year = $year ?: date('Y');
        
        return DB::table($this->table)
            ->select([
                'leave_balances.*',
                'leave_types.lt_name',
                'leave_types.lt_code',
                'leave_types.lt_color'
            ])
            ->join('leave_types', 'leave_types.id', '=', 'leave_balances.leave_type_id')
            ->where('leave_balances.user_id', $userId)
            ->where('leave_balances.lb_year', $year)
            ->get();
    }

    // Check if user has sufficient leave balance
    public function hasSufficientBalance($userId, $leaveTypeId, $days, $year = null)
    {
        $year = $year ?: date('Y');
        $balance = $this->getLeaveBalance($userId, $leaveTypeId, $year);
        
        if (!$balance) {
            return false;
        }

        return $balance->lb_remaining_balance >= $days;
    }
}
