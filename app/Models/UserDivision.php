<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserDivision extends Model
{
    use HasFactory;
    
    // Use user_divisions (which is now a view from store_types)
    protected $table = 'user_divisions';
    
    protected $fillable = [
        'ud_name', 'ud_description', 'ud_status', 
        'created_by', 'updated_by', 'created_at', 'updated_at', 'lead_id', 'manager_id', 'division_type'
    ];
    
    // Store data method for compatibility - but write to store_types
    public function storeData($mode, $id, $data)
    {
        try {
            // Map ud_ fields to stt_ fields for store_types table
            $mappedData = [];
            if (isset($data['ud_name'])) {
                $mappedData['stt_name'] = $data['ud_name'];
            }
            if (isset($data['ud_description'])) {
                $mappedData['stt_description'] = $data['ud_description'];
            }
            if (isset($data['lead_id'])) {
                $mappedData['lead_id'] = $data['lead_id'];
            }
            if (isset($data['manager_id'])) {
                $mappedData['manager_id'] = $data['manager_id'];
            }
            if (isset($data['ud_description'])) {
                $mappedData['stt_description'] = $data['ud_description'];
            }
            if (isset($data['ud_status'])) {
                $mappedData['stt_delete'] = $data['ud_status'] == 'active' ? '0' : '1';
            }

            if (isset($data['division_type'])) {
                $mappedData['division_type'] = $data['division_type'];
            }
            
            // Add audit fields
            if ($mode == 'add') {
                $mappedData['created_by'] = auth()->user()->u_name ?? 'system';
                $mappedData['created_at'] = date('Y-m-d H:i:s');
                $mappedData['updated_at'] = date('Y-m-d H:i:s');
                $result = DB::table('store_types')->insert($mappedData);
            } elseif ($mode == 'edit') {
                $mappedData['updated_by'] = auth()->user()->u_name ?? 'system';
                $mappedData['updated_at'] = date('Y-m-d H:i:s');
                $result = DB::table('store_types')->where('id', $id)->update($mappedData);
            }
            
            return $result;
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::error('UserDivision storeData error', [
                'mode' => $mode,
                'id' => $id,
                'data' => $data,
                'error' => $ex->getMessage()
            ]);
            return false;
        }
    }
    
    // Delete data method - delete from store_types
    public function deleteData($id)
    {
        try {
            // Soft delete - set stt_delete to '1' in store_types
            $result = DB::table('store_types')
                ->where('id', $id)
                ->update([
                    'stt_delete' => '1',
                    'updated_by' => auth()->user()->u_name ?? 'system',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            return $result;
        } catch (\Illuminate\Database\QueryException $ex) {
            \Log::error('UserDivision deleteData error', [
                'id' => $id,
                'error' => $ex->getMessage()
            ]);
            return false;
        }
    }
    
    // Relationship with users
    public function users()
    {
        return $this->hasMany(User::class, 'ud_id');
    }
}
