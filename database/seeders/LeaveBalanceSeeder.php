<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all users
        $users = DB::table('users')->where('u_delete', '0')->get();
        
        // Get annual leave type
        $annualLeaveType = DB::table('leave_types')->where('lt_code', 'ANNUAL')->first();
        
        if (!$annualLeaveType) {
            echo "Annual leave type not found. Please run LeaveTypeSeeder first.\n";
            return;
        }

        $currentYear = date('Y');
        
        foreach ($users as $user) {
            // Create leave balance for annual leave (12 days per year)
            DB::table('leave_balances')->insert([
                'user_id' => $user->id,
                'leave_type_id' => $annualLeaveType->id,
                'lb_year' => $currentYear,
                'lb_initial_balance' => 12,
                'lb_used_balance' => 0,
                'lb_remaining_balance' => 12,
                'lb_notes' => 'Initial balance for ' . $currentYear,
                'created_by' => 'system',
                'updated_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        echo "Leave balances seeded successfully for " . count($users) . " users.\n";
    }
}
