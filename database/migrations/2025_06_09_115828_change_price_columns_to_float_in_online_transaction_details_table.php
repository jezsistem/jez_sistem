<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePriceColumnsToFloatInOnlineTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('online_transaction_details', function (Blueprint $table) {
            $table->float('original_price')->change();
            $table->float('discount_seller')->change();
            $table->float('discount_platform')->change();
            $table->float('total_discount')->change();
            $table->float('price_after_discount')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('online_transaction_details', function (Blueprint $table) {
            $table->string('original_price')->change();
            $table->string('discount_seller')->change();
            $table->string('discount_platform')->change();
            $table->string('total_discount')->change();
            $table->string('price_after_discount')->change();
        });
    }
}
