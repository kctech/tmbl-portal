<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSsoCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sso_credentials', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->string('provider')->default('AZURE');
            $table->string('client_id');
            $table->string('client_secret');
            $table->string('tenant_id');
            $table->string('domains');
            $table->timestamp('expiry')->nullable();
            $table->tinyInteger('enabled')->default(0);
            $table->tinyInteger('forced')->default(0);
            $table->timestamps();

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
        Schema::dropIfExists('sso_credentials');
    }
}
