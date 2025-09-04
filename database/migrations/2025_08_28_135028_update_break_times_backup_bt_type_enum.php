<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update existing empty bt_type records to 'break_1'
        DB::table('break_times_backup')
            ->whereNull('bt_type')
            ->orWhere('bt_type', '')
            ->update(['bt_type' => 'break_1']);

        // Then modify the enum to include break_3
        DB::statement("ALTER TABLE ts_break_times_backup MODIFY COLUMN bt_type ENUM('break_1', 'break_2', 'break_3') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        DB::statement("ALTER TABLE break_times_backup MODIFY COLUMN bt_type ENUM('break_1', 'break_2') NOT NULL");
    }
};
