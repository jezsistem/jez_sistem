<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AnnouncementMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find Human Resource menu title
        $hrMenuTitle = \DB::table('menu_titles')->where('mt_title', 'Human Resource')->first();
        
        if (!$hrMenuTitle) {
            $this->command->error('Human Resource menu title not found!');
            return;
        }

        // Check if Announcement menu already exists
        $existingMenu = \DB::table('menu_accesses')->where('ma_slug', 'announcements')->first();
        if ($existingMenu) {
            $this->command->info('Announcement menu already exists.');
            return;
        }

        // Get the latest sort number for HR menu
        $latestSort = \DB::table('menu_accesses')
            ->where('mt_id', $hrMenuTitle->id)
            ->max('ma_sort');

        // Insert main Announcement menu
        $announcementMenuId = \DB::table('menu_accesses')->insertGetId([
            'mt_id' => $hrMenuTitle->id,
            'ma_title' => 'Announcement',
            'ma_slug' => 'announcements',
            'ma_sort' => $latestSort ? $latestSort + 1 : 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Category sub-menu
        $categoryMenuId = \DB::table('menu_accesses')->insertGetId([
            'mt_id' => $hrMenuTitle->id,
            'ma_title' => 'Category',
            'ma_slug' => 'announcement-categories',
            'ma_sort' => $latestSort ? $latestSort + 2 : 11,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Reaction sub-menu
        $reactionMenuId = \DB::table('menu_accesses')->insertGetId([
            'mt_id' => $hrMenuTitle->id,
            'ma_title' => 'Reaction',
            'ma_slug' => 'announcement-reactions',
            'ma_sort' => $latestSort ? $latestSort + 3 : 12,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Grant access to all active users
        $users = \DB::table('users')->where('u_delete', '0')->get();
        
        foreach ($users as $user) {
            // Check if user already has access to announcement menu
            $existingAccess = \DB::table('user_menu_accesses')
                ->where('u_id', $user->id)
                ->where('ma_id', $announcementMenuId)
                ->first();
                
            if (!$existingAccess) {
                \DB::table('user_menu_accesses')->insert([
                    'u_id' => $user->id,
                    'ma_id' => $announcementMenuId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Grant access to category menu
            $existingCategoryAccess = \DB::table('user_menu_accesses')
                ->where('u_id', $user->id)
                ->where('ma_id', $categoryMenuId)
                ->first();
                
            if (!$existingCategoryAccess) {
                \DB::table('user_menu_accesses')->insert([
                    'u_id' => $user->id,
                    'ma_id' => $categoryMenuId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Grant access to reaction menu
            $existingReactionAccess = \DB::table('user_menu_accesses')
                ->where('u_id', $user->id)
                ->where('ma_id', $reactionMenuId)
                ->first();
                
            if (!$existingReactionAccess) {
                \DB::table('user_menu_accesses')->insert([
                    'u_id' => $user->id,
                    'ma_id' => $reactionMenuId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Announcement menu and access permissions created successfully.');
    }
}
