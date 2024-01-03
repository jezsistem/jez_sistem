<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPosTotalVouchersToPosTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->double('pos_total_vouchers', 10, 2)->after('pos_shipping')->default(0);
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
            //
        });
    }
}
