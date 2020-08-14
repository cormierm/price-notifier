<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Watcher;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Watcher::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'url' => $faker->url,
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'query_type' => $faker->word,
        'query' => $faker->word,
        'last_sync' => Carbon::now(),
        'value' => (string) $faker->randomFloat(2, 1, 1000)
    ];
});
