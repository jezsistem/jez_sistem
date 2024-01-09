<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPosPriceItemDiscountToPosTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transaction_details', function (Blueprint $table) {
            $table->double('pos_td_price_item_discount')->after('pos_td_discount_price')->default(0);
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
            $table->dropColumn('pos_td_price_item_discount');
        });
    }
}
