<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExternalAssignmentRequestsCashDetailTable extends Migration
{
    public function up()
    {
        Schema::create('external_assignment_request_cash_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ear_id'); // relasi ke external_assignment_requests
            $table->string('cash_purpose'); // tujuan penggunaan cash
            $table->decimal('cash_amount', 15, 2); // jumlah cash

            $table->timestamps();

            // Foreign key
            $table->foreign('ear_id')
                ->references('id')
                ->on('external_assignment_requests')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('external_assignment_request_cash_details');
    }
}
