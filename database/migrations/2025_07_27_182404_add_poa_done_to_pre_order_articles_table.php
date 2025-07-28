<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoaDoneToPreOrderArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pre_order_articles', function (Blueprint $table) {
            $table->boolean('poa_done')->after('poa_reminder')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pre_order_articles', function (Blueprint $table) {
            $table->dropColumn('poa_done');
        });
    }
}
