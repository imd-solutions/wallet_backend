<?php

namespace Tests\Feature;

use App\Mail\UserOTPMail;
use App\Models\User;
use App\Notifications\UserOTPNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test Case: Email sent to user after login.
     * @test
     * @group featureUserEmail
     * @return void
     */
    public function userSentEmailAfterLogin()
    {
        Notification::fake();
        $user = $this->signIn();
        $user->cacheOTP();
        $user->sendUserOTP('via_email');
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        Notification::assertSentTo($user, UserOTPNotification::class);
    }

    /**
     * Test Case: Email not sent to user if incorrect login.
     * @test
     * @group featureUserEmail
     * @return void
     */
    public function userNotSentEmailWithIncorrectLogin()
    {
        Mail::fake();
        $user = $this->signIn();
        $this->post('/login', ['email' => $user->email, 'password' => 'sdfsdfsdf']);
        Mail::assertNotSent(UserOTPMail::class);
    }

    /**
     * Test Case: Email sent to user after login.
     * @test
     * @group featureUserEmail
     * @return void
     */
    public function theOTPIsStoredWhenLogin()
    {
        $user = $this->signIn();
        $user->cacheOTP();
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->assertNotNull($user->otp());
    }
}
