<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHrFieldsToNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('ud_id')->nullable()->after('stt_id');
            $table->unsignedBigInteger('user_id')->nullable()->after('ud_id');
            $table->string('type')->default('system')->after('message');
            $table->json('data')->nullable()->after('type');
            
            // Add foreign key constraints
            $table->foreign('ud_id')->references('id')->on('ts_store_types');
            $table->foreign('user_id')->references('id')->on('ts_users');
            
            // Add index for better performance
            $table->index(['ud_id', 'is_read']);
            $table->index(['user_id', 'is_read']);
            $table->index(['type', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['ud_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex(['ud_id', 'is_read']);
            $table->dropIndex(['user_id', 'is_read']);
            $table->dropIndex(['type', 'is_read']);
            $table->dropColumn(['ud_id', 'user_id', 'type', 'data']);
        });
    }
}
