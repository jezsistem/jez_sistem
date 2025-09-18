<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubPaymentPartialColumnToPosTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->tinyInteger('sub_payment_partial')->nullable()->after('sub_payment');
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
            $table->dropColumn('sub_payment_partial');
        });
    }
}
