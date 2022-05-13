<?php

namespace Tests\Feature\Task;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;


class FetchTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_all_posted_by_me_successfully_fetched()
    {
        $this -> withoutExceptionHandling();

        $user = User::find(1);
        $token = 'Bearer ' . $user->createToken(env('APP_NAME'))-> accessToken;

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('GET', '/api/task/get_availability_details');

        // $response = $this->get('/api/offer/accept');

        dd($response);
        $response->assertStatus(200);
    }
}
