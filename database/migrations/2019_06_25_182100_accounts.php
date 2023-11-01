<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Accounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('acronym');
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('town');
            $table->string('city')->nullable();
            $table->string('county');
            $table->string('postcode');
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->string('reg_no');
            $table->string('logo');
            $table->string('logo_frontend');
            $table->string('logo_pdf');
            $table->string('viewset')->default('default');
            $table->string('css');
            $table->json('options')->nullable();
            $table->timestamps();

            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('account_id')->default(1)->unsigned();

            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
