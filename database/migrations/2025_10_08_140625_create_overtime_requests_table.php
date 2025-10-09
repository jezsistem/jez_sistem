<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOvertimeRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_by');
            $table->date('submission_date');
            $table->string('department');
            $table->json('assigned_staff');
            $table->date('start_date');
            $table->time('start_time');
            $table->date('end_date');
            $table->time('end_time');
            $table->text('details');
            $table->string('attachment')->nullable();
            $table->decimal('claim', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('request_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('overtime_requests');
    }
}
