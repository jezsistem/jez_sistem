<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrintResiStatusToOnlineTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('online_transactions', function (Blueprint $table) {
            $table->boolean('print_resi')->default(false)->after('online_print');
            $table->dateTime('time_print_resi')->nullable()->after('time_print');
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
            $table->dropColumn('print_resi');
            $table->dropColumn('time_print_resi');
        });
    }
}
