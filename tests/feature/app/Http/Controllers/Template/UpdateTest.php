<?php

namespace Tests\Feature\App\Http\Controllers\Template;

use App\Template;
use App\Watcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanUpdateTemplate(): void
    {
        $template = Template::factory()->create();

        $data = [
            'price_query' => 'some-query-value',
            'price_query_type' => 'regex',
            'client' => 'curl',
            'price_query' => 'some-stock-query',
            'price_query_type' => 'xpath',
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
            'stock_text' => 'foobar',
            'stock_requires_price' => false,
        ];

        $this->actingAs($template->user)
            ->put(route('template.update', $template), $data)
            ->assertSuccessful();

        $this->assertDatabaseHas('templates', array_merge(
            [
                'id' => $template->id,
                'domain' => $template->domain
            ],
            $data
        ));
    }
}
