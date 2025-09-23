<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPosTdItemCogsColumnToPosTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transaction_details', function (Blueprint $table) {
            $table->double('pos_td_item_cogs')->default(0)->after('pos_td_sell_price');
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
            $table->dropColumn('pos_td_item_cogs');
        });
    }
}
