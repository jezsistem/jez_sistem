<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get HR menu title ID
        $hrMenuTitle = DB::table('menu_titles')->where('mt_title', 'Human Resource')->first();
        
        if (!$hrMenuTitle) {
            $this->command->error('Menu title "Human Resource" not found. Please run HRMenuSeeder first.');
            return;
        }

        // Insert menu access for Attendance
        $attendanceMenuId = DB::table('menu_accesses')->insertGetId([
            'mt_id' => $hrMenuTitle->id,
            'ma_title' => 'Attendance',
            'ma_slug' => 'attendance',
            'ma_sort' => 3, // After Shift Codes (1) and Daily Schedules (2)
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Get all users
        $users = DB::table('users')->where('u_delete', '0')->get();

        // Assign menu access to all users
        foreach ($users as $user) {
            DB::table('user_menu_accesses')->insert([
                'u_id' => $user->id,
                'ma_id' => $attendanceMenuId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->command->info('Attendance menu has been added to Human Resource section.');
    }
}
