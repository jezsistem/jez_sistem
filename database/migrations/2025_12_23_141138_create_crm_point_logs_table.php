<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmPointLogsTable extends Migration
{
    public function up(): void
    {
        Schema::create('crm_point_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cust_id');
            $table->unsignedBigInteger('pt_id');
            $table->double('trx_value');
            $table->enum('tier', ['academy','pro','elite']);
            $table->integer('base_point');
            $table->integer('multiplier');
            $table->integer('point_earned');
            $table->timestamps();

            $table->index('cust_id');
            $table->index('pt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_point_logs');
    }
}
