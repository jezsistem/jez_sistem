<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreakTimeBackupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('break_time_backups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('bt_date');
            $table->time('bt_start_time');
            $table->time('bt_end_time')->nullable();
            $table->enum('bt_type', ['break_1', 'break_2', 'break_3', 'break_4'])->default('break_1');
            $table->enum('bt_status', ['active', 'completed', 'cancelled'])->default('active');
            $table->integer('bt_duration_minutes')->nullable();
            $table->text('bt_notes')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['user_id', 'bt_date']);
            $table->index(['bt_date']);
            $table->index(['bt_status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('break_time_backups');
    }
}
