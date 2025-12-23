<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelReasonToMassAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mass_adjustments', function (Blueprint $table) {
            $table->text('cancel_reason')->nullable()->after('ma_status');
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
            $table->dropColumn('cancel_reason');
        });
    }
}
