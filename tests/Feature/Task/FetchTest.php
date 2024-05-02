<?php

namespace Tests\Feature\Task;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

use App\Models\User;
use App\Models\Task;


class FetchTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // use RefreshDatabase;

    public function createUser() 
    {
        $user_details = User::factory() -> make(['pass' => 'password']);

        $registered_user = $this->post('/api/register', $user_details -> toArray());

        return $registered_user;
    }

    public function test_all_posted_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create Broker
        $broker = $this -> createUser();

        $number_of_tasks = rand(3,6);
        
        // Create Task * number_of_tasks
        for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> broker_id = $broker["user"]["broker"]["id"];
            $task -> push();
        }

        $my_posted = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token'],
        ])->json('GET', '/api/task/get_all_posted_by_me');

        $this -> assertEquals($number_of_tasks, count($my_posted -> decodeResponseJson()));
        $my_posted->assertStatus(200);
    }

    public function test_all_incomplete_posted_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create Broker
        $broker = $this -> createUser();

        $number_of_tasks = rand(5,7);
        $number_of_incomplete_tasks = 0;

         // Create Task * number_of_tasks
         for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> status = 1;
            $task -> broker_id = $broker["user"]["broker"]["id"];

            if(rand(0,9) < 5){
                $task -> status = 2;
                $number_of_incomplete_tasks ++;
            }

            $task -> push();
        }

        $my_posted_incomplete = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token'],
        ])->json('POST', '/api/task/get_all_posted_by_me_paginated', [
            'status' => 2
        ]);

        $my_posted_incomplete -> assertStatus(200);
        $this -> assertEquals($number_of_incomplete_tasks, count($my_posted_incomplete -> decodeResponseJson()["data"]));
    }
    
    public function test_all_complete_posted_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create Broker
        $broker = $this -> createUser();

        $number_of_tasks = rand(5,7);
        $number_of_complete_tasks = 0;

         // Create Task * number_of_tasks
         for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> status = 1;
            $task -> broker_id = $broker["user"]["broker"]["id"];

            if(rand(0,9) < 5){
                $task -> status = 3;
                $number_of_complete_tasks ++;
            }

            $task -> push();
        }

        $my_posted_complete = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token'],
        ])->json('POST', '/api/task/get_all_posted_by_me_paginated', [
            'status' => 3
        ]);

        $my_posted_complete -> assertStatus(200);
        $this -> assertEquals($number_of_complete_tasks, count($my_posted_complete -> decodeResponseJson()["data"]));
    }

    public function test_all_cancelled_posted_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create Broker
        $broker = $this -> createUser();

        $number_of_tasks = rand(5,7);
        $number_of_cancelled_tasks = 0;

         // Create Task * number_of_tasks
         for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> status = 1;
            $task -> broker_id = $broker["user"]["broker"]["id"];

            if(rand(0,9) < 5){
                $task -> status = 4;
                $number_of_cancelled_tasks ++;
            }

            $task -> push();
        }

        $my_posted_cancelled = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token'],
        ])->json('POST', '/api/task/get_all_posted_by_me_paginated', [
            'status' => 4
        ]);

        $my_posted_cancelled -> assertStatus(200);
        $this -> assertEquals($number_of_cancelled_tasks, count($my_posted_cancelled -> decodeResponseJson()["data"]));
    }
    
    public function test_all_invoiced_posted_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create Broker
        $broker = $this -> createUser();

        $number_of_tasks = rand(5,7);
        $number_of_invoiced_tasks = 0;

         // Create Task * number_of_tasks
         for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> status = 1;
            $task -> broker_id = $broker["user"]["broker"]["id"];

            if(rand(0,9) < 5){
                $task -> status = 5;
                $number_of_invoiced_tasks ++;
            }

            $task -> push();
        }

        $my_posted_invoiced = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token'],
        ])->json('POST', '/api/task/get_all_posted_by_me_paginated', [
            'status' => 5
        ]);

        $my_posted_invoiced -> assertStatus(200);
        $this -> assertEquals($number_of_invoiced_tasks, count($my_posted_invoiced -> decodeResponseJson()["data"]));
    }

    public function test_all_paid_posted_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create Broker
        $broker = $this -> createUser();

        $number_of_tasks = rand(5,7);
        $number_of_paid_tasks = 0;

         // Create Task * number_of_tasks
         for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> status = 1;
            $task -> broker_id = $broker["user"]["broker"]["id"];

            if(rand(0,9) < 5){
                $task -> status = 6;
                $number_of_paid_tasks ++;
            }

            $task -> push();
        }

        $my_posted_paid = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token'],
        ])->json('POST', '/api/task/get_all_posted_by_me_paginated', [
            'status' => 6
        ]);

        $my_posted_paid -> assertStatus(200);
        $this -> assertEquals($number_of_paid_tasks, count($my_posted_paid -> decodeResponseJson()["data"]));
    }

    public function test_all_processing_pay_posted_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create Broker
        $broker = $this -> createUser();

        $number_of_tasks = rand(5,7);
        $number_of_processing_pay_tasks = 0;

         // Create Task * number_of_tasks
         for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> status = 1;
            $task -> broker_id = $broker["user"]["broker"]["id"];

            if(rand(0,9) < 5){
                $task -> status = 8;
                $number_of_processing_pay_tasks ++;
            }

            $task -> push();
        }

        $my_posted_processing_pay = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token'],
        ])->json('POST', '/api/task/get_all_posted_by_me_paginated', [
            'status' => 8
        ]);

        $my_posted_processing_pay -> assertStatus(200);
        $this -> assertEquals($number_of_processing_pay_tasks, count($my_posted_processing_pay -> decodeResponseJson()["data"]));
    }

    public function test_all_done_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create Writer
        $writer = $this -> createUser();

        $number_of_tasks = rand(3,6);
        
        // Create Task * number_of_tasks
        for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> writer_id = $writer["user"]["writer"]["id"];
            $task -> push();
        }

        $my_done = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token'],
        ])->json('GET', '/api/task/get_all_done_by_me');

        $this -> assertEquals($number_of_tasks, count($my_done -> decodeResponseJson()));
        $my_done->assertStatus(200);
    }
    
    public function test_all_complete_done_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create Writer
        $writer = $this -> createUser();

        $number_of_tasks = rand(5,7);
        $number_of_complete_tasks = 0;

         // Create Task * number_of_tasks
         for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> status = 1;
            $task -> writer_id = $writer["user"]["writer"]["id"];

            if(rand(0,9) < 5){
                $task -> status = 3;
                $number_of_complete_tasks ++;
            }

            $task -> push();
        }

        $my_done_complete = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token'],
        ])->json('POST', '/api/task/get_all_done_by_me_paginated', [
            'status' => 3
        ]);

        $my_done_complete -> assertStatus(200);
        $this -> assertEquals($number_of_complete_tasks, count($my_done_complete -> decodeResponseJson()["data"]));
    }

    public function test_all_cancelled_done_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create writer
        $writer = $this -> createUser();

        $number_of_tasks = rand(5,7);
        $number_of_cancelled_tasks = 0;

         // Create Task * number_of_tasks
         for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> status = 1;
            $task -> writer_id = $writer["user"]["writer"]["id"];

            if(rand(0,9) < 5){
                $task -> status = 4;
                $number_of_cancelled_tasks ++;
            }

            $task -> push();
        }

        $my_done_cancelled = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token'],
        ])->json('POST', '/api/task/get_all_done_by_me_paginated', [
            'status' => 4
        ]);

        $my_done_cancelled -> assertStatus(200);
        $this -> assertEquals($number_of_cancelled_tasks, count($my_done_cancelled -> decodeResponseJson()["data"]));
    }
    
    public function test_all_invoiced_done_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create writer
        $writer = $this -> createUser();

        $number_of_tasks = rand(5,7);
        $number_of_invoiced_tasks = 0;

         // Create Task * number_of_tasks
         for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> status = 1;
            $task -> writer_id = $writer["user"]["writer"]["id"];

            if(rand(0,9) < 5){
                $task -> status = 5;
                $number_of_invoiced_tasks ++;
            }

            $task -> push();
        }

        $my_done_invoiced = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token'],
        ])->json('POST', '/api/task/get_all_done_by_me_paginated', [
            'status' => 5
        ]);

        $my_done_invoiced -> assertStatus(200);
        $this -> assertEquals($number_of_invoiced_tasks, count($my_done_invoiced -> decodeResponseJson()["data"]));
    }

    public function test_all_paid_done_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create writer
        $writer = $this -> createUser();

        $number_of_tasks = rand(5,7);
        $number_of_paid_tasks = 0;

         // Create Task * number_of_tasks
         for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> status = 1;
            $task -> writer_id = $writer["user"]["writer"]["id"];

            if(rand(0,9) < 5){
                $task -> status = 6;
                $number_of_paid_tasks ++;
            }

            $task -> push();
        }

        $my_done_paid = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token'],
        ])->json('POST', '/api/task/get_all_done_by_me_paginated', [
            'status' => 6
        ]);

        $my_done_paid -> assertStatus(200);
        $this -> assertEquals($number_of_paid_tasks, count($my_done_paid -> decodeResponseJson()["data"]));
    }

    public function test_all_processing_pay_done_by_me_successfully_fetched()
    {
        Task::truncate();
        // Create writer
        $writer = $this -> createUser();

        $number_of_tasks = rand(5,7);
        $number_of_processing_pay_tasks = 0;

         // Create Task * number_of_tasks
         for ($i=0; $i < $number_of_tasks; $i++) { 
            $task = Task::factory() -> create();
            $task -> status = 1;
            $task -> writer_id = $writer["user"]["writer"]["id"];

            if(rand(0,9) < 5){
                $task -> status = 8;
                $number_of_processing_pay_tasks ++;
            }

            $task -> push();
        }

        $my_done_processing_pay = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token'],
        ])->json('POST', '/api/task/get_all_done_by_me_paginated', [
            'status' => 8
        ]);

        $my_done_processing_pay -> assertStatus(200);
        $this -> assertEquals($number_of_processing_pay_tasks, count($my_done_processing_pay -> decodeResponseJson()["data"]));
    }
}
