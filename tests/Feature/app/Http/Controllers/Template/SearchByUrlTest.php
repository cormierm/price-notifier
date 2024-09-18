<?php

namespace Tests\App\Http\Controllers\Template;

use App\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchByUrlTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanFindTemplateByDomain(): void
    {
        $url = 'https://foobar.com/some/url/product.html';
        $template = Template::factory()->create([
            'domain' => 'foobar.com',
        ]);

        $this->actingAs($template->user)->post(route('template.search-by-url'), [
            'url' => $url
        ])
            ->assertSuccessful()
            ->assertJson([
                'domain' => $template->domain,
                'price_query' => $template->price_query,
                'price_query_type' => $template->price_query_type,
                'stock_query' => $template->stock_query,
                'stock_query_type' => $template->stock_query_type,
                'stock_text' => $template->stock_text,
                'stock_condition' => $template->stock_condition,
            ]);
    }
}
