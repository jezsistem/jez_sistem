<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveRequestAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leave_request_id');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('file_type');
            $table->bigInteger('file_size');
            $table->timestamps();
            
            $table->foreign('leave_request_id')->references('id')->on('leave_requests')->onDelete('cascade');
            $table->index(['leave_request_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_request_attachments');
    }
}
