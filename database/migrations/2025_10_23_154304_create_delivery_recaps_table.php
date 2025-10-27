<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryRecapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_recaps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expedition_id')->nullable();
            $table->string('courier_name');
            $table->string('courier_phone')->nullable();
            $table->string('import_file')->nullable();
            $table->longText('signature_pic')->nullable();
            $table->longText('signature_courier')->nullable();
            $table->timestamp('recap_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // relasi opsional
            $table->foreign('expedition_id')->references('id')->on('couriers')->onDelete('set null');
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_recaps');
    }
}
