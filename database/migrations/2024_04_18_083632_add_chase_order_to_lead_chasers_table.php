<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChaseOrderToLeadChasersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_chasers', function (Blueprint $table) {
            $table->tinyInteger('chase_order')->after('strategy_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_chasers', function (Blueprint $table) {
            $table->dropColumn(['chase_order']);
        });
    }
}
