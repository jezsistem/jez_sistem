<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('daily_schedule_id')->nullable();
            $table->date('at_date');
            $table->time('at_time_in')->nullable();
            $table->time('at_time_out')->nullable();
            $table->enum('at_status', ['present', 'late', 'absent', 'early_leave', 'scan_once'])->default('present');
            $table->text('at_notes')->nullable();
            $table->string('at_source')->default('fingerprint'); // fingerprint, manual, system
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('daily_schedule_id')->references('id')->on('daily_schedules')->onDelete('set null');
            $table->index(['user_id', 'at_date']);
            $table->index(['at_date']);
            $table->unique(['user_id', 'at_date']); // Satu user hanya bisa satu record per hari
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance');
    }
}
