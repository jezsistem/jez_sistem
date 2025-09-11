<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBaTypeToBinAdjustmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bin_adjustments', function (Blueprint $table) {
                $table->string('ba_type')->nullable()->after('ba_note');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bin_adjustments', function (Blueprint $table) {
            $table->dropColumn(['ba_type']);
        });
    }
}
