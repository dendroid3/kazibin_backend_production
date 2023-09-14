<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class VerificationTest extends TestCase
{
    public function test_correct_inputs_email_verification_successful()
    {
        $user = User::factory() -> create();

        $response = $this->post('/api/verify_email', $user -> toArray());

        $response->assertStatus(200);
    }

    public function test_incorrect_inputs_email_verification_fails()
    {
        $user = User::factory() -> create();

        $user->email='not@this.com';

        $response = $this->post('/api/verify_email', $user -> toArray());

        $response->assertStatus(201);
    }

    public function test_email_already_verified_verification_fails()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> create(['email_verification' => 'jhgfghjkjhghuijHGujHUKiuHjkjHjuyt567ujhKiu']);
        $registered_user = User::find($user -> id);
        $registered_user -> email_verification = null; # User::find($user -> id);
        $registered_user -> push();
        $verification_response = $this->post('/api/verify_email', $user -> toArray());
        $verification_response->assertStatus(202);
    }
}
