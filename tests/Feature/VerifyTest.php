<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class VerifyTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test Case: user can submit OTP to get verified.
     * @test
     * @group featureUserVerify
     * @return void
     */
    public function userCanSeeVerifyPage()
    {
        $user = $this->signIn();
        $this->get('/otp-verify')->assertStatus(200);
    }

    /**
     * Test Case: user can submit OTP to get verified.
     * @test
     * @group featureUserVerify
     * @return void
     */
    public function userSubmitsOTPToGetVerified()
    {
        $user = $this->signIn();
        $otp = $user->cacheOTP();
        $this->post('/verify-otp', ['otp' => $otp])->assertStatus(302);
        $this->assertEquals($user->isVerified, true);
    }

    /**
     * Test Case: user will get notified for an invalid OTP.
     * @test
     * @group featureUserVerify
     * @return void
     */
    public function userNotifiedOfInvalidOTP()
    {
        $user = $this->signIn();
        $this->post('/verify-otp', ['otp' => 'xxx'])->assertSessionHasErrors();
    }

    /**
     * Test Case: user will get notified of missing OTP when submitting form.
     * @test
     * @group featureUserVerify
     * @return void
     */
    public function userNotifiedOfMissingOTPOnSubmit()
    {
        $user = $this->signIn();
        $this->post('/verify-otp', ['otp' => null])->assertSessionHasErrors();
    }


}
