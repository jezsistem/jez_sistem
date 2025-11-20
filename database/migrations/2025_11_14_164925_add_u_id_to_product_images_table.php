<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUIdToProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->unsignedBigInteger('u_id')->nullable()->after('p_id');

            // Jika ingin relational ke users table (opsional)
            $table->foreign('u_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_images', function (Blueprint $table) {
            // Jika pakai foreign key, hapus dulu
            $table->dropForeign(['u_id']);

            $table->dropColumn('u_id');
        });
    }
}
