<?php

namespace Database\Factories;

use App\Watcher;
use App\WatcherLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class WatcherLogFactory extends Factory
{
    protected $model = WatcherLog::class;

    public function definition(): array
    {
        $randomPrice = $this->faker->randomFloat(2, 1, 100);

        return [
            'watcher_id' => function () {
                return Watcher::factory()->create()->id;
            },
            'formatted_value' => $randomPrice,
            'raw_value' => $this->faker->word . $randomPrice,
            'duration' => $this->faker->randomDigit,
            'error' => null,
        ];
    }
}
