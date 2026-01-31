<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuAccessTemplateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_access_template_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_access_template_id');
            $table->unsignedBigInteger('menu_access_id');
            $table->timestamps();

            $table->foreign('menu_access_template_id')->references('id')->on('menu_access_templates')->onDelete('cascade');
            $table->foreign('menu_access_id')->references('id')->on('menu_accesses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_access_template_details');
    }
}
