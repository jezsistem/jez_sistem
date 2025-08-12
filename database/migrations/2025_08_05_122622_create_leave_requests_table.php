<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User yang mengajukan
            $table->unsignedBigInteger('leave_type_id'); // Tipe cuti
            $table->date('lr_start_date'); // Tanggal mulai
            $table->date('lr_end_date')->nullable(); // Tanggal selesai (untuk multiple days)
            $table->time('lr_start_time')->nullable(); // Jam mulai (untuk partial day)
            $table->time('lr_end_time')->nullable(); // Jam selesai (untuk partial day)
            $table->integer('lr_total_days')->default(0); // Total hari
            $table->integer('lr_total_hours')->default(0); // Total jam
            $table->enum('lr_unit', ['days', 'hours'])->default('days'); // Unit
            $table->text('lr_reason'); // Alasan cuti
            $table->enum('lr_status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('lr_admin_notes')->nullable(); // Catatan admin
            $table->unsignedBigInteger('lr_approved_by')->nullable(); // User yang approve
            $table->timestamp('lr_approved_at')->nullable(); // Waktu approval
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
            $table->foreign('lr_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['user_id', 'lr_start_date']);
            $table->index(['lr_status']);
            $table->index(['lr_approved_by']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_requests');
    }
}
