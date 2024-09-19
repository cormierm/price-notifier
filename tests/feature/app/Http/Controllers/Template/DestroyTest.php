<?php

namespace Tests\Feature\App\Http\Controllers\Template;

use App\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDeletesTemplate(): void
    {
        $template = Template::factory()->create();

        $this->actingAs($template->user)
            ->delete(route('template.destroy', $template))
            ->assertSuccessful();

        $this->assertDatabaseMissing('templates', [
            'id' => $template->id,
            'domain' => $template->domain
        ]);
    }
}
