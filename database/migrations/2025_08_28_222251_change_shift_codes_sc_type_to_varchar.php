<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeShiftCodesScTypeToVarchar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change sc_type from ENUM to VARCHAR(50)
        DB::statement("ALTER TABLE ts_shift_codes MODIFY COLUMN sc_type VARCHAR(50) NOT NULL");
        
        // Verify data consistency after change
        $validTypes = ['FULL TIME', 'PART TIME', 'PART FULL', 'CASUAL', 'ALL'];
        
        // Update any invalid data to 'FULL TIME' as default
        DB::table('shift_codes')
            ->whereNotIn('sc_type', $validTypes)
            ->update(['sc_type' => 'FULL TIME']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to ENUM
        DB::statement("ALTER TABLE ts_shift_codes MODIFY COLUMN sc_type ENUM('FULL TIME','PART TIME','PART FULL','CASUAL','ALL') NOT NULL");
    }
}
