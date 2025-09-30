<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('id');
            $table->string('avatar')->nullable()->after('u_email');
            $table->string('provider')->nullable()->after('avatar'); // 'google', 'local', 'both'
            $table->boolean('google_linked')->default(false)->after('provider');
            $table->timestamp('google_linked_at')->nullable()->after('google_linked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar', 'provider', 'google_linked', 'google_linked_at']);
        });
    }
};
