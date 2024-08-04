<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChaseStrategyIdToLeadChaseStepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_chase_steps', function (Blueprint $table) {
            $table->tinyInteger('strategy_id')->nullable()->after('account_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_chase_steps', function (Blueprint $table) {
            $table->dropColumn(['strategy_id']);
        });
    }
}
