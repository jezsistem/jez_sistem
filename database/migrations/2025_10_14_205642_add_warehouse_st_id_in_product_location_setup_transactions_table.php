<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseStIdInProductLocationSetupTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_location_setup_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_st_id')->nullable()->after('st_id');
            $table->foreign('warehouse_st_id')->references('id')->on('stores')->onDelete('no action');
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
            $table->dropForeign(['warehouse_st_id']);
            $table->dropColumn('warehouse_st_id');
        });
    }
}
