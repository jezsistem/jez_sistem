<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickAccessAndPosAccessColumnOnTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('pos_access')->nullable()->after('delete_access');
            $table->tinyInteger('pick_access')->nullable()->after('pos_access');
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
            $table->dropColumn(['pos_access, pick_access']);
        });
    }
}
