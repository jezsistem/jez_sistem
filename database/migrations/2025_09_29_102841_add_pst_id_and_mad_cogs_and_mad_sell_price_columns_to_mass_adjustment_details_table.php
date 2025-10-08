<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPstIdAndMadCogsAndMadSellPriceColumnsToMassAdjustmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mass_adjustment_details', function (Blueprint $table) {
            $table->unsignedBigInteger('pst_id')->nullable()->after('pls_id');
            $table->float('mad_cogs')->nullable()->after('pst_id');
            $table->float('mad_sell_price')->nullable()->after('mad_cogs');

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
        Schema::table('mass_adjustment_details', function (Blueprint $table) {
            $table->dropForeign(['pst_id']);
            $table->dropColumn('pst_id');
            $table->dropColumn('mad_cogs');
            $table->dropColumn('mad_sell_price');
        });
    }
}
