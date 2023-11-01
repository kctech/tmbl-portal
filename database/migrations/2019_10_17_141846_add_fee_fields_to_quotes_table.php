<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFeeFieldsToQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('fee_type')->after('term_mnth')->default('Fixed Fee');
            $table->string('fee_timing')->after('fee')->default('Application');
            $table->string('fee_2_type')->after('fee_timing')->default('NA');
            $table->double('fee_2', 15, 2)->after('fee_2_type');
            $table->string('fee_2_timing')->after('fee_2')->default('NA');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['fee_type']);
            $table->dropColumn(['fee_timing']);
            $table->dropColumn(['fee_2_type']);
            $table->dropColumn(['fee_2']);
            $table->dropColumn(['fee_2_timing']);
        });
    }
}