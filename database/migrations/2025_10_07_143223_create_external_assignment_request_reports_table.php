<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalAssignmentRequestReportsTable extends Migration
{
    public function up(): void
    {
        Schema::create('external_assignment_request_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ear_id')->constrained('external_assignment_requests')->onDelete('cascade');
            $table->text('earr_detail')->nullable();
            $table->date('earr_date')->nullable();
            $table->time('earr_time_start')->nullable();
            $table->time('earr_time_end')->nullable();
            $table->decimal('cash_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_assignment_request_reports');
    }
}
