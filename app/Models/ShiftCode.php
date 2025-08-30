<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShiftCode extends Model
{
    use HasFactory;
    
    protected $table = 'shift_codes';
    protected $fillable = [
        'sc_code',
        'sc_description',
        'sc_shift_name',
        'sc_start_time',
        'sc_end_time',
        'sc_type',
        'sc_status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'sc_start_time' => 'datetime:H:i',
        'sc_end_time' => 'datetime:H:i',
    ];

    /**
     * Get the user types that can use this shift code
     */
    public function userTypes()
    {
        return $this->belongsToMany(UserType::class, 'shift_code_user_types', 'shift_code_id', 'user_type_id');
    }

    /**
     * Check if this shift code is compatible with a specific user type
     */
    public function isCompatibleWithUserType($userTypeName)
    {
        return $this->userTypes()->where('ut_name', $userTypeName)->exists();
    }

    /**
     * Check if this shift code is compatible with a specific user type (by ID)
     */
    public function isCompatibleWithUserTypeId($userTypeId)
    {
        return $this->userTypes()->where('id', $userTypeId)->exists();
    }

    /**
     * Get all compatible user type names for this shift code
     */
    public function getCompatibleUserTypeNames()
    {
        return $this->userTypes()->pluck('ut_name')->toArray();
    }

    /**
     * Get all compatible user type IDs for this shift code
     */
    public function getCompatibleUserTypeIds()
    {
        return $this->userTypes()->pluck('id')->toArray();
    }

    public function dailySchedules()
    {
        return $this->hasMany(DailySchedule::class, 'sc_id');
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

    public function getActiveShiftCodes()
    {
        return DB::table($this->table)
            ->where('sc_status', 'active')
            ->orderBy('sc_type')
            ->orderBy('sc_code')
            ->get();
    }

    /**
     * Get shift codes by user type (new method)
     */
    public function getShiftCodesByUserType($userTypeName)
    {
        return DB::table($this->table)
            ->join('shift_code_user_types', 'shift_codes.id', '=', 'shift_code_user_types.shift_code_id')
            ->join('user_types', 'shift_code_user_types.user_type_id', '=', 'user_types.id')
            ->where('user_types.ut_name', $userTypeName)
            ->where('shift_codes.sc_status', 'active')
            ->select('shift_codes.*')
            ->orderBy('shift_codes.sc_code')
            ->get();
    }

    /**
     * Get shift codes by user type ID (new method)
     */
    public function getShiftCodesByUserTypeId($userTypeId)
    {
        return DB::table($this->table)
            ->join('shift_code_user_types', 'shift_codes.id', '=', 'shift_code_user_types.shift_code_id')
            ->where('shift_code_user_types.user_type_id', $userTypeId)
            ->where('shift_codes.sc_status', 'active')
            ->select('shift_codes.*')
            ->orderBy('shift_codes.sc_code')
            ->get();
    }

    /**
     * Legacy method for backward compatibility
     */
    public function getShiftCodesByType($type)
    {
        // First try to get from new relationship
        if (Schema::hasTable('shift_code_user_types')) {
            return $this->getShiftCodesByUserType($type);
        }
        
        // Fallback to old method
        return DB::table($this->table)
            ->where('sc_type', $type)
            ->where('sc_status', 'active')
            ->orderBy('sc_code')
            ->get();
    }

    /**
     * Get primary user type for backward compatibility
     * Returns the first user type name if multiple, or the sc_type if legacy
     */
    public function getPrimaryUserType()
    {
        // If this shift code has associated user types through pivot table
        $userTypes = $this->userTypes;
        if ($userTypes->isNotEmpty()) {
            return $userTypes->first()->ut_name;
        }
        
        // Fallback to legacy sc_type
        return $this->sc_type;
    }

    /**
     * Check if shift code is compatible with user type (backward compatible)
     */
    public function isCompatibleWithUserTypeLegacy($userTypeName)
    {
        // First check new pivot table relationship
        if ($this->relationLoaded('userTypes') || Schema::hasTable('shift_code_user_types')) {
            return $this->isCompatibleWithUserType($userTypeName);
        }
        
        // Fallback to legacy sc_type check
        return $this->sc_type === 'ALL' || $this->sc_type === $userTypeName;
    }

    /**
     * Get all shift codes compatible with a specific user type (static method)
     */
    public static function getCompatibleShiftCodes($userTypeName)
    {
        if (Schema::hasTable('shift_code_user_types')) {
            return self::whereHas('userTypes', function($query) use ($userTypeName) {
                $query->where('ut_name', $userTypeName);
            })
            ->where('sc_status', 'active')
            ->orderBy('sc_code')
            ->get();
        }
        
        // Fallback to legacy method
        return self::whereIn('sc_type', ['ALL', $userTypeName])
            ->where('sc_status', 'active')
            ->orderBy('sc_code')
            ->get();
    }

    /**
     * Get break allowance primary user type for this shift code
     */
    public function getBreakAllowancePrimaryType()
    {
        // Get the first/primary user type for this shift code
        $primaryType = $this->getPrimaryUserType();
        
        // If no specific type, default to the sc_type or a sensible default
        if (!$primaryType || $primaryType === 'MULTIPLE') {
            // Get first user type from relationship
            $userTypes = $this->userTypes;
            if ($userTypes->isNotEmpty()) {
                return $userTypes->first()->ut_name;
            }
            
            // Ultimate fallback
            return 'PART TIME';
        }
        
        return $primaryType;
    }
} 