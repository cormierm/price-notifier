<?php

namespace Tests\App\Http\Controllers\Template;

use App\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itShowViewWithTemplate(): void
    {
        $template = factory(Template::class)->create();

        $this->actingAs($template->user)->get(route('template.edit', $template))
            ->assertSuccessful()
            ->assertViewIs('template.edit')
            ->assertViewHas('template', $template);
    }
}
