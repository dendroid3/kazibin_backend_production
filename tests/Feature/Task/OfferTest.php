<?php

namespace Tests\Feature\Task;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class OfferTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_example()
    // {
        
    //     $token = $this -> createToken();

    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => $token,
    //     ])->json('POST', '/api/offer/accept', [
    //         'offer_id' => 'd80d9167-f5c5-4325-87b6-9ad3533da82e',
    //     ]);

    //     // $response = $this->get('/api/offer/accept');

    //     $response->assertStatus(200);
    // }
    // public function test_example_2()
    // {
    //     $token = $this -> createToken();

    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => $token,
    //     ])->json('POST', '/api/offer/accept', [
    //         'offer_id' => 'd80d9167-f5c5-4325-87b6-9ad3533da82e',
    //     ]);

    //     // $response = $this->get('/api/offer/accept');

    //     $response->assertStatus(202);
    // }
    public function createToken()
    {
        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());

        return 'Bearer ' . $response->decodeResponseJson()['token'];
    }
}
