<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountsForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_modules', function ($table) {
            $table->foreign('account_id')->references('id')->on('accounts'); //->onDelete('cascade')
        });
        
        Schema::table('users', function ($table) {
            $table->foreign('account_id')->references('id')->on('accounts'); //->onDelete('cascade')
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
