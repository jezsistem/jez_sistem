<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountSellerToPosTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->double('pos_discount_seller')->default(0)->after('pos_total_discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->dropColumn('discount_seller');
        });
    }
}