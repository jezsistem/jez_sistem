<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinktreePageViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linktree_page_views', function (Blueprint $table) {
            $table->id();

            $table->ipAddress('ip')->nullable();
            $table->string('user_agent')->nullable();

            $table->string('province')->nullable(); // Jawa Timur
            $table->string('city')->nullable();     // Malang

            $table->string('referrer')->nullable();
            // instagram.com, facebook.com

            $table->date('view_date');

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
        Schema::dropIfExists('linktree_page_views');
    }
}
