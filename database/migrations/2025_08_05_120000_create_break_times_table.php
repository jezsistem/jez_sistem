<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreakTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('break_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('daily_schedule_id')->nullable();
            $table->date('bt_date');
            $table->time('bt_start_time')->nullable();
            $table->time('bt_end_time')->nullable();
            $table->integer('bt_duration_minutes')->default(0);
            $table->enum('bt_type', ['break_1', 'break_2'])->default('break_1');
            $table->enum('bt_status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('bt_notes')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('daily_schedule_id')->references('id')->on('daily_schedules')->onDelete('set null');
            $table->index(['user_id', 'bt_date']);
            $table->index(['bt_date']);
            $table->unique(['user_id', 'bt_date', 'bt_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('break_times');
    }
} 