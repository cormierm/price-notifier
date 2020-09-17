<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Template;
use Faker\Generator as Faker;

$factory->define(Template::class, function (Faker $faker) {
    return [
        'domain' => $faker->domainName,
        'xpath_value' => $faker->word,
        'user_id' => function () {
            return factory(\App\User::class)->create()->id;
        },
        'xpath_stock' => $faker->word,
        'stock_text' => $faker->word,
    ];
});
