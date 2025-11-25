<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaBroadcastJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wa_broadcast_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_name');
            $table->datetime('start_at');
            $table->datetime('end_at');
            $table->integer('interval_hours');
            $table->integer('batch_size');
            $table->text('message');
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->integer('last_offset')->default(0); // untuk batch berikutnya
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
        Schema::dropIfExists('wa_broadcast_jobs');
    }
}
