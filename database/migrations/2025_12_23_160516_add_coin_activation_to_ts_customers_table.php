<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoinActivationToTsCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('cust_coin_active', ['0','1'])
                ->default('0')
                ->after('cust_coin');

            $table->unsignedBigInteger('cust_coin_pending')
                ->default(0)
                ->after('cust_coin_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'cust_coin_active',
                'cust_coin_pending'
            ]);
        });
    }
}
