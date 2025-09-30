<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalAssignmentsRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_assignment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ea_id')
                ->constrained('external_assignment_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->date('ear_date_start');
            $table->time('ear_time_start')->nullable();
            $table->date('ear_date_end')->nullable();
            $table->time('ear_time_end')->nullable();
            $table->string('ear_locations', 255)->nullable();
            $table->decimal('ear_cash_advance', 15, 2)->default(0);
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
        Schema::dropIfExists('external_assignment_requests');
    }
}
