<?php
namespace Database\Factories;

use App\Models\StockChange;
use App\Models\Watcher;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockChangeFactory extends Factory
{
    protected $model = StockChange::class;

    public function definition(): array
    {
        return [
            'watcher_id' => function () {
                return Watcher::factory()->create()->id;
            },
            'stock' => $this->faker->boolean,
            'created_at' => \Carbon\Carbon::now()->subMinutes(rand(0, 99999))
        ];
    }
}
