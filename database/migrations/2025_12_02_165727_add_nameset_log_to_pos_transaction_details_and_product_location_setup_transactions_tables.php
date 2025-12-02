<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNamesetLogToPosTransactionDetailsAndProductLocationSetupTransactionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transaction_details', function (Blueprint $table) {
            $table->unsignedBigInteger('pos_td_nameset_by')->nullable()->after('pos_td_nameset');
            $table->dateTime('pos_td_nameset_at')->nullable()->after('pos_td_nameset_by');

            $table->foreign('pos_td_nameset_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('nameset_by')->nullable()->after('cancel_pickup_time');
            $table->dateTime('nameset_at')->nullable()->after('nameset_by');

            $table->foreign('nameset_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_transaction_details', function (Blueprint $table) {
            $table->dropForeign(['pos_td_nameset_by']);
            $table->dropColumn('pos_td_nameset_by');
            $table->dropColumn('pos_td_nameset_at');
        });

        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            $table->dropForeign(['nameset_by']);
            $table->dropColumn('nameset_by');
            $table->dropColumn('nameset_at');
        });
    }
}
