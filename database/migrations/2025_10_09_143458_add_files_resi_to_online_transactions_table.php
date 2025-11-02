<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilesResiToOnlineTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('online_transactions', function (Blueprint $table) {
            $table->string('files_resi')->nullable()->after('no_resi');
        });
    }

    public function down(): void
    {
        Schema::table('online_transactions', function (Blueprint $table) {
            $table->dropColumn('files_resi');
        });
    }
}
