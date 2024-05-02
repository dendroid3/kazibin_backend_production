<?php

namespace Tests\Feature\Task;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

use App\Models\Invoice;
use App\Models\Task;
use App\Models\Taskmessage;
use App\Models\User;
use App\Models\Bid;

class TaskOperationTest extends TestCase
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
    
    public function test_broker_can_delete_task()
    {
        Task::truncate();
        // Create Broker
        $broker = $this -> createUser();

        $task = Task::factory() -> create();
        $task -> status = 1;
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> push();

        $get_for_bidding_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->json('POST', '/api/task/get_for_bidding', [
            'task_code' => $task["code"]
        ]);

        $get_for_bidding_response -> assertStatus(200);

        // Delete task
        $delete_task_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token'],
        ])->json('POST', '/api/task/delete', [
            'task_id' => $task["id"]
        ]);

        // dd($delete_task_response -> decodeResponseJson());
        $delete_task_response -> assertStatus(200);

        // Get Deleted Task
        $get_deleted_for_bidding_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->json('POST', '/api/task/get_for_bidding', [
            'task_code' => $task["code"]
        ]);
        $get_deleted_for_bidding_response -> assertStatus(200);
        $this -> assertEquals($get_deleted_for_bidding_response -> decodeResponseJson() -> json, "404");
    }

    public function test_writer_can_bid_on_task()
    {
        Bid::truncate();
       
        // Create Broker
        $writer = $this -> createUser();
       
        $task = Task::factory() -> create();

        $create_bid_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token']
        ])->json('POST', '/api/bid/create', [
            'task_id' => $task["id"],
            'broker_id' => $task["broker_id"],
            'bid_cost' => 20
        ]);

        $create_bid_response -> assertStatus(200);

        $bid_exists = DB::table('bids') -> where([
            'writer_id' => $writer["user"]["writer"]["id"],
            'task_id' => $task["id"]
        ]) -> exists();

        $this -> assertEquals($bid_exists, true);
    }

    public function test_broker_can_assign_task_by_accepting_bid()
    {
        Bid::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
       
        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> push();

        $create_bid_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token']
        ])->json('POST', '/api/bid/create', [
            'task_id' => $task["id"],
            'broker_id' => $task["broker_id"],
            'bid_cost' => 20
        ]);

        $bid_exists = DB::table('bids') -> where([
            'writer_id' => $writer["user"]["writer"]["id"],
            'task_id' => $task["id"]
        ]) -> exists();

        $this -> assertEquals($bid_exists, true);  
        
        $this -> refreshApplication();

        // Accept bid as Broker
        $accept_bid_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token']
        ]) -> json('POST', 'api/bid/accept',[
            'bid_id' => $create_bid_response -> decodeResponseJson()["bid"]["id"]
        ]);

        $accept_bid_response -> assertStatus(200);

        $assigned_task_exists = DB::table('tasks') -> where([
            'id' => $task["id"],
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'status' => 2
        ]) -> exists();

        $this -> assertEquals(true, $assigned_task_exists);

        $bid_changed_status = DB::table('bids') -> where([
            'task_id' => $task["id"],
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'status' => 4
        ]) -> exists();

        $this -> assertEquals(true, $bid_changed_status);
    }

    public function test_writer_can_mark_task_complete()
    {
        Task::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> writer_id = $writer["user"]["writer"]["id"];
        $task -> status = 2;
        $task -> push();

        $mark_task_complete_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token']
        ]) -> json('POST', 'api/task/mark_complete', [
            'task_id' => $task["id"]
        ]);

        $mark_task_complete_response -> assertStatus(200);

        $completed_task_exists = DB::table('tasks') -> where([
            'status' => 3,
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"]
        ]) -> exists();

        $this -> assertEquals($completed_task_exists, true);

    
    }

    public function test_broker_can_create_invoice()
    {
        Task::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $number_of_tasks = 3;
        $tasks_signature = "";
        $total_cost_of_tasks = 0;

        for($i = 0; $i < $number_of_tasks; $i++){
            $task = Task::factory() -> create();
            $task -> broker_id = $broker["user"]["broker"]["id"];
            $task -> writer_id = $writer["user"]["writer"]["id"];
            $task -> status = 3;
            $task -> push();

            $total_cost_of_tasks += $task["full_pay"];
            $tasks_signature .= $task["id"] . "_";
        }
        
        $create_invoice_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token']
        ]) -> json('POST', 'api/transaction/create_invoice', [
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'amount' => $total_cost_of_tasks,
            'tasks_signature' => $tasks_signature
        ]);

        $create_invoice_response -> assertStatus(200);
    }

    public function test_writer_can_create_invoice()
    {
        Task::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $number_of_tasks = 3;
        $tasks_signature = "";
        $total_cost_of_tasks = 0;

        for($i = 0; $i < $number_of_tasks; $i++){
            $task = Task::factory() -> create();
            $task -> broker_id = $broker["user"]["broker"]["id"];
            $task -> writer_id = $writer["user"]["writer"]["id"];
            $task -> status = 3;
            $task -> push();

            $total_cost_of_tasks += $task["full_pay"];
            $tasks_signature .= $task["id"] . "_";
        }
        
        $create_invoice_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token']
        ]) -> json('POST', 'api/transaction/create_invoice', [
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'amount' => $total_cost_of_tasks,
            'tasks_signature' => $tasks_signature
        ]);

        $create_invoice_response -> assertStatus(200);
    }

    public function test_broker_can_mark_invoice_paid()
    {
        Task::truncate();
        Invoice::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $number_of_tasks = 3;
        $tasks_signature = "";
        $total_cost_of_tasks = 0;

        for($i = 0; $i < $number_of_tasks; $i++){
            $task = Task::factory() -> create();
            $task -> broker_id = $broker["user"]["broker"]["id"];
            $task -> writer_id = $writer["user"]["writer"]["id"];
            $task -> status = 3;
            $task -> push();

            $total_cost_of_tasks += $task["full_pay"];
            $tasks_signature .= $task["id"] . "_";
        }
        
        $create_invoice_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token']
        ]) -> json('POST', 'api/transaction/create_invoice', [
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'amount' => $total_cost_of_tasks,
            'tasks_signature' => $tasks_signature
        ]);

        $create_invoice_response -> assertStatus(200);

        $invoice = Invoice::first();

        $mark_invoice_paid_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token']
        ]) -> json('POST', 'api/transaction/mark_paid', [
            'invoice_id' => $invoice["id"]
        ]);

        $mark_invoice_paid_response -> assertStatus(200);

        $invoice_exists = DB::table('invoices') -> where([
            'status' => 2,
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'tasks_signature' => $tasks_signature
        ]) -> exists();

        $this -> assertEquals(true, $invoice_exists);

    }

    public function test_writer_can_mark_invoice_paid()
    {
        Task::truncate();
        Invoice::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $number_of_tasks = 3;
        $tasks_signature = "";
        $total_cost_of_tasks = 0;

        for($i = 0; $i < $number_of_tasks; $i++){
            $task = Task::factory() -> create();
            $task -> broker_id = $broker["user"]["broker"]["id"];
            $task -> writer_id = $writer["user"]["writer"]["id"];
            $task -> status = 3;
            $task -> push();

            $total_cost_of_tasks += $task["full_pay"];
            $tasks_signature .= $task["id"] . "_";
        }
        
        $create_invoice_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token']
        ]) -> json('POST', 'api/transaction/create_invoice', [
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'amount' => $total_cost_of_tasks,
            'tasks_signature' => $tasks_signature
        ]);

        $create_invoice_response -> assertStatus(200);

        $invoice = Invoice::first();

        $mark_invoice_paid_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token']
        ]) -> json('POST', 'api/transaction/mark_paid', [
            'invoice_id' => $invoice["id"]
        ]);

        $mark_invoice_paid_response -> assertStatus(200);

        $invoice_exists = DB::table('invoices') -> where([
            'status' => 3,
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'tasks_signature' => $tasks_signature
        ]) -> exists();

        $this -> assertEquals(true, $invoice_exists);
    }

    public function test_writer_can_confirm_invoice_paid()
    {
        Task::truncate();
        Invoice::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $number_of_tasks = 3;
        $tasks_signature = "";
        $total_cost_of_tasks = 0;

        for($i = 0; $i < $number_of_tasks; $i++){
            $task = Task::factory() -> create();
            $task -> broker_id = $broker["user"]["broker"]["id"];
            $task -> writer_id = $writer["user"]["writer"]["id"];
            $task -> status = 3;
            $task -> push();

            $total_cost_of_tasks += $task["full_pay"];
            $tasks_signature .= $task["id"] . "_";
        }
        
        $create_invoice_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token']
        ]) -> json('POST', 'api/transaction/create_invoice', [
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'amount' => $total_cost_of_tasks,
            'tasks_signature' => $tasks_signature
        ]);

        $create_invoice_response -> assertStatus(200);

        $invoice = Invoice::first();

        $mark_invoice_paid_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token']
        ]) -> json('POST', 'api/transaction/mark_paid', [
            'invoice_id' => $invoice["id"]
        ]);

        $mark_invoice_paid_response -> assertStatus(200);

        $invoice_exists = DB::table('invoices') -> where([
            'status' => 2,
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'tasks_signature' => $tasks_signature
        ]) -> exists();

        $this -> assertEquals(true, $invoice_exists);

        $this -> refreshApplication();

        $confirm_payment_response = $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token']
        ]) -> json('POST', 'api/transaction/confirm_paid', [
            'invoice_id' => $invoice["id"]
        ]);

        $confirm_payment_response -> assertStatus(200);

        $invoice_exists = DB::table('invoices') -> where([
            'status' => 3,
            'broker_id' => $broker["user"]["broker"]["id"],
            'writer_id' => $writer["user"]["writer"]["id"],
            'tasks_signature' => $tasks_signature
        ]) -> exists();

    }

    public function test_broker_can_send_text_message()
    {
        Task::truncate();
        Taskmessage::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> writer_id = $writer["user"]["writer"]["id"];
        $task -> status = 2;
        $task -> push();

        $message = "The time is " . Carbon::now();

        $send_message_response =  $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker['token']
        ]) -> json('POST', 'api/task/send_message', [
            'task_id' => $task["id"],
            'message' => $message
        ]);
        
        $send_message_response -> assertStatus(200);

        $message_exists = DB::table('taskmessages') -> where([
            'user_id' => $broker["user"]["id"],
            'message' => $message,
            'task_id' => $task["id"]
        ]) -> exists();
        $this -> assertEquals(true, $message_exists);

    }

    public function test_writer_can_send_text_message()
    {
        Task::truncate();
        Taskmessage::truncate();
    
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> writer_id = $writer["user"]["writer"]["id"];
        $task -> status = 2;
        $task -> push();

        $message = "The time is " . Carbon::now();

        $send_message_response =  $this -> withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer['token']
        ]) -> json('POST', 'api/task/send_message', [
            'task_id' => $task["id"],
            'message' => $message
        ]);
        
        $send_message_response -> assertStatus(200);

        $message_exists = DB::table('taskmessages') -> where([
            'user_id' => $writer["user"]["id"],
            'message' => $message,
            'task_id' => $task["id"]
        ]) -> exists();
        $this -> assertEquals(true, $message_exists);
    }
    
    public function test_broker_can_send_file_message()
    {
        Task::truncate();
        Taskmessage::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> writer_id = $writer["user"]["writer"]["id"];
        $task -> status = 2;
        $task -> push();

        $documents = [];

        for ($i=0; $i < 3; $i++) { 
            array_push($documents, UploadedFile::fake()->image('avatar' . $i .'.jpg'));
        }

        $send_message_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', '/api/task/send_message', [
            'documents' => $documents,
            'task_id' => $task["id"],
        ]);

        $send_message_response -> assertStatus(200);

        for($i=0; $i < 3; $i++){
            $message_exists = DB::table('taskmessages') -> where([
                'user_id' => $broker["user"]["id"],
                'task_id' => $task["id"],
                'message' => 'avatar' . $i . '.jpg',
                'type' => 'https://kazibin.sfo3.digitaloceanspaces.com/' . $i
            ]) -> exists();
            $this -> assertEquals(true, $message_exists);
        }

    }

    public function test_writer_can_send_file_message()
    {
        Task::truncate();
        Taskmessage::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> writer_id = $writer["user"]["writer"]["id"];
        $task -> status = 2;
        $task -> push();

        $documents = [];

        for ($i=0; $i < 3; $i++) { 
            array_push($documents, UploadedFile::fake()->image('avatar' . $i .'.jpg'));
        }

        $send_message_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $writer["token"],
        ])->json('POST', '/api/task/send_message', [
            'documents' => $documents,
            'task_id' => $task["id"],
        ]);

        $send_message_response -> assertStatus(200);

        for($i=0; $i < 3; $i++){
            $message_exists = DB::table('taskmessages') -> where([
                'user_id' => $writer["user"]["id"],
                'task_id' => $task["id"],
                'message' => 'avatar' . $i . '.jpg',
                'type' => 'https://kazibin.sfo3.digitaloceanspaces.com/' . $i
            ]) -> exists();
            $this -> assertEquals(true, $message_exists);
        }

    }

    public function test_broker_can_change_due_date()
    {
        Task::truncate();
        Taskmessage::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> writer_id = $writer["user"]["writer"]["id"];
        $task -> status = 2;
        $task -> expiry_time = Carbon::now();   
        $task -> push();

        $new_time = Carbon::now()->addMinutes(rand(360, 7200))->toDateTimeString();

        // Change Due Date
        $send_message_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', '/api/create_task/change_deadline', [
            'task_id' => $task["id"],
            'expiry_time' => $new_time,
        ]);

        $send_message_response -> assertStatus(200);

        $task_with_new_date_exists = DB::table('tasks') -> where([
            'id' => $task["id"],
            'expiry_time' => $new_time
        ]) -> exists();

        $this -> assertEquals(true, $task_with_new_date_exists);

    }

    public function test_broker_can_change_cost_of_task()
    {
        Task::truncate();
        Taskmessage::truncate();
       
        // Create Broker and Writer
        $writer = $this -> createUser();
        $broker = $this -> createUser();
        
        $task = Task::factory() -> create();
        $task -> broker_id = $broker["user"]["broker"]["id"];
        $task -> writer_id = $writer["user"]["writer"]["id"];
        $task -> status = 2;
        $task -> full_pay = rand(500, 1000);   
        $task -> push();

        $new_full_pay = rand(5000, 10000);

        // Change Due Date
        $send_message_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $broker["token"],
        ])->json('POST', '/api/create_task/change_payment', [
            'task_id' => $task["id"],
            'full_pay' => $new_full_pay,
        ]);

        $send_message_response -> assertStatus(200);

        $task_with_new_full_pay_exists = DB::table('tasks') -> where([
            'id' => $task["id"],
            'full_pay' => $new_full_pay
        ]) -> exists();

        $this -> assertEquals(true, $task_with_new_full_pay_exists);
    }
}
