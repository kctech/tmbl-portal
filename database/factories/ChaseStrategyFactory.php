<?php

use App\Models\ApiUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\Models\ApiKey::class, function (Faker $faker) {
    return [
        'account_id' => 1,
        'name' => 'Default',
        'status' => 0
    ];
});