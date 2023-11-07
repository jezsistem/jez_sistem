<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserActivity extends Model
{
    use HasFactory;
    protected $table = 'user_activities';
    protected $fillable = [
        'user_id',
        'ua_description',
        'created_at',
    ];

    public function getAllJoinData($select)
    {
        $affected = DB::table($this->table)
            ->select($select)
            ->leftJoin('users', 'users.id', '=', 'user_activities.user_id')
            ->orderByDesc('uaid')
            ->limit(2)
            ->get();
        return $affected;
    }

    public function storeData($data)
    {
        $created = [
			'created_at' => date('Y-m-d H:i:s')
		];
        $store = DB::table($this->table)->insert(array_merge($data, $created));
        return $store;
    }
}
