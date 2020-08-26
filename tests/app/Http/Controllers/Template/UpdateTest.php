<?php

namespace Tests\App\Http\Controllers\Template;

use App\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanUpdateWatcher(): void
    {
        $template = factory(Template::class)->create();

        $data = [
            'domain' => 'Foobar template',
            'xpath_name' => 'some-query-name',
            'xpath_value' => 'some-query-value',
            'client' => 'curl'
        ];

        $this->actingAs($template->user)
            ->put(route('template.update', $template), $data)
            ->assertSuccessful();

        $this->assertDatabaseHas('templates', array_merge(
            ['id' => $template->id],
            $data
        ));
    }
}
