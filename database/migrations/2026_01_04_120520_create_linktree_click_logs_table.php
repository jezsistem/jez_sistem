<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinktreeClickLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linktree_click_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('linktree_link_id')
                ->constrained('linktree_links')
                ->cascadeOnDelete();

            $table->enum('type', ['whatsapp', 'marketplace', 'report']);
            $table->enum('platform', ['whatsapp', 'tiktok', 'shopee', 'tokopedia'])->nullable();

            $table->string('province')->nullable();
            $table->string('city')->nullable();

            $table->string('referrer')->nullable(); // instagram.com

            $table->ipAddress('ip')->nullable();
            $table->string('user_agent')->nullable();

            $table->date('click_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('linktree_click_logs');
    }
}
