<?php

namespace Tests\App\Http\Controllers\Profile;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function itCanUpdateUserPushoverCredentials(): void
    {
        $user = User::factory()->create();

        $data = [
            'pushover_user_key' => 'ffffffffffoooooooooooooooooooo',
            'pushover_api_token' => 'bbbbbbbbbbaaaaaaaaaarrrrrrrrrr',
        ];

        $this->actingAs($user)
            ->put(route('profile.update'), $data)
            ->assertSuccessful();

        $this->assertEquals($data['pushover_api_token'], $user->fresh()->pushover_api_token);
        $this->assertEquals($data['pushover_user_key'], $user->fresh()->pushover_user_key);
    }

    /** @test */
    public function itCanUpdateApiKey(): void
    {
        $user = User::factory()->create([
            'api_key' => null,
        ]);

        $data = [
            'api_key' => $this->faker->uuid,
        ];

        $this->actingAs($user)
            ->put(route('profile.update'), $data)
            ->assertSuccessful();

        $this->assertEquals($data['api_key'], $user->fresh()->api_key);
    }
}
