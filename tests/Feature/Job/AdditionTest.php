<?php

namespace Tests\Feature\Job;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

use Tests\TestCase;

use App\Models\Task;
use App\Models\User;

class AdditionTest extends TestCase
{
    public function test_addition_fails_not_logged_in()
    {
        // $response = $this->get('/');
        $task = Task::factory() -> make();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $response->assertStatus(401);
    }
    // public function test_ownership_middleware_works_changes_cannot_be_made_to_another_users_document()
    // {
    //     $this -> withoutExceptionHandling();
        
    //     $task = Task::factory() -> make();
    //     $step_1_response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $this -> createToken()
    //         ,
    //     ])->json('POST', 'api/create_task/step_1', $task -> toArray());

    //     $token = 'Bearer ' . $this -> createToken();

    //     $step_3_response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => $token,
    //     ])->json('POST', 'api/create_task/step_3', [
    //         'task_id' =>  $step_1_response->decodeResponseJson()['task']['id'],
    //         'full_pay' => 3000
    //     ]);
    //     $step_3_response->assertStatus(202);
    // }
    public function test_addition_fails_no_topic()
    {
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make(['topic' => null]);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $response->assertStatus(201);
    }
    public function test_addition_fails_no_unit()
    {
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make(['unit' => null]);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $response->assertStatus(201);
    }
    public function test_addition_fails_no_instructions()
    {
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make(['instructions' => null]);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $response->assertStatus(201);
    }
    public function test_addition_fails_no_type()
    {
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make(['type' => null]);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $response->assertStatus(201);
    }

    public function test_addition_step_one_done_successfully()
    {
        $this -> withoutExceptionHandling();
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $response->assertStatus(200);
    }

    public function test_addition_of_files_to_task_successful(){
        $this -> withoutExceptionHandling();
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make();
        $step_1_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $step_1_response->assertStatus(200);

        $documents = [];
        for ($i=0; $i < 5; $i++) { 
            array_push($documents, UploadedFile::fake()->image('avatar' . $i .'.jpg'));
        }

        $step_2_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_2', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'documents' => $documents
        ]);

        $step_2_response->assertStatus(200);

        for ($i=0; $i < 5; $i++) { 
            $this->assertEquals(1, DB::table('taskfiles') -> where('url', $step_2_response->decodeResponseJson()['task_files'][$i]['url']) -> exists());
        }

    }

    
    public function test_addition_step_three_with_no_pages_done_successfully()
    {
        $this -> withoutExceptionHandling();
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make();
        $step_1_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $step_1_response->assertStatus(200);

        $step_3_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_3', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'full_pay' => 3000
        ]);

        $step_3_response->assertStatus(200);
    } 

    public function test_addition_step_three_with_pages_done_successfully()
    {
        $this -> withoutExceptionHandling();
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make();
        $step_1_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $step_1_response->assertStatus(200);

        $step_3_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_3', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'pages' => 3,
            'page_cost' => 5000
        ]);

        $step_3_response->assertStatus(200);
    }

    public function test_addition_step_four_successfully_done_deadline_added(){

        $this -> withoutExceptionHandling();
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make();
        $step_1_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $step_1_response->assertStatus(200);

        $step_4_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_4', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'expiry_time' => Carbon::now()->toDateTimeString(),
        ]);

        $step_4_response->assertStatus(200);
    }

    public function test_addition_step_five_successfully_done_pay_day_added(){

        $this -> withoutExceptionHandling();
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make();
        $step_1_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $step_1_response->assertStatus(200);

        $step_5_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_5', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'pay_day' => Carbon::now()->toDateTimeString(),
        ]);

        $step_5_response->assertStatus(200);
    }

    public function test_addition_step_six_fails_no_difficulty_level_entered(){

        $this -> withoutExceptionHandling();
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make();
        $step_1_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $step_1_response->assertStatus(200);

        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'takers' => ' ',
        ]);

        $step_6_response->assertStatus(201);
    }

    public function test_addition_step_six_successfully_done_takers_added(){

        $this -> withoutExceptionHandling();
        
        $token = 'Bearer ' . $this -> createToken();

        $task = Task::factory() -> make();
        $step_1_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $step_1_response->assertStatus(200);

        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'difficulty' => rand(2,9),
            'takers' => '1_2_3_4_5_',
        ]);

        $step_6_response->assertStatus(200);
    }

    public function test_full_flow_with_no_pages(){
        
        $this -> withoutExceptionHandling();
        $token = 'Bearer ' . $this -> createToken();

        //step one
        $task = Task::factory() -> make();
        $step_1_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $step_1_response->assertStatus(200);

        //step_two
        
        $documents = [];
        for ($i=0; $i < 5; $i++) { 
            array_push($documents, UploadedFile::fake()->image('avatar' . $i .'.jpg'));
        }

        $step_2_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_2', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'documents' => $documents
        ]);

        $step_2_response->assertStatus(200);

        for ($i=0; $i < 5; $i++) { 
            $this->assertEquals(1, DB::table('taskfiles') -> where('url', $step_2_response->decodeResponseJson()['task_files'][$i]['url']) -> exists());
        }

        //step_3
        
        $step_3_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_3', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'full_pay' => rand(15,50) * 10
        ]);

        $step_3_response->assertStatus(200);

        //step 4 | expiry time

        $step_4_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_4', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'expiry_time' => Carbon::now()->toDateTimeString(),
        ]);
        $step_4_response->assertStatus(200);

        //step 5 | pay day
        
        $step_5_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_5', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'pay_day' => Carbon::now()->toDateTimeString(),
        ]);

        $step_5_response->assertStatus(200);

        //step 6
        
        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_6', [
            'broadcast_on_telegram' => true,
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'difficulty' => rand(2,9),
            'takers' => ' ',
        ]);

        $step_6_response->assertStatus(200);
    }
    
    public function createToken()
    {
        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());
        // dd($response);
        return $response->decodeResponseJson()['token'];
    }

    public function test_full_flow_with_pages(){
        
        $this -> withoutExceptionHandling();

        $token = 'Bearer ' . $this -> createToken();

        //step one
        $task = Task::factory() -> make();
        $step_1_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_1', $task -> toArray());

        $step_1_response->assertStatus(200);

        //step_two
        
        $documents = [];
        for ($i=0; $i < 5; $i++) { 
            array_push($documents, UploadedFile::fake()->image('avatar' . $i .'.jpg'));
        }

        $step_2_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_2', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'documents' => $documents
        ]);

        $step_2_response->assertStatus(200);
        // dd($step_2_response);

        for ($i=0; $i < 5; $i++) { 
            $this->assertEquals(1, DB::table('taskfiles') -> where('url', $step_2_response->decodeResponseJson()['task_files'][$i]['url']) -> exists());
        }

        //step_3
        
        $step_3_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_3', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'pages' => rand(1,8),
            'page_cost' => rand(15,50) * 10
            // 'full_pay' => rand(250,3500)

        ]);

        $step_3_response->assertStatus(200);

        //step 4 | expiry time

        $step_4_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_4', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'expiry_time' => Carbon::now()->addMinutes(rand(120, 7200))->toDateTimeString(),
        ]);
        $step_4_response->assertStatus(200);

        //step 5 | pay day
        
        $step_5_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_5', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            // 'pay_day' => Carbon::now() -> addMinute(rand(120, 7200)) ->toDateTimeString(),
            'pay_day' => '1965-05-28 00:00:00',
            'pay_day' => '1997-09-17 00:00:00',

        ]);

        $step_5_response->assertStatus(200);

        //step 6
        
        $step_6_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('POST', 'api/create_task/step_6', [
            'task_id' => $step_1_response->decodeResponseJson()['task']['id'],
            'difficulty' => rand(2,9),
            'takers' => ' ',
        ]);

        $step_6_response->assertStatus(200);
    }
}
