<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Interval;
use App\User;
use App\Utils\Fetchers\HtmlFetcher;
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
        'price_query' => $faker->word,
        'last_sync' => Carbon::now(),
        'value' => (string) $faker->randomFloat(2, 1, 1000),
        'interval_id' => function () {
            return factory(Interval::class)->create()->id;
        },
        'client' => HtmlFetcher::CLIENT_BROWERSHOT,
    ];
});

$factory->state(Watcher::class, 'disabled', function () {
    return [
        'interval_id' => null,
    ];
});
