<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeMenuSeeder extends Seeder
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

        // Check if User Types menu already exists
        $existingMenu = DB::table('menu_accesses')
            ->where('ma_slug', 'user-types')
            ->first();

        if ($existingMenu) {
            echo "User Types menu already exists.\n";
            return;
        }

        // Get the latest sort order for HR menu
        $latestSort = DB::table('menu_accesses')
            ->where('mt_id', $hrMenuTitle->id)
            ->max('ma_sort');

        // Insert User Types menu with sort order after user-divisions (which should be 8)
        $userTypesMenuId = DB::table('menu_accesses')->insertGetId([
            'mt_id' => $hrMenuTitle->id,
            'ma_title' => 'User Types',
            'ma_slug' => 'user-types',
            'ma_sort' => $latestSort ? $latestSort + 1 : 9, // Position after existing menus
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        echo "User Types menu created with ID: {$userTypesMenuId}\n";

        // Assign menu access to all active users
        $users = DB::table('users')->where('u_delete', '0')->get();
        
        foreach ($users as $user) {
            // Check if user already has access
            $existingAccess = DB::table('user_menu_accesses')
                ->where('u_id', $user->id)
                ->where('ma_id', $userTypesMenuId)
                ->first();

            if (!$existingAccess) {
                DB::table('user_menu_accesses')->insert([
                    'u_id' => $user->id,
                    'ma_id' => $userTypesMenuId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        echo "User Types menu access granted to " . $users->count() . " users.\n";
    }
}
