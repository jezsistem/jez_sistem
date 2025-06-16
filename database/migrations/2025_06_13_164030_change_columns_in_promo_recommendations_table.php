<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsInPromoRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promo_recommendations', function (Blueprint $table) {
            $table->dropForeign(['p_id']);
            $table->dropColumn('p_id');

            // Change 'discount' column to 'amount' with float type
            $table->dropColumn('discount');

            // Change 'note' column to 'amount' with float type
            $table->dropColumn('notes');

            $table->unsignedBigInteger('u_id')->after('id');
            $table->string('pr_code')->unique()->after('u_id');

            $table->foreign('u_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promo_recommendations', function (Blueprint $table) {
            $table->dropForeign(['u_id']);
            $table->dropColumn('u_id');
            $table->dropColumn('pr_code');

            $table->unsignedBigInteger('p_id')->nullable();
            $table->float('discount')->nullable();
            $table->string('notes')->nullable();

            $table->foreign('p_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
}
