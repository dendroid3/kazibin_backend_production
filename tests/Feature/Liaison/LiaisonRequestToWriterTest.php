<?php

namespace Tests\Feature\Liaison;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
class LiaisonRequestToWriterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_request_successfully_made()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make(['pass' => 'password']);

        $step_1_response = $this->post('/api/register', $user -> toArray());

        $token = 'Bearer ' . $this -> createToken();
        $step_2_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', '/api/writer/liaison/request', [
            'writer_id' => $step_1_response->decodeResponseJson()['user']['id'],
        ]);

        $step_2_response->assertStatus(200);
    }

    
    public function createToken()
    {
        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());
        
        return $response->decodeResponseJson()['token'];
    }
}
