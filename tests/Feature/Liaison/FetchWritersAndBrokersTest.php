<?php

namespace Tests\Feature\Liaison;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class FetchWritersAndBrokersTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_fetch_writers()
    {
        $this -> withoutExceptionHandling();
        User::truncate();

        $number_of_users_to_be_created = 5;
        for($i=0; $i < $number_of_users_to_be_created; $i++ ){
            $user = User::factory() -> create();
        }

        $token = 'Bearer ' . $this -> createToken();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', '/api/liaison/get_all_writers', []);

        $response->assertStatus(200);

        $this -> assertEquals(count($response["writers"]), $number_of_users_to_be_created);
    }
    
    public function test_user_can_fetch_brokers()
    {
        $this -> withoutExceptionHandling();
        User::truncate();

        $number_of_users_to_be_created = 5;
        for($i=0; $i < $number_of_users_to_be_created; $i++ ){
            $user = User::factory() -> create();
        }

        $token = 'Bearer ' . $this -> createToken();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', '/api/liaison/get_all_brokers', []);

        $response->assertStatus(200);

        $this -> assertEquals(count($response["brokers"]), $number_of_users_to_be_created);
    }
    
    public function createToken()
    {
        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());
        
        return $response->decodeResponseJson()['token'];
    }
}
