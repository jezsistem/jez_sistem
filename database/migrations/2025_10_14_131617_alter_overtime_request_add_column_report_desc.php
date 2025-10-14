<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOvertimeRequestAddColumnReportDesc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            // Tambah kolom baru
            $table->text('report_desc')->nullable()->after('details');
            $table->string('report_attachment')->nullable()->after('report_desc');
            $table->unsignedBigInteger('hr_checked_by')->nullable()->after('report_attachment');
            $table->timestamp('hr_checked_at')->nullable()->after('hr_checked_by');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'HR Check', 'Done'])
                ->default('Pending')
                ->after('hr_checked_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->dropColumn([
                'report_desc',
                'report_attachment',
                'hr_checked_by',
                'hr_checked_at',
                'status',
            ]);
        });
    }
}
