<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Tests\TestCase;

class LoginTest extends TestCase
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
     * Test Case: User can access home page unless verified.
     * @test
     * @group featureUserLogin
     * @return void
     */
    public function userCannotAccessPageUntilVerified()
    {
        $this->user = $this->signIn();
        $this->get('/home')->assertRedirect('/otp-verify');
    }


    /**
     * Test Case: User can access home page unless verified.
     * @test
     * @group featureUserLogin
     * @return void
     */
    public function userCanAccessPageWhenVerified()
    {
        $this->user = $this->signIn(User::factory()->create(['isVerified' => 1]));
        $this->get('/home')->assertStatus(200);
    }

    /**
     * Test Case: User can access endpoint when verified.
     * @test
     * @group featureUserLogin
     * @lang GraphQL
     * @return void
     */
    public function userCanAccessEndPoint()
    {
        $this->user = $this->signIn();
        $this->graphQL( '
        {
            users {
                id
                name
            }
        }
        ')->assertJson([
            'data' => [
                'users' => [
                    [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                    ],
                ],
            ],
        ]);
    }


    /**
     * Test Case: User can not access endpoint unless verified.
     * @test
     * @group featureUserLogin
     * @lang GraphQL
     * @return void
     */
    public function userCanNotAccessEndPointUnlessVerified()
    {
        $expectedMessage = 'Unauthenticated.';
        $this->graphQL('
        {
            users {
                id
            }
        }
        ')->assertGraphQLErrorMessage($expectedMessage);
    }
}
