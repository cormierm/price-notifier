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

    /** @test */
    public function itCanUpdatePhoneNumber()
    {
        $user = factory(User::class)->create();

        $data = [
            'phone_number' => '+19051234567'
        ];

        $this->actingAs($user)
            ->put(route('profile.update'), $data)
            ->assertSuccessful();

        $this->assertEquals($data['phone_number'], $user->fresh()->phone_number);
    }

    /** @test */
    public function itCanClearPhoneNumber()
    {
        $user = factory(User::class)->create([
            'phone_number' => '+19056664444',
        ]);

        $data = [
            'phone_number' => null,
        ];

        $this->assertNotNull($user->phone_number);

        $this->actingAs($user)
             ->put(route('profile.update'), $data)
             ->assertSuccessful();

        $this->assertNull($user->fresh()->phone_number);

    }

    /** @test */
    public function itCanUpdateApiKey(): void
    {
        $user = factory(User::class)->create([
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
