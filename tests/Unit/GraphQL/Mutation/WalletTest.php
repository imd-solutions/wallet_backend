<?php

namespace Tests\Unit\GraphQL\Mutation;

use Tests\Fragments;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MarvinRabe\LaravelGraphQLTest\TestGraphQL;

class WalletTest extends TestCase
{
    use RefreshDatabase, TestGraphQL, Fragments;

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->signIn();
    }

    /**
     * Test Case: User can generate a token.
     * @test
     * @group gqlMutationWallet
     */
    public function canGenerateToken()
    {
        $method = 'walletLogin';
        $input = [
            'email' => 'a.cioltei@editec.co',
            'password' => 'AtlasAlin',
            'transaction_id' => 'A123456',
        ];

        $response = $this->mutation($method, ['input' => $input], $this->atlasLoginResponseFragment())
        ->assertJsonStructure([
                'data' => [
                    $method => array(
                        'success',
                        'token'
                    )
                ]
            ]);

        $response->assertSee($this->encodeJsonResult($response['data'][$method]));
    }

    /**
     * Test Case: User can top up their wallet.
     * @test
     * @group gqlMutationWallet
     * @return void
     */
    public function canTopUpWallet()
    {
        $method = 'walletPaymentForm';
        $input = [
            'token' => 'A1234567890',
            'country_id' => 1,
            'customer_id' => 1,
            'language' => 'english',
            'amount' => '100.00',
            'transaction_id' => 584514,
            'logo_url' => 'https://www.test.com',
            'background_colour' => 'red',
            'callback_url' => 'https://www.test.com/callback',
            'error_url' => 'https://www.test.com/error',
            'success_url' => 'https://www.test.com/success',
            "extra_properties" => [
                "bonus" => "1",
                "bonus_text" => "100% up to 10000 XAF"
            ]
        ];
        $response = $this->mutation($method, ['input' => $input], $this->atlasFormResponseFragment())
        ->assertJsonStructure([
            'data' => [
                $method => [
                    'status',
                    'html'
                ]
            ]
        ]);

        $response->assertSee($this->encodeJsonResult($response['data'][$method]));
    }

}
