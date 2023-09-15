<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use App\Models\User;

class VerificationTest extends TestCase
{
    public function test_correct_inputs_email_verification_successful()
    {
        $user = User::factory() -> create(['email_verification' => 'jhgfghjkjhghuijHGujHUKiuHjkjHjuyt567ujhKiu']);

        $response = $this->post('/api/verify_email', $user -> toArray());

        $response->assertStatus(200);
    }

    public function test_incorrect_inputs_email_verification_fails()
    {
        $user = User::factory() -> create(['email_verification' => 'jhgfghjkjhghuijHGujHUKiuHjkjHjuyt567ujhKiu']);

        $user -> email_verification = "another_value";

        $response = $this->post('/api/verify_email', $user -> toArray());

        $response->assertStatus(201);
    }

    public function test_email_already_verified_verification_fails()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> create();

        $verification_response = $this->post('/api/verify_email', $user -> toArray());
        $verification_response->assertStatus(202);
    }
}
