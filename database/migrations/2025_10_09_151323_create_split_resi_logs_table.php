<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSplitResiLogsTable extends Migration
{
    public function up()
    {
        Schema::create('split_resi_logs', function (Blueprint $table) {
            $table->id();
            $table->string('original_file');
            $table->string('split_file');
            $table->unsignedBigInteger('uploaded_by')->nullable();
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
        Schema::dropIfExists('split_resi_logs');
    }
}
