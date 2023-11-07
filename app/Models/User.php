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
        'name',
        'email',
        'password',
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
        ->leftJoin('user_groups' , 'user_groups.user_id', '=', 'users.id')
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
        $select = array_merge($select, ['stt_name', 'st_name']);
        $affected = DB::table($this->table)
            ->leftJoin('user_groups' , 'user_groups.user_id', '=', 'users.id')
            ->leftJoin('groups', 'groups.id', '=', 'user_groups.group_id')
            ->leftJoin('stores', 'stores.id', '=', 'users.st_id')
            ->leftJoin('store_types', 'store_types.id', '=', 'users.stt_id')
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
            if($ex->getCode() === '23000') {
                return false;
            }
        }
    }
}
