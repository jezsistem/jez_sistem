<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSettlePartialAndPosNoteSettlePartialColumnsToPosTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->boolean('is_settle_partial')->default(false)->after('is_settle');
            $table->text('pos_notes_settle_partial')->nullable()->after('pos_notes_settle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->dropColumn('is_settle_partial');
            $table->dropColumn('pos_notes_settle_partial');
        });
    }
}
