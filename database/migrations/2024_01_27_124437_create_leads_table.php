<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->bigInteger('source_id')->unsigned();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email_address')->nullable();
            $table->string('contact_number')->nullable();
            $table->json('data')->nullable();
            $table->tinyInteger('status')->default(\App\Models\Lead::PROSPECT);
            $table->tinyInteger('contact_count')->default(0);
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('allocated_at')->nullable();
            $table->timestamp('transferred_at')->nullable();
            $table->timestamps();

            $table->index('source_id');
            $table->index('account_id');
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
        Schema::dropIfExists('leads');
    }
}
