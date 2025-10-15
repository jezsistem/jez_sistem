<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternalOrderStatusToOnlineTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('online_transactions', function (Blueprint $table) {
            $table->enum('internal_order_status', ['NEW TRX', 'WAITING ONLINE', 'UNDER REVIEW', 'WAITING RECEIPT','WAITING PACKING','DONE'])->nullable()->after('order_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('online_transactions', function (Blueprint $table) {
            $table->dropColumn('internal_order_status');
        });
    }
}
