<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\PriceChange::class, function (Faker $faker) {
    return [
        'watcher_id' => function () {
            return factory(\App\Watcher::class)->create()->id;
        },
        'price' => $faker->randomFloat(2, 1, 100),
        'stock' => $faker->boolean,
        'created_at' => \Carbon\Carbon::now()->subMinutes(rand(0, 99999))
    ];
});
