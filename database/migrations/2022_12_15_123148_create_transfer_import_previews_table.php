<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferImportPreviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_import_previews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pst_id');
            $table->foreign('pst_id')->references('id')->on('product_stocks');
            $table->unsignedBigInteger('pl_id');
            $table->foreign('pl_id')->references('id')->on('product_locations');
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
        Schema::dropIfExists('transfer_import_previews');
    }
}
