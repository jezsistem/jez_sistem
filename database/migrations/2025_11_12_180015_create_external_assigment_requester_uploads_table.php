<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalAssigmentRequesterUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_assigment_requester_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ear_id');
            $table->string('file_path');
            $table->timestamps();

            $table->foreign('ear_id')->references('id')->on('external_assignment_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('external_assigment_requester_uploads');
    }
}
