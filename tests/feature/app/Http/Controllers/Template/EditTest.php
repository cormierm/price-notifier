<?php

namespace Tests\Feature\App\Http\Controllers\Template;

use App\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itShowViewWithTemplate(): void
    {
        $template = Template::factory()->create();

        $this->actingAs($template->user)->get(route('template.edit', $template))
            ->assertSuccessful()
            ->assertViewIs('template.edit')
            ->assertViewHas('template', $template);
    }
}
