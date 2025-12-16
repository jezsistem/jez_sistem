<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUIdHelperQcAndDoneQcTimeToProductLocationSetupTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('u_id_helper_qc')->nullable()->after('u_id_helper');
            $table->timestamp('done_qc_time')->nullable()->after('move_store_time');

            $table->foreign('u_id_helper_qc')->references('id')->on('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            $table->dropForeign(['u_id_helper_qc']);
            $table->dropColumn('u_id_helper_qc');
            $table->dropColumn('done_qc_time');
        });
    }
}
