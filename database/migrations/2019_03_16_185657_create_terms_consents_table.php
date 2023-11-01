<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermsConsentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms_consents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('client_id')->unsigned();
            $table->string('service');
            $table->longtext('description');
            $table->text('type');
            $table->double('amount', 8, 2);
            $table->text('timing');
            $table->enum('privacy_consent', ['Y', 'N'])->default('N');
            $table->enum('terms_consent', ['Y', 'N'])->default('N');
            $table->text('signature')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('client_id');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms_consents');
    }
}
