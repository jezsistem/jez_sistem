<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HRMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the maximum sort order for menu titles
        $maxMtSort = DB::table('menu_titles')->max('mt_sort') ?? 0;
        
        // Insert HR Management menu title
        $hrMenuTitleId = DB::table('menu_titles')->insertGetId([
            'mt_title' => 'Human Resource',
            'mt_sort' => $maxMtSort + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get the maximum sort order for menu accesses
        $maxMaSort = DB::table('menu_accesses')->max('ma_sort') ?? 0;

        // Insert menu accesses for HR Management
        $menuAccesses = [
            [
                'mt_id' => $hrMenuTitleId,
                'ma_title' => 'Shift Codes',
                'ma_slug' => 'shift-codes',
                'ma_sort' => $maxMaSort + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mt_id' => $hrMenuTitleId,
                'ma_title' => 'Daily Schedules',
                'ma_slug' => 'daily-schedules',
                'ma_sort' => $maxMaSort + 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $menuAccessIds = [];
        foreach ($menuAccesses as $menuAccess) {
            $menuAccessIds[] = DB::table('menu_accesses')->insertGetId($menuAccess);
        }

        // Get all users to give them access to HR Management menus
        $users = DB::table('users')->where('u_delete', '0')->get();
        
        // Insert user menu access for all users
        foreach ($users as $user) {
            foreach ($menuAccessIds as $menuAccessId) {
                DB::table('user_menu_accesses')->insert([
                    'u_id' => $user->id,
                    'ma_id' => $menuAccessId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('HR Management menu has been added successfully!');
        $this->command->info('Menu Title ID: ' . $hrMenuTitleId);
        $this->command->info('Menu Access IDs: ' . implode(', ', $menuAccessIds));
    }
} 