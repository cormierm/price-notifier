<?php

namespace Tests\Feature\App\Http\Controllers\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itShowsViewWithUser(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.index'))
            ->assertSuccessful()
            ->assertViewIs('profile.index');
    }
}
