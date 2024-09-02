<?php

namespace Tests\Feature\Job;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

use Carbon\Carbon;

use Tests\TestCase;

use App\Models\Task;
use App\Models\User;

class AdditionTest extends TestCase
{

    public function createToken()
    {
        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());

        return $response->decodeResponseJson()['token'];
    }


    public function test_full_flow_with_no_pages()
    {

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

     public function test_full_flow_with_pages()
     {
        // dd(config('filesystems.disks'));
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
