<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEarCashAdvanceToIntegerInExternalAssignmentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('external_assignment_requests', function (Blueprint $table) {
            $table->integer('ear_cash_advance')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('external_assignment_requests', function (Blueprint $table) {
            $table->decimal('ear_cash_advance', 15, 2)->default(0.00)->change();
        });
    }
}
