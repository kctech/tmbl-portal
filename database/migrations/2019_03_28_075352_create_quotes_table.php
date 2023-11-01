<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('client_id')->unsigned();
            $table->json('options');
            $table->double('purchase_val', 15, 2);
            $table->double('loan_amnt', 15, 2);
            $table->double('loan_interest', 15, 2);
            $table->bigInteger('term_yrs');
            $table->bigInteger('term_mnth');
            $table->double('fee', 15, 2);
            $table->text('message')->nullable();
            $table->bigInteger('accepted')->default(0);
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
        Schema::dropIfExists('quotes');
    }
}
