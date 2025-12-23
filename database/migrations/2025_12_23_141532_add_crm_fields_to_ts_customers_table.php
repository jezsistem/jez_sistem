<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCrmFieldsToTsCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('cust_tier', ['academy', 'pro', 'elite'])
                ->default('academy')
                ->after('cust_member');

            $table->unsignedBigInteger('cust_coin')
                ->default(0)
                ->after('cust_tier');

            $table->enum('cust_member_card', ['0', '1'])
                ->default('0')
                ->after('cust_coin');
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
                'cust_tier',
                'cust_coin',
                'cust_member_card',
            ]);
        });
    }
}
