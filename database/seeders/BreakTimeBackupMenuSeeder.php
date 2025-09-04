<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BreakTimeBackupMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Get all users
        $users = DB::table('users')->where('u_delete', '0')->get();

        // Assign menu access to all users
        foreach ($users as $user) {
            DB::table('user_menu_accesses')->insert([
                'u_id' => $user->id,
                'ma_id' => 201,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        echo "Break Time Backup menu created and assigned to " . count($users) . " users.\n";
    }
} 