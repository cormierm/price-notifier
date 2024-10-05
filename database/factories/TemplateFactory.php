<?php

namespace Database\Factories;

use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TemplateFactory extends Factory
{
    protected $model = Template::class;

    public function definition(): array
    {
        return [
            'domain' => $this->faker->domainName,
            'price_query' => $this->faker->word,
            'price_query_type' => \App\Models\Watcher::QUERY_TYPE_XPATH,
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'stock_query' => $this->faker->word,
            'stock_query_type' => \App\Models\Watcher::QUERY_TYPE_XPATH,
            'stock_text' => $this->faker->word,
            'stock_condition' => \App\Models\Watcher::STOCK_CONDITION_CONTAINS_TEXT,
        ];
    }
}
