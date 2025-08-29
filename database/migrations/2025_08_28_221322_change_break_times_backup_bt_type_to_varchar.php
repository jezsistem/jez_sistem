<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBreakTimesBackupBtTypeToVarchar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change bt_type from ENUM to VARCHAR(20)
        DB::statement("ALTER TABLE ts_break_times_backup MODIFY COLUMN bt_type VARCHAR(20) NOT NULL");
        
        // Update existing empty bt_type records to 'break_1'
        DB::table('break_times_backup')
            ->whereNull('bt_type')
            ->orWhere('bt_type', '')
            ->update(['bt_type' => 'break_1']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to ENUM
        DB::statement("ALTER TABLE ts_break_times_backup MODIFY COLUMN bt_type ENUM('break_1', 'break_2', 'break_3') NOT NULL");
    }
}
