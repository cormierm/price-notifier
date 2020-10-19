<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\StockChange::class, function (Faker $faker) {
    return [
        'watcher_id' => function () {
            return factory(\App\Watcher::class)->create()->id;
        },
        'stock' => $faker->boolean,
        'created_at' => \Carbon\Carbon::now()->subMinutes(rand(0, 99999))
    ];
});
