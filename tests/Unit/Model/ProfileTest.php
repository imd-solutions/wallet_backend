<?php

namespace Tests\Unit\Model;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
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
     * Test Case: User has a firstname.
     * @test
     * @group modelProfile
     * @return void
     */
    public function userHasProfileFirstname()
    {
        $name = explode(' ', $this->user->name);

        $this->user->profile()->create([
            'firstname' => $name[0],
            'lastname' => $name[1],
        ]);

        $this->assertEquals($this->user->profile->firstname, $name[0]);
    }

    /**
     * Test Case: User has a lastname.
     * @test
     * @group modelProfile
     * @return void
     */
    public function userHasProfileLastname()
    {
        $name = explode(' ', $this->user->name);

        $this->user->profile()->create([
            'firstname' => $name[0],
            'lastname' => $name[1],
        ]);

        $this->assertEquals($this->user->profile->lastname, $name[1]);

    }
}
