<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SSOProviderSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sso_providers')->insert([
            'account_id' => 1,
            'name' => 'Default',
            'status' => 0
        ]);

    }
}
