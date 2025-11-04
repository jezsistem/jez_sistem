<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPinnedToOnlineTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('online_transactions', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false)->after('print_manifest');
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
            $table->dropColumn('is_pinned');
        });
    }
}
