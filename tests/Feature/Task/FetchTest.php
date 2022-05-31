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
    public function test_all_posted_by_me_successfully_fetched()
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
        
        $my_posted = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ])->json('GET', 'api/task/get_all_posted_by_me');
        $this -> assertEquals(1, count($my_posted['tasks']));
        $my_posted->assertStatus(200);
    }

    public function createToken()
    {
        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());

        return $response->decodeResponseJson()['token'];
    }
}
