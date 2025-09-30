<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffMenuSeeder extends Seeder
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

        // Insert Staff Management menu
        $staffMenuId = DB::table('menu_accesses')->insertGetId([
            'mt_id' => $hrMenuTitle->id,
            'ma_title' => 'Staff Management',
            'ma_slug' => 'staff',
            'ma_sort' => 9,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Assign menu access to all users
        $users = DB::table('users')->where('u_delete', '0')->get();
        
        foreach ($users as $user) {
            // Assign Staff Management access
            DB::table('user_menu_accesses')->insert([
                'u_id' => $user->id,
                'ma_id' => $staffMenuId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        echo "Staff Management menu item created and assigned to users successfully.\n";
    }
}
