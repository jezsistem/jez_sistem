<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LeaveType extends Model
{
    protected $table = 'leave_types';
    protected $fillable = [
        'lt_code', 'lt_name', 'lt_description', 'lt_default_days',
        'lt_default_hours', 'lt_unit', 'lt_requires_approval',
        'lt_is_active', 'lt_color', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'lt_requires_approval' => 'boolean',
        'lt_is_active' => 'boolean',
    ];

    // Relationships
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }

    // Get active leave types
    public function getActiveLeaveTypes()
    {
        return DB::table($this->table)
            ->where('lt_is_active', true)
            ->orderBy('lt_name')
            ->get();
    }

    // Check if leave type exists
    public function checkData($select, $where)
    {
        $data = DB::table($this->table)->select($select)->where($where)->first();
        return $data;
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
        } catch (\Illuminate\Database\QueryException $ex) {
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
}
