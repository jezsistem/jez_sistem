<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserPositionMenuSeeder extends Seeder
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

        // Insert User Positions menu
        $userPositionsMenuId = DB::table('menu_accesses')->insertGetId([
            'mt_id' => $hrMenuTitle->id,
            'ma_title' => 'User Positions',
            'ma_slug' => 'user-positions',
            'ma_sort' => 7,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Insert User Divisions menu
        $userDivisionsMenuId = DB::table('menu_accesses')->insertGetId([
            'mt_id' => $hrMenuTitle->id,
            'ma_title' => 'User Divisions',
            'ma_slug' => 'user-divisions',
            'ma_sort' => 8,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Assign menu access to all users
        $users = DB::table('users')->where('u_delete', '0')->get();
        
        foreach ($users as $user) {
            // Assign User Positions access
            DB::table('user_menu_accesses')->insert([
                'u_id' => $user->id,
                'ma_id' => $userPositionsMenuId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Assign User Divisions access
            DB::table('user_menu_accesses')->insert([
                'u_id' => $user->id,
                'ma_id' => $userDivisionsMenuId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        echo "User Positions and User Divisions menu items created and assigned to users successfully.\n";
    }
}
