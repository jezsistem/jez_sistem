<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtdIdColumnToProductLocationSetupTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('otd_id')->nullable()->after('pt_id');
            $table->foreign('otd_id')->references('id')->on('online_transaction_details')->onDelete('set null');
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
            $table->dropForeign(['otd_id']);
            $table->dropColumn('otd_id');
        });
    }
}
