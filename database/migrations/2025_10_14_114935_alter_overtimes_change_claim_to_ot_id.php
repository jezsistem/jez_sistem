<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOvertimesChangeClaimToOtId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            if (Schema::hasColumn('overtime_requests', 'claim')) {
                $table->dropColumn('claim');
            }

            $table->unsignedBigInteger('ot_id')->nullable()->after('attachment');

            $table->foreign('ot_id')
                ->references('id')
                ->on('overtime_types')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->dropForeign(['ot_id']);
            $table->dropColumn('ot_id');

            $table->decimal('claim', 10, 2)->nullable()->after('attachment');
        });
    }
}
