<?php

use Loonpwn\Swiftype\Tests\App\Models\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => $faker->password,
        'created_at' => $faker->dateTimeBetween('-12 months'),
        'updated_at' => $faker->dateTimeBetween('-12 months'),
    ];
});

