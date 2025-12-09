<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTsCustomersTableUpdateColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Hapus kolom yang tidak diperlukan
            $table->dropColumn(['93536', '10', '1', '93537', '5', '30']);

            $table->integer('cust_point')->nullable();
            $table->dateTime('cust_last_transactions')->nullable();

        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['cust_point', 'cust_last_transactions', 'created_at', 'updated_at']);

            $table->integer('93536')->nullable();
            $table->integer('10')->nullable();
            $table->integer('1')->nullable();
            $table->integer('93537')->nullable();
            $table->integer('5')->nullable();
            $table->integer('30')->nullable();
        });
    }
}
