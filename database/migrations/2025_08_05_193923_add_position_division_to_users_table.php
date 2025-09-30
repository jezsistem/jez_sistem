<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionDivisionToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('up_id')->nullable()->after('u_delete');
            $table->unsignedBigInteger('ud_id')->nullable()->after('up_id');
            
            $table->foreign('up_id')->references('id')->on('user_positions')->onDelete('set null');
            $table->foreign('ud_id')->references('id')->on('user_divisions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['up_id']);
            $table->dropForeign(['ud_id']);
            $table->dropColumn(['up_id', 'ud_id']);
        });
    }
}
