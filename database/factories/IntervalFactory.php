<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Interval;
use Faker\Generator as Faker;

$factory->define(Interval::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'minutes' => $faker->numberBetween(1, 1440),
    ];
});
