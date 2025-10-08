<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPosTdItemPriceTagColumnToPosTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transaction_details', function (Blueprint $table) {
            $table->double('pos_td_item_price_tag')->default(0)->after('pos_td_item_cogs');
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
            $table->dropColumn('pos_td_item_price_tag');
        });
    }
}
