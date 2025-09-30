<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSettleColumnToPosTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->boolean('is_settle')->default(false)->after('pos_status');
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
            $table->dropColumn('is_settle');
        });
    }
}
