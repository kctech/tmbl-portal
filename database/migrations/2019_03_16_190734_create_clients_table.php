<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('tel')->nullable();

            $table->enum('comm_email_consent', ['Y', 'N'])->default('N');
            $table->enum('comm_phone_consent', ['Y', 'N'])->default('N');
            $table->enum('comm_sms_consent', ['Y', 'N'])->default('N');
            $table->enum('comm_face_consent', ['Y', 'N'])->default('N');
            $table->enum('comm_thirdparty_consent', ['Y', 'N'])->default('N');
            $table->enum('comm_other_consent', ['Y', 'N'])->default('N');

            $table->enum('mkt_post_consent', ['Y', 'N'])->default('N');
            $table->enum('mkt_automatedcall_consent', ['Y', 'N'])->default('N');
            $table->enum('mkt_web_consent', ['Y', 'N'])->default('N');
            $table->enum('mkt_email_consent', ['Y', 'N'])->default('N');
            $table->enum('mkt_phone_consent', ['Y', 'N'])->default('N');
            $table->enum('mkt_sms_consent', ['Y', 'N'])->default('N');
            $table->enum('mkt_face_consent', ['Y', 'N'])->default('N');
            $table->enum('mkt_thirdparty_consent', ['Y', 'N'])->default('N');
            $table->enum('mkt_other_consent', ['Y', 'N'])->default('N');
            
            $table->string('uid')->unique();
            $table->string('password')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            
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
        Schema::dropIfExists('clients');
    }
}
