<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddLeaveStatusToAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, we need to drop the existing enum constraint
        // Since MySQL doesn't support direct enum modification, we'll recreate the column
        
        // Get current enum values
        $enumValues = DB::select("SHOW COLUMNS FROM ts_attendance WHERE Field = 'at_status'")[0]->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $enumValues, $matches);
        $currentValues = explode("','", $matches[1]);
        
        // Add new leave status values
        $newValues = array_merge($currentValues, [
            'leave_SICK',
            'leave_ANNUAL', 
            'leave_MATERNITY',
            'leave_EMERGENCY',
            'leave_HALF_DAY',
            'leave_SPECIAL'
        ]);
        
        // Remove duplicates and sort
        $newValues = array_unique($newValues);
        sort($newValues);
        
        // Create new enum string
        $enumString = "'" . implode("','", $newValues) . "'";
        
        // Update the column
        DB::statement("ALTER TABLE ts_attendance MODIFY COLUMN at_status ENUM($enumString) DEFAULT 'present'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE ts_attendance MODIFY COLUMN at_status ENUM('present', 'late', 'absent', 'early_leave', 'scan_once') DEFAULT 'present'");
    }
}
