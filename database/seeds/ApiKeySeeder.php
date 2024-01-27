<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Str;

class ApiKeySeeder extends Seeder
{
    public $tokens = 5;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        // And now let's generate a few dozen users for our app:
        for ($i = 0; $i < $this->tokens; $i++) {
            \App\Models\ApiKey::create([
                'account_id' => 1,
                'source' => $faker->company,
                'api_token' => Str::random(60),
            ]);
        }
    }
}
