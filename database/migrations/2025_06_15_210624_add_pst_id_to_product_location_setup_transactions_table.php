<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPstIdToProductLocationSetupTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('pst_id')->after('sa_id')->nullable();

            $table->foreign('pst_id')->references('id')->on('product_stocks')->onDelete('set null');
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
            $table->dropForeign(['pst_id']);
            $table->dropColumn('pst_id');
        });
    }
}
