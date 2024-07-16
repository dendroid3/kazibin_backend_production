<?php

namespace Tests\Feature\Task;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

use App\Models\User;
use App\Models\Taskoffer;
use App\Models\Task;

class OfferTest extends TestCase
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

    public function test_broker_can_offer_tasks_to_writer()
    {
        Taskoffer::truncate();

        // Create Broker
        $broker = $this -> createUser();

        $number_of_writers = 5;
        $takers = "";

        for($i=0; $i < $number_of_writers; $i++){
            ${'writer' . $i} = $this -> createUser();
            $takers .= ${'writer' . $i}["user"]["writer"]["id"] . "_";
        }

        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> push();

        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $task["id"],
            'takers' => $takers,
            'difficulty' => rand(2,9),
        ]);

        $step_6_response -> assertStatus(200);

        // Check Offers
        for($i=0; $i < $number_of_writers; $i++){
            $offer_exists = DB::table('taskoffers') -> where([
                'broker_id' => $broker["user"]["broker"]["id"],
                'task_id' => $task["id"],
                'writer_id' => ${'writer' . $i}["user"]["writer"]["id"],
                'status' => 1
            ]) -> exists();

            $this -> assertEquals(true, $offer_exists);
        }

    }

    public function test_broker_can_cancel_task_offer()
    {
        Taskoffer::truncate();

        // Create Broker
        $broker = $this -> createUser();

        $number_of_writers = 2;
        $takers = "";

        for($i=0; $i < $number_of_writers; $i++){
            ${'writer' . $i} = $this -> createUser();
            $takers .= ${'writer' . $i}["user"]["writer"]["id"] . "_";
        }

        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> push();

        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $task["id"],
            'takers' => $takers,
            'difficulty' => rand(2,9),
        ]);

        $step_6_response -> assertStatus(200);

        // Cancel All but One Offer
        for($i=0; $i < ($number_of_writers - 1); $i++){
            $offer_id = Taskoffer::query() -> where([
                'task_id' => $task["id"],
                'writer_id' => ${'writer' . $i}["user"]["writer"]["id"],
                'broker_id' => $broker["user"]["broker"]["id"],
                'status' => 1
            ]) -> value('id');

            $cancel_offer_response = $this->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $broker["token"],
            ])->json('POST', 'api/offer/cancel', [
                'offer_id' => $offer_id,
                'task_id' => $task["id"]
            ]);

            $cancel_offer_response -> assertStatus(200);

            $offer_exists = DB::table('taskoffers') -> where([
                'broker_id' => $broker["user"]["broker"]["id"],
                'task_id' => $task["id"],
                'writer_id' => ${'writer' . $i}["user"]["writer"]["id"],
                'status' => 2
            ]) -> exists();

            $this -> assertEquals(true, $offer_exists);
        }
    }

    public function test_writer_can_fetch_task_offers()
    {
        Taskoffer::truncate();

        // Create Broker
        $broker = $this -> createUser();

        $number_of_writers = 2;
        $takers = "";

        for($i=0; $i < $number_of_writers; $i++){
            ${'writer' . $i} = $this -> createUser();
            $takers .= ${'writer' . $i}["user"]["writer"]["id"] . "_";
        }

        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> push();

        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $task["id"],
            'takers' => $takers,
            'difficulty' => rand(2,9),
        ]);
        
        $step_6_response -> assertStatus(200);

        $this -> refreshApplication();

        //Each Writer to Fetch Offers
        for($i=0; $i < $number_of_writers; $i++){
            
            $fetch_offer_response = $this->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . ${'writer' . $i}["token"],
            ])->json('POST', 'api/offer/get_mine_paginated', []);

            $fetch_offer_response -> assertStatus(200);
            $number_of_offers_to_the_writer = $fetch_offer_response -> decodeResponseJson();
            $this -> assertEquals(1, count($number_of_offers_to_the_writer["data"]));
        }
    }
    
    public function test_writer_can_accept_task_offer()
    {
        Taskoffer::truncate();
        Task::truncate();

        // Create Broker and Writer
        $broker = $this -> createUser();
        $number_of_writers = 2;
        $takers = "";

        for($i=0; $i < $number_of_writers; $i++){
            ${'writer' . $i} = $this -> createUser();
            $takers .= ${'writer' . $i}["user"]["writer"]["id"] . "_";
        }

        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> push();

        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $task["id"],
            'takers' => $takers,
            'difficulty' => rand(2,9),
        ]);

        $step_6_response -> assertStatus(200);

        $this -> refreshApplication();

        //Let the last Writer Accept the offer
        $offer_id = Taskoffer::query() -> where([
            'task_id' => $task["id"],
            'writer_id' => ${'writer' . ($number_of_writers - 1)}["user"]["writer"]["id"],
            'broker_id' => $broker["user"]["broker"]["id"],
            'status' => 1
        ]) -> value('id');

        $accept_task_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . ${'writer' . ($number_of_writers - 1)}["token"],
        ])->json('POST', 'api/offer/accept', [
            'task_id' => $task["id"],
            'offer_id' => $offer_id
        ]);

        $accept_task_response -> assertStatus(200);

        $is_task_updated = DB::table('tasks') -> where([
            'id' => $task["id"],
            'status' => 2,
            'writer_id' => ${'writer' . ($number_of_writers - 1)}["user"]["writer"]["id"]
        ]) -> exists();

        $this -> assertEquals(true, $is_task_updated);

        // Check offer status for writer who accepted offer
        $accepted_offer_is_updated = DB::table('taskoffers') -> where([
            'id' => $offer_id,
            'status' => 4,
            'writer_id' => ${'writer' . ($number_of_writers - 1)}["user"]["writer"]["id"]
        ]) -> exists();

        $this -> assertEquals(true, $accepted_offer_is_updated);

        //Check Whether Other Offers are updated
        for($j=0; $j < ($number_of_writers - 1); $j++){
            ${'offer_id' . $j} = Taskoffer::query() -> where([
                'task_id' => $task["id"],
                'writer_id' => ${'writer' . $j}["user"]["writer"]["id"],
                'broker_id' => $broker["user"]["broker"]["id"],
            ]) -> value('id');

            $cancelled_offer_is_task_updated = DB::table('taskoffers') -> where([
                'id' => ${'offer_id' . $j},
                'status' => 5,
                'writer_id' => ${'writer' . $j}["user"]["writer"]["id"]
            ]) -> exists();
            $this -> assertEquals(true, $cancelled_offer_is_task_updated);
        }

    }

    public function test_writer_can_reject_task_offer()
    {
        Taskoffer::truncate();
        Task::truncate();

        // Create Broker and Writer
        $broker = $this -> createUser();
        $writer = $this -> createUser();
        $takers = $writer["user"]["writer"]["id"] . "_";

        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> push();

        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $task["id"],
            'takers' => $takers,
            'difficulty' => rand(2,9),
        ]);

        $step_6_response -> assertStatus(200);

        $this -> refreshApplication();

        //Let the Writer reject offer
        $offer_id = Taskoffer::query() -> where([
            'task_id' => $task["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'broker_id' => $broker["user"]["broker"]["id"],
            'status' => 1
        ]) -> value('id');

        $accept_task_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer["token"],
        ])->json('POST', 'api/offer/reject', [
            'task_id' => $task["id"],
            'offer_id' => $offer_id
        ]);

        $accept_task_response -> assertStatus(200);

        // Check offer status for writer who accepted offer
        $rejected_offer_is_updated = DB::table('taskoffers') -> where([
            'id' => $offer_id,
            'status' => 3,
            'writer_id' => $writer["user"]["writer"]["id"]
        ]) -> exists();

        $this -> assertEquals(true, $rejected_offer_is_updated);
    }

    public function test_broker_can_send_offer_text_message()
    {
        Taskoffer::truncate();
        Task::truncate();

        // Create Broker and Writer
        $broker = $this -> createUser();
        $writer = $this -> createUser();
        $takers = $writer["user"]["writer"]["id"] . "_";

        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> push();

        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $task["id"],
            'takers' => $takers,
            'difficulty' => rand(2,9),
        ]);

        $step_6_response -> assertStatus(200);

        $this -> refreshApplication();

        $offer_id = Taskoffer::query() -> where([
            'task_id' => $task["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'broker_id' => $broker["user"]["broker"]["id"],
            'status' => 1
        ]) -> value('id');

        // Send message
        $message = 'The time is ' . Carbon::now();
        $send_message_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', 'api/offer/send_message', [
            'task_offer_id' => $offer_id,
            'message' => $message
        ]);

        $send_message_response -> assertStatus(200);

        // Ensure the message is found in the DB
        $message_exists = DB::table('taskoffermessages') -> where([
            'message' => $message,
            'user_id' => $broker["user"]["id"],
            'taskoffer_id' => $offer_id
        ]) -> exists();

        $this -> assertTrue($message_exists);
    }

    public function test_writer_can_send_offer_text_message()
    {
        Taskoffer::truncate();
        Task::truncate();

        // Create Broker and Writer
        $broker = $this -> createUser();
        $writer = $this -> createUser();
        $takers = $writer["user"]["writer"]["id"] . "_";

        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> push();

        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $task["id"],
            'takers' => $takers,
            'difficulty' => rand(2,9),
        ]);

        $step_6_response -> assertStatus(200);

        $this -> refreshApplication();

        $offer_id = Taskoffer::query() -> where([
            'task_id' => $task["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'broker_id' => $broker["user"]["broker"]["id"],
            'status' => 1
        ]) -> value('id');

        // Send message
        $message = 'The time is ' . Carbon::now();
        $send_message_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer["token"],
        ])->json('POST', 'api/offer/send_message', [
            'task_offer_id' => $offer_id,
            'message' => $message
        ]);

        $send_message_response -> assertStatus(200);

        // Ensure the message is found in the DB
        $message_exists = DB::table('taskoffermessages') -> where([
            'message' => $message,
            'user_id' => $writer["user"]["id"],
            'taskoffer_id' => $offer_id
        ]) -> exists();

        $this -> assertTrue($message_exists);
    }

    public function test_broker_can_send_offer_file_message()
    {
        Taskoffer::truncate();
        Task::truncate();

        // Create Broker and Writer
        $broker = $this -> createUser();
        $writer = $this -> createUser();
        $takers = $writer["user"]["writer"]["id"] . "_";

        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> push();

        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $task["id"],
            'takers' => $takers,
            'difficulty' => rand(2,9),
        ]);

        $step_6_response -> assertStatus(200);

        $this -> refreshApplication();

        $offer_id = Taskoffer::query() -> where([
            'task_id' => $task["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'broker_id' => $broker["user"]["broker"]["id"],
            'status' => 1
        ]) -> value('id');

        // Send message
        $documents = [];

        for ($i=0; $i < 5; $i++) { 
            array_push($documents, UploadedFile::fake()->image('avatar' . $i .'.jpg'));
        }

        $send_message_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', 'api/offer/send_message', [
            'task_offer_id' => $offer_id,
            'documents' => $documents
        ]);

        $send_message_response -> assertStatus(200);

        // Ensure the message is found in the DB
        $message_exists = DB::table('taskoffermessages') -> where([
            'user_id' => $broker["user"]["id"],
            'taskoffer_id' => $offer_id
        ]) -> exists();

        for($i=0; $i < 3; $i++){
            $message_exists = DB::table('taskoffermessages') -> where([
                'user_id' => $broker["user"]["id"],
                'message' => 'avatar' . $i . '.jpg',
                'type' => 'https://kazibin.sfo3.digitaloceanspaces.com/' . $i
            ]) -> exists();

            $this -> assertTrue($message_exists);
        }
    }

    public function test_writer_can_send_offer_file_message()
    {
        Taskoffer::truncate();
        Task::truncate();

        // Create Broker and Writer
        $broker = $this -> createUser();
        $writer = $this -> createUser();
        $takers = $writer["user"]["writer"]["id"] . "_";

        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> push();

        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $task["id"],
            'takers' => $takers,
            'difficulty' => rand(2,9),
        ]);

        $step_6_response -> assertStatus(200);

        $this -> refreshApplication();

        $offer_id = Taskoffer::query() -> where([
            'task_id' => $task["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'broker_id' => $broker["user"]["broker"]["id"],
            'status' => 1
        ]) -> value('id');

        // Send message
        $documents = [];

        for ($i=0; $i < 5; $i++) { 
            array_push($documents, UploadedFile::fake()->image('avatar' . $i .'.jpg'));
        }

        $send_message_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer["token"],
        ])->json('POST', 'api/offer/send_message', [
            'task_offer_id' => $offer_id,
            'documents' => $documents
        ]);

        $send_message_response -> assertStatus(200);

        // Ensure the message is found in the DB
        $message_exists = DB::table('taskoffermessages') -> where([
            'user_id' => $writer["user"]["id"],
            'taskoffer_id' => $offer_id
        ]) -> exists();

        for($i=0; $i < 3; $i++){
            $message_exists = DB::table('taskoffermessages') -> where([
                'user_id' => $writer["user"]["id"],
                'message' => 'avatar' . $i . '.jpg',
                'type' => 'https://kazibin.sfo3.digitaloceanspaces.com/' . $i
            ]) -> exists();

            $this -> assertTrue($message_exists);
        }
    }

    public function createToken()
    {
        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());

        return 'Bearer ' . $response->decodeResponseJson()['token'];
    }
}
