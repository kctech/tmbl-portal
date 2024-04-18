<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChaseStrategySeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sso_providers')->insert([
            'organisation' => 'Microsoft',
            'provider' => 'AZURE',
            'icon' => 'fa-microsoft',
            'color' => '#01a4ef',
        ]);

    }
}
