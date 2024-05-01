<?php

namespace Tests\Feature\Liaison;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Broker;
use App\Models\Writer;
use App\Models\Liaisonrequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Facade;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

class LiaisonRequestToWriterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function createUser() 
    {
        $user_details = User::factory() -> make(['pass' => 'password']);

        $registered_user = $this->post('/api/register', $user_details -> toArray());

        return $registered_user;
    }

    public function createToken()
    {
        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());
        return ['token' => $response->decodeResponseJson()['token'], 'id' => $response ->decodeResponseJson()['user']['id']];
    }

    public function test_liaison_request_sent_to_writer()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make(['pass' => 'password']);

        $create_writer_response = $this->post('/api/register', $user -> toArray());
        $create_broker_response = $this -> createToken();
        $writer_id = User::find($create_writer_response['user']['id']) -> writer -> id;

        $token = 'Bearer ' . $create_broker_response['token'];
        
        $liaison_request_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', '/api/liaison/request/writer', [
            'writer_id' => $writer_id,
        ]);
        // dd($liaison_request_response);


        $liaison_request_response->assertStatus(200);
        $this -> assertSame($liaison_request_response['liaison_request']['writer_id'], $writer_id);
        $this -> assertSame($liaison_request_response['liaison_request']['broker_id'], User::find($create_broker_response['id']) -> broker -> id);
    }

    public function test_writer_can_reject_request()
    {
        $this -> withoutExceptionHandling();
        
        $user = User::factory() -> make(['pass' => 'password']);

        $create_writer_response = $this->post('/api/register', $user -> toArray());
        $create_broker_response = $this -> createToken();
        $writer_id = User::find($create_writer_response['user']['id']) -> writer -> id;

        $token = 'Bearer ' . $create_broker_response['token'];
        
        $liaison_request_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', '/api/liaison/request/writer', [
            'writer_id' => $writer_id,
        ]);

        $liaison_request_response->assertStatus(200);
        $this -> assertSame($liaison_request_response['liaison_request']['writer_id'], $writer_id);

        //writer token

        $writer_token = 'Bearer ' . $create_writer_response['token'];

        $reject_liaison_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $writer_token,
        ])->json('POST', '/api/liaison/request/reject', [
            'broker_id' => $liaison_request_response['liaison_request']['broker_id'],
            'base' => 'reject_request_from_broker'
        ]);

        $reject_liaison_response->assertStatus(200);

    }

    public function sendRequestFromBrokerToWriter($writer_id, $broker_token)
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $broker_token,
        ])->json('POST', '/api/liaison/request/writer', [
            'writer_id' => $writer_id,
        ]);
    }
    
    public function test_writer_can_accept_request()
    {
        $this -> withoutExceptionHandling();
        
        /* 
            Create two accounts
            One for the writer, another for the broker
        */
        $create_writer_response = $this-> createUser();
        $create_broker_response = $this-> createUser();

        $writer_id = User::find($create_writer_response['user']['id']) -> writer -> id;
        $broker_id = User::find($create_broker_response['user']['id']) -> broker -> id;

        $writer_token = 'Bearer ' . $create_writer_response['token'];
        $broker_token = 'Bearer ' . $create_broker_response['token'];

        // Send request from broker to writer
        $liaison_request_response = $this-> sendRequestFromBrokerToWriter($writer_id, $broker_token);

        $liaison_request_response->assertStatus(200);
        
        $this -> assertSame($liaison_request_response['liaison_request']['writer_id'], $writer_id);
        $this -> assertSame($liaison_request_response['liaison_request']['broker_id'], $broker_id);

        $this->refreshApplication();

        // Accept broker's request as writer
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

    }

    public function test_writer_can_fetch_brokers_to_request()
    {
        User::truncate();
        Writer::truncate();
        Broker::truncate();

        $number_of_brokers = 5;

        // Create Brokers
        for ($i=0; $i < $number_of_brokers; $i++) { 
            $broker = $this-> createUser();
        }
        // Create Writer
        $writer = $this-> createUser();
        $writer_token = $writer["token"];

        // Get All Brokers
        $all_brokers_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer_token,
        ])->json('POST', '/api/liaison/get_all_brokers', []);

        $all_brokers_response->assertStatus(200);
        $this -> assertEquals($number_of_brokers, count($all_brokers_response["brokers"]));
        // dd();
    }

    public function test_writer_can_fetch_requests_from_brokers()
    {
        Liaisonrequest::truncate();
        // Create Writer
        $writer = $this-> createUser();
        $writer_id = $writer['user']['writer']['id'];

        // Create Broker and send request to writer * 5

        $broker_tokens = array();
        $number_of_requests = 6;

        for ($i=0; $i < $number_of_requests; $i++) { 
            $broker = $this -> createUser();
            array_push($broker_tokens, 'Bearer ' . $broker['token']);
            $this->refreshApplication();
        }

        foreach ($broker_tokens as $broker_token) {
            $liaison_request_response = $this -> sendRequestFromBrokerToWriter($writer_id, $broker_token);
            $liaison_request_response->assertStatus(200);
            $this->refreshApplication();
        }

        $this -> assertEquals(Liaisonrequest::count(), $number_of_requests);

        // Fetch requests

        $requests_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer["token"],
        ])->json('GET', '/api/liaison/requests/get_all');
        $requests_response->assertStatus(200);

        $this -> assertEquals(count($requests_response["requests"]["brokers_requests"]), $number_of_requests);
    }
    
    public function test_writer_can_fetch_brokers_in_their_network()
    {
        Liaisonrequest::truncate();
        DB::table('broker_writer') -> truncate();

        // Create Writer
        $writer = $this-> createUser();
        $writer_id = $writer['user']['writer']['id'];
        $writer_token = $writer["token"];

        // Create Broker and send request to writer * number_of_requests
        $brokers = array();
        $number_of_requests = 6;

        for ($i=0; $i < $number_of_requests; $i++) { 
            $broker = $this -> createUser();
            $broker = $broker;
            array_push($brokers, $broker);
            $this->refreshApplication();
        }

        // Send requests from brokers to writer
        foreach ($brokers as $broker) {
            $liaison_request_response = $this -> sendRequestFromBrokerToWriter($writer_id, 'Bearer ' . $broker["token"]);
            $liaison_request_response->assertStatus(200);

            $this->refreshApplication();

            $accept_liaison_response = $this->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $writer_token,
            ])->json('POST', '/api/liaison/request/accept', [
                'broker_id' => $broker['user']['broker']['id'],
                'base' => 'attach_broker_to_me'
            ]);

            $accept_liaison_response->assertStatus(200);
        }

        $this -> assertEquals(Liaisonrequest::count(), $number_of_requests);

        $get_brokers_belonging_to_writer = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer_token,
        ])->json('GET', '/api/liaison/get_my_brokers');

        $get_brokers_belonging_to_writer->assertStatus(200);

        $number_of_brokers = count($get_brokers_belonging_to_writer["brokers"]);
        $this -> assertEquals($number_of_brokers, $number_of_requests);
        
    }

    // public function test_writer_can_fetch_request_messages_and_files(){}
    
    public function test_writer_can_send_request_text_message()
    {
        DB::table('requestmessages') -> truncate();

        //  Create Writer and Broker
        $writer = $this-> createUser();
        $writer_id = $writer['user']['writer']['id'];

        $broker = $this -> createUser();
        $this->refreshApplication();

        // Send Request From Broker To Writer
        $liaison_request_response = $this -> sendRequestFromBrokerToWriter($writer_id, 'Bearer ' . $broker["token"]);
        $liaison_request_response->assertStatus(200);

        // Send Message to broker request chat
        $message = "The time is " . Carbon::now();
        $send_message_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer["token"],
        ])->json('POST', '/api/liaison/request/send_message', [
            'request_id' => $liaison_request_response["liaison_request"]["id"],
            'message' => $message
        ]);

        $send_message_response->assertStatus(200);

        $this -> assertEquals($message, $send_message_response["message"]["message"]);
    }

    public function test_writer_can_send_request_files()
    {
        $this -> withoutExceptionHandling();

        //   Create Writer and Broker
        $writer = $this-> createUser();
        $writer_id = $writer['user']['writer']['id'];
        $writer_token = $writer["token"];

        $broker = $this-> createUser();
        $broker_token = $broker['token'];
        
        // Request Writer as Broker
        $liaison_request_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker_token,
        ])->json('POST', '/api/liaison/request/writer', [
            'writer_id' => $writer_id
        ]);

        $liaison_request_response -> assertStatus(200);

        $liaison_request_id = $liaison_request_response["liaison_request"]["id"];

        $this->refreshApplication();

        // Create Fake Files
        $documents = [];

        for ($i=0; $i < 5; $i++) { 
            array_push($documents, UploadedFile::fake()->image('avatar' . $i .'.jpg'));
        }

        // Fetch Request as Writer
        $send_message_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer_token,
        ])->json('POST', '/api/liaison/request/send_message', [
            'documents' => $documents,
            'request_id' => $liaison_request_response["liaison_request"]["id"]
        ]);

        $send_message_response -> assertStatus(200);
    }
}
