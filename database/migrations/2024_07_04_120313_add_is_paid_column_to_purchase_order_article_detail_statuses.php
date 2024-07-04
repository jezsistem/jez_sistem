<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPaidColumnToPurchaseOrderArticleDetailStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_article_detail_statuses', function (Blueprint $table) {
            $table->boolean('is_paid')->default(0)->after('u_id_approve')->comment('0: Not Paid, 1: Paid');
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
            //
        });
    }
}
