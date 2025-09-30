<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        
        $userTypes = [
            [
                'ut_name' => 'FULL TIME',
                'ut_code' => 'FT',
                'ut_description' => 'Full Time Employee - Karyawan penuh waktu',
                'ut_status' => 'active',
                'created_by' => '1',
                'updated_by' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ut_name' => 'PART TIME',
                'ut_code' => 'PT',
                'ut_description' => 'Part Time Employee - Karyawan paruh waktu',
                'ut_status' => 'active',
                'created_by' => '1',
                'updated_by' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'ut_name' => 'PART FULL',
                'ut_code' => 'PF',
                'ut_description' => 'Part Full Employee - Karyawan part full',
                'ut_status' => 'active',
                'created_by' => '1',
                'updated_by' => '1',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('user_types')->insert($userTypes);
    }
}
