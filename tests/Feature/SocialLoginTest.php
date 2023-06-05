<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;
    use MakesGraphQLRequests;
    use RefreshesSchemaCache;

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->bootRefreshesSchemaCache();

    }

    /**
     * Test Case: User can log into application with facebook sign in.
     * @test
     * @group featureSocialLogin
     * @return void
     */
    public function userCanLoginWithFacebook()
    {
        $response = $this->get('/auth-login/facebook');

        $response->assertStatus(302);
    }

    /**
     * Test Case: User is authenticated after facebook login.
     * @test
     * @group featureSocialLogin
     * @return void
     */
    public function userAuthenticatedAfterFacebookLogin()
    {
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('redirect')->andReturn('Redirected');

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $providerId = 1234567890;

        $abstractUser->shouldReceive('getId')
            ->andReturn($providerId)
            ->shouldReceive('getEmail')
            ->andReturn(Str::random(10).'@noemail.app')
            ->shouldReceive('getNickname')
            ->andReturn('Laztopaz')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('facebook')->andReturn($provider);

        User::factory()->create(['provider_id' => $providerId]);

        $response = $this->get('/home');
        $response->assertStatus(302);
    }
}
