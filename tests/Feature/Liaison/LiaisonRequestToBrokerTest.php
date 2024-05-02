<?php

namespace Tests\Feature\Liaison;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

use App\Models\Broker;
use App\Models\Liaisonrequest;
use App\Models\User;
use App\Models\Writer;

class LiaisonRequestToBrokerTest extends TestCase
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
    
    public function sendRequestFromWriterToBroker($broker_id, $writer_token)
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $writer_token,
        ])->json('POST', '/api/liaison/request/broker', [
            'broker_id' => $broker_id,
        ]);
    }

    public function test_liaison_request_sent_to_broker()
    {
        $this -> withoutExceptionHandling();

        // Create Broker 
        $broker = $this-> createUser();
        $broker_id = $broker["user"]["broker"]["id"];

        // Create Writer
        $writer = $this -> createUser();
        $writer_id = $writer["user"]["writer"]["id"];

        // Send Request to Broker
        $liaison_request_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer["token"],
        ])->json('POST', '/api/liaison/request/broker', [
            'broker_id' => $broker_id,
        ]);

        $liaison_request_response -> assertStatus(200);
        
        $this -> assertSame($liaison_request_response['liaison_request']['writer_id'], $writer_id);
        $this -> assertSame($liaison_request_response['liaison_request']['broker_id'], $broker_id);
    }

    public function test_broker_can_reject_request()
    {
        // Create Broker 
        $broker = $this-> createUser();
        $broker_id = $broker["user"]["broker"]["id"];

        // Create Writer
        $writer = $this -> createUser();
        $writer_id = $writer["user"]["writer"]["id"];

        // Send Request to Broker
        $liaison_request_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer["token"],
        ])->json('POST', '/api/liaison/request/broker', [
            'broker_id' => $broker_id,
        ]);

        $liaison_request_response -> assertStatus(200);
        
        $this -> assertSame($liaison_request_response['liaison_request']['writer_id'], $writer_id);
        $this -> assertSame($liaison_request_response['liaison_request']['broker_id'], $broker_id);

        $this->refreshApplication();

        // Get Request as Broker
        $reject_liaison_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', '/api/liaison/request/reject', [
            'writer_id' => $liaison_request_response['liaison_request']['writer_id'],
        ]);

        $reject_liaison_response->assertStatus(200);

        $liaison_request = DB::table('liaisonrequests') -> where([
            'writer_id' => $writer_id,
            'broker_id' => $broker_id,
        ]) -> first();

        $this -> assertEquals($liaison_request -> status, 3);

    }

    public function test_broker_can_accept_request()
    {
        // Create Broker 
        $broker = $this-> createUser();
        $broker_id = $broker["user"]["broker"]["id"];

        // Create Writer
        $writer = $this -> createUser();
        $writer_id = $writer["user"]["writer"]["id"];

        // Send Request to Broker
        $liaison_request_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer["token"],
        ])->json('POST', '/api/liaison/request/broker', [
            'broker_id' => $broker_id,
        ]);

        $liaison_request_response -> assertStatus(200);
        
        $this -> assertSame($liaison_request_response['liaison_request']['writer_id'], $writer_id);
        $this -> assertSame($liaison_request_response['liaison_request']['broker_id'], $broker_id);

        $this->refreshApplication();

        // Get Request as Broker
        $acceptt_liaison_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', '/api/liaison/request/accept', [
            'writer_id' => $liaison_request_response['liaison_request']['writer_id'],
        ]);

        $acceptt_liaison_response->assertStatus(200);

        $this->assertEquals(true,  DB::table('broker_writer') -> where([
                ['writer_id', $writer_id], 
                ['broker_id', $broker_id]
            ]) -> exists()
        );


        $liaison_request = DB::table('liaisonrequests') -> where([
            'writer_id' => $writer_id,
            'broker_id' => $broker_id,
        ]) -> first();

        $this -> assertEquals($liaison_request -> status, 4);

    }

    public function test_broker_can_fetch_writers_to_request()
    {
        User::truncate();
        Writer::truncate();
        Broker::truncate();

        $number_of_writers = 5;

        // Create Writers
        for ($i=0; $i < $number_of_writers; $i++) { 
            $writer = $this-> createUser();
        }

        // Create Broker
        $broker = $this-> createUser();
        $broker_token = $broker["token"];

        // Get All Writers
        $all_writers_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker_token,
        ])->json('POST', '/api/liaison/get_all_writers', []);

        $all_writers_response->assertStatus(200);
        $this -> assertEquals($number_of_writers, count($all_writers_response["writers"]));

    } 

    public function test_broker_can_fetch_requests_from_writers()
    {
        Liaisonrequest::truncate();

        // Create Broker
        $broker = $this-> createUser();
        $broker_id = $broker['user']['broker']['id'];

        // Create Writer and send request to Broker * 5

        $writer_tokens = array();
        $number_of_requests = 6;

        for ($i=0; $i < $number_of_requests; $i++) { 
            $writer = $this -> createUser();
            array_push($writer_tokens, 'Bearer ' . $writer['token']);
            $this->refreshApplication();
        }

        foreach ($writer_tokens as $writer_token) {
            $liaison_request_response = $this -> sendRequestFromWriterToBroker($broker_id, $writer_token);
            $liaison_request_response->assertStatus(200);
            $this->refreshApplication();
        }

        $this -> assertEquals(Liaisonrequest::count(), $number_of_requests);

        // Fetch requests

        $requests_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('GET', '/api/liaison/requests/get_all');
        $requests_response->assertStatus(200);

        $this -> assertEquals(count($requests_response["requests"]["writers_requests"]), $number_of_requests);
    }
    
    public function test_broker_can_fetch_writers_in_their_network()
    {
        Liaisonrequest::truncate();
        DB::table('broker_writer') -> truncate();

        // Create Broker
        $broker = $this-> createUser();
        $broker_id = $broker['user']['broker']['id'];
        $broker_token = $broker["token"];

        // Create Writer and send request to Broker * number_of_requests
        $writers = array();
        $number_of_requests = 6;

        for ($i=0; $i < $number_of_requests; $i++) { 
            $writer = $this -> createUser();
            $writer = $writer;
            array_push($writers, $writer);
            $this->refreshApplication();
        }

        // Send requests from writers to broker
        foreach ($writers as $writer) {
            $liaison_request_response = $this -> sendRequestFromWriterToBroker($broker_id, 'Bearer ' . $writer["token"]);
            $liaison_request_response->assertStatus(200);

            $this->refreshApplication();

            $accept_liaison_response = $this->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $broker_token,
            ])->json('POST', '/api/liaison/request/accept', [
                'writer_id' => $writer['user']['writer']['id'],
            ]);

            $accept_liaison_response->assertStatus(200);
        }

        $this -> assertEquals(Liaisonrequest::count(), $number_of_requests);

        $get_writers_belonging_to_broker = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker_token,
        ])->json('GET', '/api/liaison/get_my_writers');

        $get_writers_belonging_to_broker->assertStatus(200);

        $number_of_writers = count($get_writers_belonging_to_broker["writers"]);
        $this -> assertEquals($number_of_writers, $number_of_requests);
        
    }

    // public function test_broker_can_fetch_request_messages_and_files(){}

    public function test_broker_can_send_request_text_message()
    {
        DB::table('requestmessages') -> truncate();

        //  Create Writer and Broker
        $broker = $this-> createUser();
        $broker_id = $broker['user']['broker']['id'];

        $writer = $this -> createUser();
        $this->refreshApplication();

        // Send Request From Writer To Broker
        $liaison_request_response = $this -> sendRequestFromWriterToBroker($broker_id, 'Bearer ' . $writer["token"]);
        $liaison_request_response->assertStatus(200);

        // Send Message to broker request chat
        $message = "The time is " . Carbon::now();
        $send_message_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', '/api/liaison/request/send_message', [
            'request_id' => $liaison_request_response["liaison_request"]["id"],
            'message' => $message
        ]);

        $send_message_response->assertStatus(200);

        $this -> assertEquals($message, $send_message_response["message"]["message"]);
    }

    public function test_broker_can_send_request_files()
    {
        $this -> withoutExceptionHandling();

        //   Create Writer and Broker
        $broker = $this-> createUser();
        $broker_id = $broker['user']['broker']['id'];
        $broker_token = $broker["token"];

        $writer = $this-> createUser();
        $writer_token = $writer['token'];
        
        // Request Broker as Writer
        $liaison_request_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer_token,
        ])->json('POST', '/api/liaison/request/broker', [
            'broker_id' => $broker_id
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
            'Authorization' => 'Bearer ' . $broker_token,
        ])->json('POST', '/api/liaison/request/send_message', [
            'documents' => $documents,
            'request_id' => $liaison_request_response["liaison_request"]["id"]
        ]);

        $send_message_response -> assertStatus(200);
    }
}
