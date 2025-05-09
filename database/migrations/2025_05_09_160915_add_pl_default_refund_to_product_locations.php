<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlDefaultRefundToProductLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_locations', function (Blueprint $table) {
            $table->int('pl_default_refund')->after('pl_default');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_locations', function (Blueprint $table) {
            $table->dropColumn('pl_default_refund');
        });
    }
}
