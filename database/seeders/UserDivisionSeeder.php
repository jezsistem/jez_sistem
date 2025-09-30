<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserDivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $divisions = [
            [
                'ud_name' => 'FINANCE & TECH',
                'ud_code' => 'FIN_TECH',
                'ud_description' => 'Divisi Finance dan Technology',
                'ud_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ud_name' => 'GA & CS',
                'ud_code' => 'GA_CS',
                'ud_description' => 'Divisi General Affairs dan Customer Service',
                'ud_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ud_name' => 'HELPER MLG',
                'ud_code' => 'HELPER_MLG',
                'ud_description' => 'Helper Malang',
                'ud_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ud_name' => 'HR MLG & HRGA SBY',
                'ud_code' => 'HR_MLG_HRGA_SBY',
                'ud_description' => 'Human Resource Malang dan HRGA Surabaya',
                'ud_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ud_name' => 'LOG MLG',
                'ud_code' => 'LOG_MLG',
                'ud_description' => 'Logistics Malang',
                'ud_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ud_name' => 'AMP MLG',
                'ud_code' => 'AMP_MLG',
                'ud_description' => 'AMP Malang',
                'ud_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ud_name' => 'AMP SBY',
                'ud_code' => 'AMP_SBY',
                'ud_description' => 'AMP Surabaya',
                'ud_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('user_divisions')->insert($divisions);
    }
} 