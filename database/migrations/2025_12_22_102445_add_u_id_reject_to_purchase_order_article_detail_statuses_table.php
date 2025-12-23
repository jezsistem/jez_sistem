<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUIdRejectToPurchaseOrderArticleDetailStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_article_detail_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('u_id_reject')->nullable()->after('u_id_approve');
            $table->string('reject_reason')->nullable()->after('u_id_reject');

            $table->foreign('u_id_reject')->references('id')->on('users')->onDelete('no action');
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
            $table->dropForeign(['u_id_reject']);
            $table->dropColumn('u_id_reject');
        });
    }
}
