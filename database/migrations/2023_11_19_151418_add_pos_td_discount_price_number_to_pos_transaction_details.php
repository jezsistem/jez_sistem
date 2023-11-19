<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPosTdDiscountPriceNumberToPosTransactionDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transaction_details', function (Blueprint $table) {
            $table->double('pos_td_discount_number')->nullable()->after('pos_td_discount');
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
            //
        });
    }
}
