<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $positions = [
            [
                'up_code' => 'STAFF',
                'up_name' => 'Staff',
                'up_description' => 'Regular staff member',
                'up_level' => 1,
                'up_can_approve_leave' => false,
                'up_is_active' => true,
                'up_color' => '#3699FF',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'up_code' => 'SUPERVISOR',
                'up_name' => 'Supervisor',
                'up_description' => 'Supervisor level with approval authority',
                'up_level' => 2,
                'up_can_approve_leave' => true,
                'up_is_active' => true,
                'up_color' => '#1BC5BD',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'up_code' => 'MANAGER',
                'up_name' => 'Manager',
                'up_description' => 'Manager level with full authority',
                'up_level' => 3,
                'up_can_approve_leave' => true,
                'up_is_active' => true,
                'up_color' => '#F64E60',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'up_code' => 'DIRECTOR',
                'up_name' => 'Director',
                'up_description' => 'Director level with highest authority',
                'up_level' => 4,
                'up_can_approve_leave' => true,
                'up_is_active' => true,
                'up_color' => '#8950FC',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($positions as $position) {
            DB::table('user_positions')->insert($position);
        }

        echo "User positions seeded successfully.\n";
    }
}
