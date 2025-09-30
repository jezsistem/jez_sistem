<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalAssignmentsRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_assignment_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ear_id')
                ->constrained('external_assignment_requests')
                ->cascadeOnDelete();
            $table->date('rundown_date');
            $table->time('rundown_time')->nullable();
            $table->string('activity', 255);
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('external_assignment_request_details');
    }
}
