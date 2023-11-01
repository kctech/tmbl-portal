<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            //$table->foreign('role_id')->references('id')->on('roles'); //->onDelete('cascade')
        });

        Schema::table('btl_consents', function($table) {
            $table->foreign('user_id')->references('id')->on('users'); //->onDelete('cascade')
            $table->foreign('client_id')->references('id')->on('clients'); //->onDelete('cascade')
        });

        Schema::table('terms_consents', function($table) {
            $table->foreign('user_id')->references('id')->on('users'); //->onDelete('cascade')
            $table->foreign('client_id')->references('id')->on('clients'); //->onDelete('cascade')
        });

        Schema::table('gdpr_consents', function($table) {
            $table->foreign('user_id')->references('id')->on('users'); //->onDelete('cascade')
            $table->foreign('client_id')->references('id')->on('clients'); //->onDelete('cascade')
        });

        Schema::table('client_transfer_consents', function($table) {
            $table->foreign('user_id')->references('id')->on('users'); //->onDelete('cascade')
            $table->foreign('client_id')->references('id')->on('clients'); //->onDelete('cascade')
        });

        Schema::table('quotes', function($table) {
            $table->foreign('user_id')->references('id')->on('users'); //->onDelete('cascade')
            $table->foreign('client_id')->references('id')->on('clients'); //->onDelete('cascade')
        });

        Schema::table('clients', function($table) {
            $table->foreign('user_id')->references('id')->on('users'); //->onDelete('cascade')
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
