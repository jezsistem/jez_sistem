<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNotesSettleAndNotesDpToPosTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->text('pos_notes_settle')->nullable()->after('pos_note');
            $table->text('pos_notes_dp')->nullable()->after('pos_notes_settle');
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
            $table->dropColumn('pos_notes_settle');
            $table->dropColumn('pos_notes_dp');
        });
    }
}
