<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDateToDatetimeOnArrivedAtToPurchaseOrderArticleDetailStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_article_detail_statuses', function (Blueprint $table) {
            $table->dateTime('arrived_at')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_article_detail_statuses', function (Blueprint $table) {
            $table->date('arrived_at')->change();
        });
    }
}
