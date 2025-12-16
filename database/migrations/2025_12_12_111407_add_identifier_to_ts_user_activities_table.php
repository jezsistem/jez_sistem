<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdentifierToTsUserActivitiesTable extends Migration
{
    public function up(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->string('identifier')->nullable()->after('user_id');
            $table->integer('key_identifier')->nullable()->after('identifier');
        });
    }

    public function down(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->dropColumn(['identifier', 'key_identifier']);
        });
    }
}
