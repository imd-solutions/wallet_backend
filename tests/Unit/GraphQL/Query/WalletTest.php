<?php

namespace Tests\Unit\GraphQL\Query;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MarvinRabe\LaravelGraphQLTest\TestGraphQL;
use Tests\TestCase;
use Tests\Fragments;

class WalletTest extends TestCase
{
    use RefreshDatabase, TestGraphQL, Fragments;

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->signIn();

        $this->assertCount(1, $this->user::all());

        $name = explode(' ', $this->user->name);

        $this->user->profile()->save(Profile::factory()->create([
            'user_id' => $this->user->id,
            'firstname' => $name[0],
            'lastname' => $name[1],
            'balance' => 125.00
        ]));
    }

    /**
     * Test Case: Can get the users balance.
     * @test
     * @group gqlQueryWallet
     * @return void
     */
    public function canGetUsersWalletBalance()
    {
        $this->withExceptionHandling();
        $method = 'userBalance';
        $input = [
            'id' => $this->user->id,
        ];

        $response = $this->query($method, ['input' => $input], $this->userBalanceFragment())
        ->assertJsonStructure([
            'data' => [
                $method => [
                    'balance'
                ]
            ]
        ]);

        $response->assertSee($this->encodeJsonResult($response['data'][$method]));
    }
}
