<?php

namespace Database\Factories;

use App\Template;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TemplateFactory extends Factory
{
    protected $model = Template::class;

    public function definition(): array
    {
        return [
            'domain' => $this->faker->domainName,
            'price_query' => $this->faker->word,
            'price_query_type' => \App\Watcher::QUERY_TYPE_XPATH,
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'xpath_stock' => $this->faker->word,
            'stock_text' => $this->faker->word,
            'stock_condition' => \App\Watcher::STOCK_CONDITION_CONTAINS_TEXT,
        ];
    }
}
