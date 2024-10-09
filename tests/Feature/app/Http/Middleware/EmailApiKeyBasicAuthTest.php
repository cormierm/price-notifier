<?php

namespace Tests\Feature\App\Http\Middleware;

use App\Http\Middleware\EmailApiKeyBasicAuth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class EmailApiKeyBasicAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itWillThrowsUnauthorizedExceptionForNoCredentials(): void
    {
        $request = new Request();

        $middleware = $this->app->make(EmailApiKeyBasicAuth::class);

        $response = $middleware->handle($request, function () {
        });

        TestResponse::fromBaseResponse($response)->assertStatus(401);
    }

    /** @test */
    public function itWillReturnUnauthorizedForInvalidCredentials(): void
    {
        $user = User::factory()->create();

        $request = new Request();
        $request->headers->set('Authorization', "Basic " . base64_encode("{$user->email}:not-the-api-key"));

        $middleware = $this->app->make(EmailApiKeyBasicAuth::class);

        $response = $middleware->handle($request, function () {
        });

        TestResponse::fromBaseResponse($response)->assertStatus(401);
    }

    /** @test */
    public function itWilCallNextForValidCredentials(): void
    {
        $user = User::factory()->create();

        $request = new Request();
        $request->headers->set('Authorization', "Basic " . base64_encode("{$user->email}:{$user->api_key}"));

        $middleware = $this->app->make(EmailApiKeyBasicAuth::class);

        $response = $middleware->handle($request, function () {

            return new Response('Next was called.');
        });

        $this->assertEquals('Next was called.', $response->content());
    }
}
