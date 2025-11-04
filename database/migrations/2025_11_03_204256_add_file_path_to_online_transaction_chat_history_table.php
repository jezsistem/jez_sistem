<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilePathToOnlineTransactionChatHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('online_transaction_chat_history', function (Blueprint $table) {
            $table->text('messages')->nullable()->change();
            $table->string('file_path')->nullable()->after('messages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('online_transaction_chat_history', function (Blueprint $table) {
            $table->text('messages')->change();
            $table->dropColumn('file_path');
        });
    }
}
