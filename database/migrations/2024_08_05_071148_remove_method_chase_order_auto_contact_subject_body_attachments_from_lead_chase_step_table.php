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
        Schema::table('lead_chase_steps', function (Blueprint $table) {
            $table->dropColumn('method');
            $table->dropColumn('auto_contact');
            $table->dropColumn('subject');
            $table->dropColumn('body');
            $table->dropColumn('attachments');
            $table->dropColumn('chase_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_chase_steps', function (Blueprint $table) {
            //
        });
    }
};
