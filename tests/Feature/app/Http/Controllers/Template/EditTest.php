<?php

namespace Tests\Feature\App\Http\Controllers\Template;

use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
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
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->component('Template/Form')
                ->has('template')
                ->where('template.id', $template->id));
    }
}
