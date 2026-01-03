<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'google_id',
        'st_id',
        'stt_id',
        'u_id_responsibility',
        'u_nip',
        'u_ktp',
        'u_secret_code',
        'u_name',
        'u_email',
        'avatar',
        'provider',
        'google_linked',
        'google_linked_at',
        'u_phone',
        'u_disc',
        'u_address',
        'u_active',
        'join_date',
        'u_delete',
        'up_id',
        'ud_id',
        'ut_id',
        'u_photo',
        'delete_access',
        'pos_access',
        'pick_access',
        'manual_attendance_access',
        'created_by',
        'updated_by',
        'u_ktp_image',
        'u_npwp',
        'u_npwp_image',
        'u_birthday',
        'u_bpjs_kes_number',
        'u_bpjs_kes_image',
        'u_bpjs_tk_number',
        'u_bpjs_tk_image',
        'u_bank_name',
        'u_bank_account_number',
        'u_bank_account_holder',
        'contract_number',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles($id)
    {
        $roles = DB::table($this->table)
            ->select('groups.id', 'groups.g_name')
            ->leftJoin('user_groups', 'user_groups.user_id', '=', 'users.id')
            ->leftJoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->where('user_groups.user_id', $id)
            ->get()->first();
        return $roles;
    }

    public function checkData($select, $where)
    {
        $affected = DB::table($this->table)
            ->select($select)
            ->where($where)
            ->get()->first();
        return $affected;
    }

    public function checkJoinData($select, $where)
    {
        $select = array_merge($select, ['stt_name', 'st_name','leave_balances.lb_remaining_balance as leave_balance']);
        $affected = DB::table($this->table)
            ->leftJoin('user_groups', 'user_groups.user_id', '=', 'users.id')
            ->leftJoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'users.stt_id')
            ->leftJoin('leave_balances', 'leave_balances.user_id', '=', 'users.id')
            ->select($select)
            ->where($where)
            ->get();
        return $affected;
    }

    public function storeData($mode, $id, $data, $group, $password, $created, $updated)
    {
        $hash_password = Hash::make($password);
        $new_password = [
            'password' => $hash_password
        ];
        $group_id = [
            'group_id' => $group
        ];
        if ($mode == 'edit') {
            if (empty($password)) {
                $user = DB::table($this->table)->where('id', $id)->update(array_merge($data, $updated));
            } else {
                $user = DB::table($this->table)->where('id', $id)->update(array_merge($data, $new_password, $updated));
            }
            $group = DB::table('user_groups')->where('user_id', $id)->update(array_merge($group_id, $updated));
        } else {
            $user = DB::table($this->table)->insertGetId(array_merge($data, $new_password, $created, $updated));
            $group = DB::table('user_groups')->insert(array_merge(['user_id' => $user], $group_id, $created, $updated));
        }
        return $group;
    }

    public function storePassword($id, $data)
    {
        $user = DB::table($this->table)->where('id', $id)->update($data);
        return $user;
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
            if ($ex->getCode() === '23000') {
                return false;
            }
        }
    }

    public static function isAdmin($u_id)
    {
        $adminGroupId = DB::table('groups')
            ->where('g_name', 'administrator')
            ->value('id');

        if (!$adminGroupId) {
            return false;
        }

        return DB::table('user_groups')
            ->where('user_id', $u_id)
            ->where('group_id', $adminGroupId)
            ->exists();
    }

    public function userDivision()
    {
        return $this->belongsTo(UserDivision::class, 'ud_id');
    }

    public function userPosition()
    {
        return $this->belongsTo(UserPosition::class, 'up_id');
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class, 'ut_id');
    }

    public function dailySchedules()
    {
        return $this->hasMany(DailySchedule::class, 'user_id');
    }

    public function division()
    {
        return $this->belongsTo(UserDivision::class, 'ud_id');
    }

    /**
     * Google OAuth Helper Methods
     */

    /**
     * Check if user has Google account linked
     */
    public function hasGoogleAccount()
    {
        return !empty($this->google_id);
    }

    /**
     * Check if user has local password
     */
    public function hasLocalPassword()
    {
        return !empty($this->password) && $this->password !== '';
    }

    /**
     * Check if user can use Google login
     */
    public function canUseGoogleLogin()
    {
        return $this->hasGoogleAccount();
    }

    /**
     * Check if user can use password login
     */
    public function canUsePasswordLogin()
    {
        return $this->hasLocalPassword();
    }

    /**
     * Get user's avatar (Google or local)
     */
    public function getAvatar()
    {
        return $this->avatar ?: $this->u_photo;
    }

    /**
     * Get user's display name
     */
    public function getDisplayName()
    {
        return $this->u_name ?: $this->name;
    }

    public function remainingLeaveBalance() {
        return $this->hasOne(LeaveBalance::class, 'user_id')->select('user_id', 'lb_remaining_balance')->latest('updated_at');
    }
}
