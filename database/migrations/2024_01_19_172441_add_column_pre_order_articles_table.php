<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPreOrderArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pre_order_articles', function (Blueprint $table) {
            $table->double('poa_discount')->after('pr_id')->nullable();
            $table->double('poa_extra_discount')->after('poa_discount')->nullable();
            $table->double('poa_sub_discount')->after('poa_extra_discount')->nullable();
            $table->double('poa_reminder')->after('poa_sub_discount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
