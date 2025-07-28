<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoaStatusToPreOrderArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pre_order_articles', function (Blueprint $table) {
            $table->smallInteger('poa_status')->after('poa_draft')->default(1);
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
            $table->dropColumn('poa_status');
        });
    }
}
