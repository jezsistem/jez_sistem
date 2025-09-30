<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadAndManagerToStoreTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_types', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_id')->nullable()->after('stt_delete');
            $table->unsignedBigInteger('manager_id')->nullable()->after('lead_id');

            $table->foreign('lead_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_types', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['lead_id', 'manager_id']);
        });
    }
}
