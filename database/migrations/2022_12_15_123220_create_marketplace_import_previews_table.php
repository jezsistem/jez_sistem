<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketplaceImportPreviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketplace_import_previews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pst_id');
            $table->foreign('pst_id')->references('id')->on('product_stocks');
            $table->unsignedBigInteger('std_id');
            $table->foreign('std_id')->references('id')->on('store_type_divisions');
            $table->string('article_name');
            $table->string('article_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marketplace_import_previews');
    }
}
