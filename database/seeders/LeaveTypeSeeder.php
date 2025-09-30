<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leaveTypes = [
            [
                'lt_code' => 'ANNUAL',
                'lt_name' => 'Cuti Tahunan',
                'lt_description' => 'Cuti tahunan karyawan sesuai ketentuan perusahaan',
                'lt_default_days' => 12,
                'lt_default_hours' => 0,
                'lt_unit' => 'days',
                'lt_requires_approval' => true,
                'lt_is_active' => true,
                'lt_color' => '#3699FF',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lt_code' => 'SICK',
                'lt_name' => 'Cuti Sakit',
                'lt_description' => 'Cuti karena sakit dengan surat dokter',
                'lt_default_days' => 0,
                'lt_default_hours' => 0,
                'lt_unit' => 'days',
                'lt_requires_approval' => true,
                'lt_is_active' => true,
                'lt_color' => '#F64E60',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lt_code' => 'MATERNITY',
                'lt_name' => 'Cuti Melahirkan',
                'lt_description' => 'Cuti melahirkan sesuai ketentuan hukum',
                'lt_default_days' => 90,
                'lt_default_hours' => 0,
                'lt_unit' => 'days',
                'lt_requires_approval' => true,
                'lt_is_active' => true,
                'lt_color' => '#FFA800',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lt_code' => 'EMERGENCY',
                'lt_name' => 'Cuti Darurat',
                'lt_description' => 'Cuti karena keadaan darurat/keluarga',
                'lt_default_days' => 0,
                'lt_default_hours' => 0,
                'lt_unit' => 'days',
                'lt_requires_approval' => true,
                'lt_is_active' => true,
                'lt_color' => '#8950FC',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lt_code' => 'HALF_DAY',
                'lt_name' => 'Setengah Hari',
                'lt_description' => 'Cuti setengah hari (pagi/siang)',
                'lt_default_days' => 0,
                'lt_default_hours' => 4,
                'lt_unit' => 'hours',
                'lt_requires_approval' => true,
                'lt_is_active' => true,
                'lt_color' => '#1BC5BD',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'lt_code' => 'SPECIAL',
                'lt_name' => 'Cuti Khusus',
                'lt_description' => 'Cuti khusus dengan alasan tertentu',
                'lt_default_days' => 0,
                'lt_default_hours' => 0,
                'lt_unit' => 'days',
                'lt_requires_approval' => true,
                'lt_is_active' => true,
                'lt_color' => '#E4E6EF',
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            DB::table('leave_types')->insert($leaveType);
        }

        echo "Leave types seeded successfully.\n";
    }
}
