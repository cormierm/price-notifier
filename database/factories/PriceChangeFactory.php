<?php
namespace Database\Factories;

use App\PriceChange;
use App\Watcher;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceChangeFactory extends Factory
{
    protected $model = PriceChange::class;

    public function definition(): array
    {
        return [
            'watcher_id' => function () {
                return Watcher::factory()->create()->id;
            },
            'price' => $this->faker->randomFloat(2, 1, 100),
            'created_at' => \Carbon\Carbon::now()->subMinutes(rand(0, 99999))
        ];
    }
}
