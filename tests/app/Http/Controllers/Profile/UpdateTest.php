<?php

namespace Tests\App\Http\Controllers\Profile;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanUpdateUserPushoverCredentials(): void
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();

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
}
