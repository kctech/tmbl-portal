<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lead_chase_step_contact_methods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('strategy_id')->unsigned();
            $table->bigInteger('step_id')->unsigned();
            $table->tinyInteger('chase_order')->default(0);
            $table->string('chase_duration');
            $table->string('method');
            $table->tinyInteger('auto_contact')->nullable()->default(0);
            $table->string('name');
            $table->bigInteger('template_ids')->unsigned()->nullable();
            $table->bigInteger('default_template_id')->unsigned()->nullable();
            $table->tinyInteger('status')->default(\App\Models\LeadChaseStepContactMethod::ACTIVE);
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_chase_step_contact_methods');
    }
};
