<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get HR menu title
        $hrMenuTitle = DB::table('menu_titles')->where('mt_title', 'Human Resource')->first();
        
        if (!$hrMenuTitle) {
            echo "HR menu title not found. Please run HRMenuSeeder first.\n";
            return;
        }

        // Insert Leave Types menu
        $leaveTypesMenuId = DB::table('menu_accesses')->insertGetId([
            'mt_id' => $hrMenuTitle->id,
            'ma_title' => 'Leave Types',
            'ma_slug' => 'leave-types',
            'ma_sort' => 5,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Insert Leave Requests menu
        $leaveRequestsMenuId = DB::table('menu_accesses')->insertGetId([
            'mt_id' => $hrMenuTitle->id,
            'ma_title' => 'Leave Requests',
            'ma_slug' => 'leave-requests',
            'ma_sort' => 6,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Assign menu access to all users
        $users = DB::table('users')->where('u_delete', '0')->get();
        
        foreach ($users as $user) {
            // Assign Leave Types access
            DB::table('user_menu_accesses')->insert([
                'u_id' => $user->id,
                'ma_id' => $leaveTypesMenuId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Assign Leave Requests access
            DB::table('user_menu_accesses')->insert([
                'u_id' => $user->id,
                'ma_id' => $leaveRequestsMenuId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        echo "Leave menu items created and assigned to users successfully.\n";
    }
}
