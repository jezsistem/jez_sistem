<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AnnouncementReactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reactions = [
            [
                'name' => 'Planned',
                'emoji' => '📅',
                'color' => '#6c757d',
                'hide_announcement' => false,
                'sort_order' => 1
            ],
            [
                'name' => 'In Progress',
                'emoji' => '⏳',
                'color' => '#ffc107',
                'hide_announcement' => false,
                'sort_order' => 2
            ],
            [
                'name' => 'Cancel',
                'emoji' => '❌',
                'color' => '#dc3545',
                'hide_announcement' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Done',
                'emoji' => '✅',
                'color' => '#28a745',
                'hide_announcement' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'OK',
                'emoji' => '👍',
                'color' => '#007bff',
                'hide_announcement' => true,
                'sort_order' => 5
            ]
        ];

        foreach ($reactions as $reaction) {
            \DB::table('announcement_reactions')->updateOrInsert(
                ['name' => $reaction['name']],
                array_merge($reaction, [
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        $this->command->info('Announcement reactions seeded successfully.');
    }
}
