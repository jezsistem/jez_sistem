<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyEnumColumnPosStatusInPosTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            // Use the DB::statement method to execute raw SQL
            DB::statement('ALTER TABLE ts_pos_transactions MODIFY pos_status ENUM("DONE","NAMESET","EXCHANGE","REFUND","SHIPPING NUMBER","IN DELIVERY","WAITING FOR CONFIRMATION","IN PROGRESS","CANCEL","PAID","UNPAID","DP") NOT NULL');
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
            DB::statement('ALTER TABLE ts_pos_transactions MODIFY pos_status ENUM("DONE","NAMESET","EXCHANGE","REFUND","SHIPPING NUMBER","IN DELIVERY","WAITING FOR CONFIRMATION","IN PROGRESS","CANCEL","PAID","UNPAID")');
        });
    }
}
