<?php

namespace Tests\App\Http\Controllers\Template;

use App\Http\Controllers\Template\SearchByUrl;
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
        $template = factory(Template::class)->create([
            'domain' => 'foobar.com',
        ]);

        $this->actingAs($template->user)->post(route('template.search-by-url'), [
            'url' => $url
        ])
            ->assertSuccessful()
            ->assertJson([
                'domain' => $template->domain,
                'xpath_value' => $template->xpath_value,
                'xpath_stock' => $template->xpath_stock,
                'stock_text' => $template->stock_text,
            ]);
    }
}
