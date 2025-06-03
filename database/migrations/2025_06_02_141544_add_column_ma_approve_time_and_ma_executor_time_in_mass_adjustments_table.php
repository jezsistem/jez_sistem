<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnMaApproveTimeAndMaExecutorTimeInMassAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mass_adjustments', function (Blueprint $table) {
            $table->timestamp('ma_approve_time')->nullable()->after('ma_status');
            $table->timestamp('ma_executor_time')->nullable()->after('ma_approve_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mass_adjustments', function (Blueprint $table) {
            $table->dropColumn(['ma_approve_time', 'ma_executor_time']);
        });
    }
}
