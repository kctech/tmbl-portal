<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEligibilityStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eligibility_statements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('client_id')->unsigned();
            $table->string('statement_type')->nullable();
            $table->text('infor')->nullable();
            $table->json('options');
            $table->text('response_message')->nullable();
            $table->enum('responded', ['Y', 'N'])->default('N');
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
        Schema::dropIfExists('eligibilitystatements');
    }
}
