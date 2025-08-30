<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DailySchedule extends Model
{
    use HasFactory;
    
    protected $table = 'daily_schedules';
    protected $fillable = [
        'user_id',
        'ud_id',
        'sc_id',
        'ds_date',
        'ds_start_time',
        'ds_end_time',
        'ds_status',
        'ds_notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'ds_date' => 'date',
        'ds_start_time' => 'datetime:H:i',
        'ds_end_time' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userDivision()
    {
        return $this->belongsTo(UserDivision::class, 'ud_id');
    }

    public function shiftCode()
    {
        return $this->belongsTo(ShiftCode::class, 'sc_id');
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

    public function getScheduleByDateRange($startDate, $endDate, $userId = null, $divisionId = null, $shiftId = null)
    {
        $query = DB::table($this->table)
            ->select([
                'daily_schedules.*',
                'users.u_name',
                'users.u_nip',
                'user_divisions.ud_name',
                'shift_codes.sc_code',
                'shift_codes.sc_description',
                'shift_codes.sc_shift_name',
                'shift_codes.sc_start_time',
                'shift_codes.sc_end_time',
                'shift_codes.sc_type'
            ])
            ->leftJoin('users', 'users.id', '=', 'daily_schedules.user_id')
            ->leftJoin('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->whereBetween('ds_date', [$startDate, $endDate])
            ->orderBy('ds_date')
            ->orderBy('users.u_name');

        if ($userId) {
            $query->where('daily_schedules.user_id', $userId);
        }

        if ($divisionId) {
            $query->where('users.ud_id', $divisionId);
        }

        if ($shiftId) {
            $query->where('daily_schedules.sc_id', $shiftId);
        }

        return $query->get();
    }

    public function getScheduleByUserAndDate($userId, $date)
    {
        return DB::table($this->table)
            ->select([
                'daily_schedules.*',
                'shift_codes.sc_code',
                'shift_codes.sc_description',
                'shift_codes.sc_shift_name',
                'shift_codes.sc_start_time',
                'shift_codes.sc_end_time',
                'shift_codes.sc_type'
            ])
            ->leftJoin('shift_codes', 'shift_codes.id', '=', 'daily_schedules.sc_id')
            ->where('user_id', $userId)
            ->where('ds_date', $date)
            ->first();
    }

    public function bulkInsertSchedules($schedules)
    {
        return DB::table($this->table)->insert($schedules);
    }

    public function updateScheduleStatus($id, $status)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update([
                'ds_status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }
} 