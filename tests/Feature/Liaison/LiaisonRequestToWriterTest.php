<?php

namespace Tests\Feature\Liaison;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Facade;

class LiaisonRequestToWriterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_liaison_request_sent_to_writer()
    // {
    //     $this -> withoutExceptionHandling();

    //     $user = User::factory() -> make(['pass' => 'password']);

    //     $create_writer_response = $this->post('/api/register', $user -> toArray());
    //     $create_broker_response = $this -> createToken();
    //     $writer_id = User::find($create_writer_response['user']['id']) -> writer -> id;

    //     $token = 'Bearer ' . $create_broker_response['token'];
        
    //     $liaison_request_response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => $token,
    //     ])->json('POST', '/api/liaison/request/writer', [
    //         'writer_id' => $writer_id,
    //     ]);
    //     // dd($liaison_request_response);


    //     $liaison_request_response->assertStatus(200);
    //     $this -> assertSame($liaison_request_response['liaison_request']['writer_id'], $writer_id);
    //     $this -> assertSame($liaison_request_response['liaison_request']['broker_id'], User::find($create_broker_response['id']) -> broker -> id);
    // }

    // public function test_writer_can_reject_request()
    // {
    //     $this -> withoutExceptionHandling();
        
    //     $user = User::factory() -> make(['pass' => 'password']);

    //     $create_writer_response = $this->post('/api/register', $user -> toArray());
    //     $create_broker_response = $this -> createToken();
    //     $writer_id = User::find($create_writer_response['user']['id']) -> writer -> id;

    //     $token = 'Bearer ' . $create_broker_response['token'];
        
    //     $liaison_request_response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => $token,
    //     ])->json('POST', '/api/liaison/request/writer', [
    //         'writer_id' => $writer_id,
    //     ]);

    //     $liaison_request_response->assertStatus(200);
    //     $this -> assertSame($liaison_request_response['liaison_request']['writer_id'], $writer_id);

    //     //writer token

    //     $writer_token = 'Bearer ' . $create_writer_response['token'];

    //     $reject_liaison_response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => $writer_token,
    //     ])->json('POST', '/api/liaison/request/reject', [
    //         'broker_id' => $liaison_request_response['liaison_request']['broker_id'],
    //         'base' => 'reject_request_from_broker'
    //     ]);

    //     $reject_liaison_response->assertStatus(200);

    // }

    // public function test_writer_can_cancel_request(){}

    public function sendRequest($writer_id, $token){
        return $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', '/api/liaison/request/writer', [
            'writer_id' => $writer_id,
        ]);
    }

    public function test_writer_can_accept_request(){
        $this -> withoutExceptionHandling();
        
        $writer_user = User::factory() -> make(['pass' => 'password']);
        $broker_user = User::factory() -> make(['pass' => 'password']);

        $create_writer_response = $this->post('/api/register', $writer_user -> toArray());
        $create_broker_response = $this->post('/api/register', $broker_user -> toArray());

        $writer_id = User::find($create_writer_response['user']['id']) -> writer -> id;
        $broker_id = User::find($create_broker_response['user']['id']) -> broker -> id;

        $writer_token = 'Bearer ' . $create_writer_response['token'];
        $broker_token = 'Bearer ' . $create_broker_response['token'];
        
        $liaison_request_response = $this-> sendRequest($writer_id, $broker_token);

        $liaison_request_response->assertStatus(200);
        
        $this -> assertSame($liaison_request_response['liaison_request']['writer_id'], $writer_id);
        $this -> assertSame($liaison_request_response['liaison_request']['broker_id'], $broker_id);

        Log::info("writer_id");
        Log::info($writer_id);
        Log::info("broker_id");
        Log::info($broker_id);

        $accept_liaison_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $writer_token,
        ])->json('POST', '/api/liaison/request/accept', [
            'broker_id' => $liaison_request_response['liaison_request']['broker_id'],
            'base' => 'attach_broker_to_me'
        ]);

        $accept_liaison_response->assertStatus(200);

        $this->assertEquals(true,  DB::table('broker_writer') -> where([
                ['writer_id', $writer_id], 
                ['broker_id', $broker_id]
            ]) -> exists()
        );

       
        // );

    }

    // public function test_writer_can_fetch_brokers_to_request(){}

    // public function test_writer_can_fetch_requests_from_brokers(){}
    
    // public function test_writer_can_fetch_brokers_in_their_network(){}

    // public function test_writer_can_fetch_request_messages_and_files(){}
    
    // public function test_writer_can_send_request_message(){}

    // public function test_writer_can_send_request_files(){}
    //     $this -> withoutExceptionHandling();

    //     $user = User::factory() -> make(['pass' => 'password']);

    //     $create_writer_response = $this->post('/api/register', $user -> toArray());
    //     // dd(]);
    //     $create_broker_response = $this -> createToken();

    //     $token = 'Bearer ' . $create_broker_response['token'];
        
    //     $liaison_request_response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => $token,
    //     ])->json('POST', '/api/liaison/request/writer', [
    //         'writer_id' => $create_writer_response['user']['id'],
    //     ]);

    //     $fetch_requests_response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $create_writer_response['token'],
    //     ])->json('GET', '/api/liaison/requests/from_writers');

    //     dd($fetch_requests_response);

    // }

    public function createToken()
    {
        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());
        // dd($response ->decodeResponseJson()['user']['id']);
        return ['token' => $response->decodeResponseJson()['token'], 'id' => $response ->decodeResponseJson()['user']['id']];
    }
}
