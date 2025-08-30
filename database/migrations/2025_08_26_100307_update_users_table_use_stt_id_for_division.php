<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateUsersTableUseSttIdForDivision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add ud_id as alias to stt_id for backward compatibility
            // This allows queries to use both ud_id and stt_id
            $table->unsignedBigInteger('ud_id')->nullable()->after('stt_id');
            
            // Copy stt_id values to ud_id for existing records
            DB::statement('UPDATE ts_users SET ud_id = stt_id WHERE stt_id IS NOT NULL');
            
            // Add foreign key constraint for ud_id pointing to store_types
            $table->foreign('ud_id')->references('id')->on('ts_store_types');
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
            $table->dropForeign(['ud_id']);
            $table->dropColumn('ud_id');
        });
    }
}
