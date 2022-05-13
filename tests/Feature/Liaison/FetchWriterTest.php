<?php

namespace Tests\Feature\Liaison;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class FetchWriterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this -> withoutExceptionHandling();

        $token = 'Bearer ' . $this -> createToken();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', '/api/liaison/get_all_writers', []);

        $response->assertStatus(200);
    }
    
    public function createToken()
    {
        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());
        
        return $response->decodeResponseJson()['token'];
    }
}
