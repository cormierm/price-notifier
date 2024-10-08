<?php

namespace Tests\Feature\App\Http\Controllers\Watcher;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanShowView(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('watcher.create'))
            ->assertSuccessful()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Watcher/Form')
            );
    }
}
