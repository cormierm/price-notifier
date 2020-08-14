<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\WatcherLog;
use Faker\Generator as Faker;

$factory->define(WatcherLog::class, function (Faker $faker) {
    $randomPrice = $faker->randomFloat(2, 1, 100);

    return [
        'watcher_id' => function () {
            return factory(\App\Watcher::class)->create()->id;
        },
        'formatted_value' => $randomPrice,
        'raw_value' => $faker->word . $randomPrice,
        'duration' => $faker->randomDigit,
        'error' => null,
    ];
});
