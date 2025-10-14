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

            // siapa yang mengajukan
            $table->unsignedBigInteger('request_by');

            // data utama
            $table->date('submission_date');
            $table->unsignedBigInteger('ud_id');
            $table->json('assigned_staff');
            $table->date('start_date');
            $table->time('start_time');
            $table->date('end_date');
            $table->time('end_time');
            $table->text('details');
            $table->string('attachment')->nullable();
            $table->decimal('claim', 10, 2)->nullable();
            // approval
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            // foreign key
            $table->foreign('request_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            // Jika department terkait tabel user_divisions:
            // $table->foreign('department')->references('id')->on('user_divisions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('overtime_requests');
    }
}
