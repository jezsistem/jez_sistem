<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePositionAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('position_access', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('position_id');
            $table->string('route')->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->enum('action', ['create', 'read', 'update', 'delete'])->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->timestamps();
        }, 'utf8mb4', 'utf8mb4_general_ci');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('position_access');
    }
}
