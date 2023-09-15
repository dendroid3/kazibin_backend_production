<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\Concerns\ImpersonatesUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\User;

class LoginTest extends TestCase
{
     
    public function createToken()
    {
        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());

        return $response->decodeResponseJson()['token'];
    }

    public function test_wrong_credentials_entered_user_not_found_and_not_logged_in()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> create();

        $response = $this->post('/api/login', [
            'email' => $user -> email,
            'pass' => 'passwords'
        ]);

        $response->assertStatus(201);
    }

    public function test_correct_credentials_entered_user_found_loggged_in_and_token_given()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> create(['password' => Hash::make('password')]);

        $response = $this->post('/api/login', [
            'email' => $user -> email,
            'pass' => 'password'
        ]);
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_loggout()
    {
        $token = 'Bearer ' . $this -> createToken();

        $response=  $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->get('/api/logout');

        $response -> assertStatus(200);
    }

    public function test_unauthenticated_user_can_not_loggout()
    {
        $user = User::factory() -> create();

        $response= $this->get('/api/logout');

        $response -> assertStatus(500);
    }
}
