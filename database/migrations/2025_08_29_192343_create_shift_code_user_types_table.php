<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftCodeUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_code_user_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_code_id')->constrained('shift_codes')->onDelete('cascade');
            $table->foreignId('user_type_id')->constrained('user_types')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination
            $table->unique(['shift_code_id', 'user_type_id']);
            
            // Add indexes for better performance
            $table->index('shift_code_id');
            $table->index('user_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_code_user_types');
    }
}
