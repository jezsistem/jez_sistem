<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPdDateStart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_discounts', function (Blueprint $table) {
            $table->date('pd_date_start')->after('pd_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_discounts', function (Blueprint $table) {
            $table->dropColumn('pd_date_start');
        });
    }
}
