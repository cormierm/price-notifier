<?php

namespace Database\Factories;

use App\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'label' => $this->faker->word,
        ];
    }
}
