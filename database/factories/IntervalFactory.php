<?php

namespace Database\Factories;

use App\Interval;
use Illuminate\Database\Eloquent\Factories\Factory;

class IntervalFactory extends Factory
{
    protected $model = Interval::class;

    public function definition(): array
    {
        return [
                'name' => $this->faker->name,
                'minutes' => $this->faker->numberBetween(1, 1440),
        ];
    }
}
