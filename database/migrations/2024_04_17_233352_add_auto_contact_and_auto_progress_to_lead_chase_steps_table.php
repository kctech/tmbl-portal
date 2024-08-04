<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoContactAndAutoProgressToLeadChaseStepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_chase_steps', function (Blueprint $table) {
            $table->tinyInteger('auto_contact')->nullable()->after('chase_duration')->default(0);
            $table->tinyInteger('auto_progress')->nullable()->after('auto_contact')->default(0);
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
            $table->dropColumn(['auto_contact']);
            $table->dropColumn(['auto_progress']);
        });
    }
}
