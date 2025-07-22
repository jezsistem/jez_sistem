<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBaApproveBaExecutorBaStatusToBinAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bin_adjustments', function (Blueprint $table) {
            $table->unsignedBigInteger('ba_approve')->after('u_id')->nullable();
            $table->unsignedBigInteger('ba_executor')->after('ba_approve')->nullable();
            $table->tinyInteger('ba_status')->after('ba_note')->default(0);
            $table->dateTime('approved_at')->nullable()->after('ba_status');
            $table->dateTime('execute_at')->nullable()->after('approved_at');

            $table->foreign('ba_approve')->references('id')->on('users')->onDelete('set null');
            $table->foreign('ba_executor')->references('id')->on('users')->onDelete('set null');
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
            $table->dropForeign(['ba_approve']);
            $table->dropForeign(['ba_executor']);
            $table->dropColumn('ba_approve');
            $table->dropColumn('ba_executor');
            $table->dropColumn('ba_status');

            $table->dropColumn('approved_at');
            $table->dropColumn('execute_at');
        });
    }
}
