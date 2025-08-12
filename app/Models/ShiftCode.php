<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function getShiftCodesByType($type)
    {
        return DB::table($this->table)
            ->where('sc_type', $type)
            ->where('sc_status', 'active')
            ->orderBy('sc_code')
            ->get();
    }
} 