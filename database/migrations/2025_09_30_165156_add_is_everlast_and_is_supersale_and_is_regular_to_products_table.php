<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsEverlastAndIsSupersaleAndIsRegularToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_everlast')->default(false)->after('mp_stock_masking');
            $table->boolean('is_supersale')->default(false)->after('is_everlast');
            $table->boolean('is_reguler')->default(false)->after('is_supersale');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_everlast', 'is_supersale', 'is_reguler']);
        });
    }
}
