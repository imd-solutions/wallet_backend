<?php

namespace Tests\Unit;

use App\Notifications\UserOTPNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->signIn();

        $this->assertCount(1, $this->user::all());

    }

    /**
     * Test Case: User can cache OTP.
     * @test
     * @group user
     * @return void
     */
    public function userCanCacheOTP()
    {
        $this->assertEquals($this->user->otpAuthKey(), "OTP_auth_{$this->user->id}");
    }

    /**
     * Test Case: User can receive an OTP notification.
     * @test
     * @group user
     * @return void
     */
    public function userCanReceiveOTPNotification()
    {
        Notification::fake();
        $this->user->sendUserOTP('via_email');
        Notification::assertSentTo($this->user, UserOTPNotification::class);
    }
}
