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
        $template = factory(Template::class)->create();

        $this->actingAs($template->user)->get(route('template.search', $template->domain))
            ->assertSuccessful()
            ->assertJson([
                'domain' => $template->domain,
                'xpath_name' => $template->xpath_name,
                'xpath_value' => $template->xpath_value,
                'xpath_stock' => $template->xpath_stock,
                'stock_text' => $template->stock_text,
                'stock_condition' => $template->stock_condition,
            ]);
    }
}
