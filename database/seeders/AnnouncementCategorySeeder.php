<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AnnouncementCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'General',
                'description' => 'General announcements for all staff',
                'color' => '#007bff'
            ],
            [
                'name' => 'New Hire',
                'description' => 'Announcements about new team members',
                'color' => '#28a745'
            ],
            [
                'name' => 'SOP Updates',
                'description' => 'Standard Operating Procedure updates',
                'color' => '#ffc107'
            ],
            [
                'name' => 'Policy Updates',
                'description' => 'Company policy changes and updates',
                'color' => '#dc3545'
            ],
            [
                'name' => 'Promotion',
                'description' => 'Staff promotions and achievements',
                'color' => '#6f42c1'
            ],
            [
                'name' => 'Transfer',
                'description' => 'Staff transfers and relocations',
                'color' => '#fd7e14'
            ],
            [
                'name' => 'Training',
                'description' => 'Training programs and educational announcements',
                'color' => '#20c997'
            ],
            [
                'name' => 'Special',
                'description' => 'Special events and important notices',
                'color' => '#e83e8c'
            ]
        ];

        foreach ($categories as $category) {
            \DB::table('announcement_categories')->updateOrInsert(
                ['name' => $category['name']],
                array_merge($category, [
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        $this->command->info('Announcement categories seeded successfully.');
    }
}
