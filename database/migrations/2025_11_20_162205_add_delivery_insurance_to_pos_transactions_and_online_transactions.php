<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryInsuranceToPosTransactionsAndOnlineTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transaction_details', function (Blueprint $table) {
            $table->bigInteger('pos_td_delivery_insurance')->default(0)->after('pos_td_nameset');
        });

        Schema::table('online_transaction_details', function (Blueprint $table) {
            $table->bigInteger('delivery_insurance')->default(0)->after('total_discount');
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
            $table->dropColumn('pos_td_delivery_insurance');
        });
        
        Schema::table('online_transaction_details', function (Blueprint $table) {
            $table->dropColumn('delivery_insurance');
        });
    }
}
