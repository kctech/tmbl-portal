<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChaseStrategyIdAndStrategyPositionToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->tinyInteger('strategy_id')->nullable()->after('account_id')->default(1);
            $table->tinyInteger('strategy_position_id')->nullable()->after('strategy_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['strategy_id']);
            $table->dropColumn(['strategy_position']);
        });
    }
}
