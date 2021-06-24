<?php

namespace Tests\App\Http\Controllers\Template;

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
        $template = factory(Template::class)->create();

        $data = [
            'price_query' => 'some-query-value',
            'client' => 'curl',
            'xpath_stock' => 'some-stock-query',
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
            'stock_text' => 'foobar',
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
