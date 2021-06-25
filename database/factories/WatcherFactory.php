<?php

namespace Database\Factories;

use App\Interval;
use App\User;
use App\Utils\Fetchers\HtmlFetcher;
use App\Watcher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class WatcherFactory extends Factory
{
    protected $model = Watcher::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'url' => $this->faker->url,
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'price_query' => $this->faker->word,
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'last_sync' => Carbon::now(),
            'value' => (string) $this->faker->randomFloat(2, 1, 1000),
            'interval_id' => function () {
                return Interval::factory()->create()->id;
            },
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
        ];
    }

    public function disabled()
    {
        return $this->state(function (array $attributes) {
            return [
                'interval_id' => null,
            ];
        });
    }
}
