<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User
            $table->unsignedBigInteger('leave_type_id'); // Tipe cuti
            $table->integer('lb_year'); // Tahun
            $table->integer('lb_initial_balance'); // Jatah awal
            $table->integer('lb_used_balance'); // Cuti yang sudah digunakan
            $table->integer('lb_remaining_balance'); // Sisa cuti
            $table->text('lb_notes')->nullable(); // Catatan
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
            $table->unique(['user_id', 'leave_type_id', 'lb_year']);
            $table->index(['user_id', 'lb_year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_balances');
    }
}
