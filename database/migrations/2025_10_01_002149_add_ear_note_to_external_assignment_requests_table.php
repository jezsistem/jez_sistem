<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEarNoteToExternalAssignmentRequestsTable extends Migration
{
    public function up(): void
    {
        Schema::table('external_assignment_requests', function (Blueprint $table) {
            $table->text('ear_note')->nullable()->after('ear_cash_advance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_assignment_requests', function (Blueprint $table) {
            $table->dropColumn('ear_note');
        });
    }
}
