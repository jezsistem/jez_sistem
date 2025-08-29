<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserType extends Model
{
    use HasFactory;
    
    protected $table = 'user_types';
    protected $fillable = [
        'ut_name',
        'ut_code',
        'ut_description',
        'ut_status',
        'created_by',
        'updated_by'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'ut_id');
    }

    /**
     * Get the shift codes that are compatible with this user type
     */
    public function shiftCodes()
    {
        return $this->belongsToMany(ShiftCode::class, 'shift_code_user_types', 'user_type_id', 'shift_code_id');
    }

    /**
     * Get all compatible shift codes for this user type
     */
    public function getCompatibleShiftCodes()
    {
        return $this->shiftCodes()
            ->where('sc_status', 'active')
            ->orderBy('sc_code')
            ->get();
    }

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

    public function getActiveUserTypes()
    {
        return DB::table($this->table)
            ->where('ut_status', 'active')
            ->orderBy('ut_name')
            ->get();
    }
}
