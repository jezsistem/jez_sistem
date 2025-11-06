<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCanceledInternalOrderStatusToOnlineTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('online_transactions', function (Blueprint $table) {
            DB::statement("ALTER TABLE ts_online_transactions MODIFY COLUMN internal_order_status ENUM('NEW TRX', 'WAITING ONLINE', 'UNDER REVIEW', 'WAITING RECEIPT', 'WAITING PACKING', 'DONE', 'DONE ONLINE','CANCEL') NULL");
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
            DB::statement("ALTER TABLE ts_online_transactions MODIFY COLUMN internal_order_status ENUM('NEW TRX', 'WAITING ONLINE', 'UNDER REVIEW', 'WAITING RECEIPT', 'WAITING PACKING', 'DONE', 'DONE ONLINE') NULL");
        });
    }
}
