<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserPosition extends Model
{
    protected $table = 'user_positions';
    protected $fillable = [
        'up_code', 'up_name', 'up_description', 'up_level',
        'up_can_approve_leave', 'up_is_active', 'up_color',
        'created_by', 'updated_by'
    ];

    protected $casts = [
        'up_can_approve_leave' => 'boolean',
        'up_is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class, 'up_id');
    }

    // Get active positions
    public function getActivePositions()
    {
        return DB::table($this->table)
            ->where('up_is_active', true)
            ->orderBy('up_level')
            ->orderBy('up_name')
            ->get();
    }

    // Get positions that can approve leave
    public function getApproverPositions()
    {
        return DB::table($this->table)
            ->where('up_is_active', true)
            ->where('up_can_approve_leave', true)
            ->orderBy('up_level')
            ->orderBy('up_name')
            ->get();
    }

    // Check if position can approve leave
    public function canApproveLeave($positionId)
    {
        $position = DB::table($this->table)
            ->where('id', $positionId)
            ->where('up_is_active', true)
            ->where('up_can_approve_leave', true)
            ->first();
        
        return $position ? true : false;
    }

    // Store data
    public function storeData($mode, $id, $data)
    {
        try {
            if ($mode == 'add') {
                $data['created_by'] = auth()->user()->u_name ?? 'system';
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['updated_at'] = date('Y-m-d H:i:s');
                \Log::info('Attempting to insert user position data', $data);
                $insert = DB::table($this->table)->insert($data);
                \Log::info('Insert result', ['result' => $insert]);
                return $insert;
            } elseif ($mode == 'edit') {
                $data['updated_by'] = auth()->user()->u_name ?? 'system';
                $data['updated_at'] = date('Y-m-d H:i:s');
                \Log::info('Attempting to update user position data', ['id' => $id, 'data' => $data]);
                $update = DB::table($this->table)->where('id', $id)->update($data);
                \Log::info('Update result', ['result' => $update]);
                return $update;
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::error('Database error in storeData: ' . $ex->getMessage());
            \Log::error('SQL: ' . $ex->getSql());
            \Log::error('Bindings: ' . json_encode($ex->getBindings()));
            return false;
        } catch (\Exception $ex) {
            \Log::error('General error in storeData: ' . $ex->getMessage());
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
