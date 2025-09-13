<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlineTransactionChatHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_transaction_chat_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ot_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_amp')->default(false);
            $table->boolean('is_readed')->default(false);
            $table->text('messages');
            $table->timestamps();
            
            $table->foreign('ot_id')->references('id')->on('online_transactions')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('online_transaction_chat_history');
    }
}
