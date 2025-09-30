<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPstIdAndBaCogsColumnToBinAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bin_adjustments', function (Blueprint $table) {
            $table->unsignedBigInteger('pst_id')->nullable()->after('pls_id');
            $table->float('ba_cogs')->nullable()->after('ba_code');

            $table->foreign('pst_id')->references('id')->on('product_stocks')->onDelete('no action');
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
            $table->dropForeign(['pst_id']);
            $table->dropColumn('pst_id');
            $table->dropColumn('ba_cogs');
        });
    }
}
