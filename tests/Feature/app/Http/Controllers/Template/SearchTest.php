<?php

namespace Tests\App\Http\Controllers\Template;

use App\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanFindTemplateByDomain(): void
    {
        $template = Template::factory()->create();

        $this->actingAs($template->user)->get(route('template.search', $template->domain))
            ->assertSuccessful()
            ->assertJson([
                'domain' => $template->domain,
                'xpath_name' => $template->xpath_name,
                'price_query' => $template->price_query,
                'price_query_type' => $template->price_query_type,
                'stock_query' => $template->stock_query,
                'stock_query_type' => $template->stock_query_type,
                'stock_text' => $template->stock_text,
                'stock_condition' => $template->stock_condition,
            ]);
    }
}
