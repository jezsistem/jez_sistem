<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersonalDataColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('u_ktp_image')->nullable();
            $table->string('u_npwp')->nullable();
            $table->string('u_npwp_image')->nullable();
            $table->string('u_birthday')->nullable();
            $table->string('u_bpjs_kes_number')->nullable();
            $table->string('u_bpjs_kes_image')->nullable();
            $table->string('u_bpjs_tk_number')->nullable();
            $table->string('u_bpjs_tk_image')->nullable();
            $table->string('u_bank_name')->nullable();
            $table->string('u_bank_account_number')->nullable();
            $table->string('u_bank_account_holder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'u_ktp_image',
                'u_npwp',
                'u_npwp_image',
                'u_birthday',
                'u_bpjs_kes_number',
                'u_bpjs_kes_image',
                'u_bpjs_tk_number',
                'u_bpjs_tk_image',
                'u_bank_name',
                'u_bank_account_number',
                'u_bank_account_holder',
            ]);
        });
    }
}
