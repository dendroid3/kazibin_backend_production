<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\Concerns\ImpersonatesUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;

use Tests\TestCase;

use App\Models\User;
use App\Models\Usercode;
use App\Models\Writer;
use App\Models\Broker;
class RegistrationTest extends TestCase
{
    // use RefreshDatabase;

    // public function test_not_all_inputs_are_submited_registration_fails()
    // {
    //     $this -> withoutExceptionHandling();

    //     $response = $this->post('/api/register', []);

    //     $response->assertStatus(201);
    // }

    public function test_username_not_entered_registration_fails()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make([
                                    'username' => null
                                    // 'test_username_not_entered_registration_failstest_username_not_entered_registration_failstest_username_not_entered_registration_failstest_username_not_entered_registration_failstest_username_not_entered_registration_fails',
                                ]);
        $response = $this->post('/api/register', $user -> toArray());
        // dd($response);

        $this -> assertContains("The username field is required.", [$response->getData() -> errors ->username[0]]);
        $this->assertEquals('201', $response->status());
        $response->assertSessionHasNoErrors();

    }

    public function test_username_more_than_25_characters_registration_fails()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make([
                                    'username' => 'qwertyuioplkjhgfdsazxcvbnm',
                                    'pass' => 'password',
                                ]);

        $response = $this->post('/api/register', $user -> toArray());
        $this -> assertContains("The username must not be greater than 25 characters.", [$response->getData() -> errors ->username[0]]);
        $this->assertEquals('201', $response->status());
        $response->assertSessionHasNoErrors();

    }

    public function test_email_not_entered_registration_fails()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make([
                                    'email' => null,
                                    'pass' => 'password',
                                ]);
        
        $response = $this->post('/api/register', $user -> toArray());
        $this -> assertContains("The email field is required.", [$response->getData() -> errors ->email[0]]);
        $this->assertEquals('201', $response->status());
        $response->assertSessionHasNoErrors();

    }

    public function test_phone_number_not_entered_registration_fails()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make([
                                    'phone_number' => null,
                                    'pass' => 'password',
                                ]);

        $response = $this->post('/api/register', $user -> toArray());
        $this -> assertContains("The phone number field is required.", [$response->getData() -> errors ->phone_number[0]]);
        $this->assertEquals('201', $response->status());
        $response->assertSessionHasNoErrors();

    }

    public function test_phone_number_less_than_9_characters_registration_fails()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make([
                                    'phone_number' => 12345678,
                                    'pass' => 'password',
                                ]);

        $response = $this->post('/api/register', $user -> toArray());
        $this -> assertContains("The phone number must be between 9 and 10 characters.", [$response->getData() -> errors ->phone_number[0]]);
        $this->assertEquals('201', $response->status());
        $response->assertSessionHasNoErrors();

    }

    public function test_phone_number_more_than_10_characters_registration_fails()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make([
                                    'phone_number' => 12345678901,
                                    'pass' => 'password',
                                ]);

        $response = $this->post('/api/register', $user -> toArray());
        $this -> assertContains("The phone number must be between 9 and 10 characters.", [$response->getData() -> errors ->phone_number[0]]);
        $this->assertEquals('201', $response->status());
        $response->assertSessionHasNoErrors();

    }

    public function test_password_not_entered_registration_fails()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make([
                                    'pass' => null,
                                ]);
        $response = $this->post('/api/register', $user -> toArray());
        $this -> assertContains("The pass field is required.", [$response->getData() -> errors ->pass[0]]);
        $this->assertEquals('201', $response->status());
        $response->assertSessionHasNoErrors();

    }

    public function test_password_less_than_6_characters_registration_fails()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make([
                                    'pass' => '12345',
                                ]);

        $response = $this->post('/api/register', $user -> toArray());
        $this -> assertContains("The pass must be at least 6 characters.", [$response->getData() -> errors ->pass[0]]);
        $this->assertEquals('201', $response->status());

        $response->assertSessionHasNoErrors();

    }

    public function test_inputs_are_duplicated_in_the_db_registration_fails()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> create();

        $response = $this->post('/api/register', $user -> toArray());
        $response->assertStatus(201);
        $response->assertSessionHasNoErrors();
    }

    public function test_all_inputs_are_submited_user_registered_successfully()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());

        $response->assertSessionHasNoErrors();

        $response->assertStatus(200);

    }

    public function test_registered_successfully_n_token_created()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());
        
        // $user = User::find($response->decodeResponseJson()['response']['id']);

//        $token = 'Bearer ' . $user->createToken(env('APP_NAME'))-> accessToken;

        $response->assertSessionHasNoErrors();

        $response->assertStatus(200);

    }

    public function test_registered_successfully_and_details_recorded_correctly()
    {
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());

        //get registered user details and compare it to the models
        $registered_user = $response -> decodeResponseJson()['user'];
        // dd($user['username']);
        $this->assertTrue($registered_user['username'] === $user['username']);
        $this->assertTrue($registered_user['email'] === $user['email']);
        $this->assertTrue($registered_user['phone_number'] === $user['phone_number']);

        $response->assertSessionHasNoErrors();
        $response->assertStatus(200);
    }

    public function test_registered_successfully_broker_n_writer_profiles_and_usercodes_are_created(){
        $this -> withoutExceptionHandling();

        $user = User::factory() -> make(['pass' => 'password']);

        $response = $this->post('/api/register', $user -> toArray());

        $response->assertSessionHasNoErrors();
        $response->assertStatus(200);
        
        $registered_user_id = $response -> decodeResponseJson()['user']['id'];

        $this -> assertTrue(DB::table('brokers') ->where('user_id', $registered_user_id)->exists());
        $this -> assertTrue(DB::table('writers') ->where('user_id', $registered_user_id)->exists());
        $this -> assertTrue(DB::table('usercodes') ->where('user_id', $registered_user_id)->exists());
    }

    public function test_user_can_create_profile(){
        $this -> withoutExceptionHandling();

        $user = User::factory() -> create();
        $response = $this  -> actingAs($user) -> post('/api/create_profile', [
            'level' => 'university',
            'course' => 'course',
            'bio' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Doloremque, molestias, soluta saepe in fugit voluptas ea assumenda dolorum quod, est natus quam quia! Animi tempora fuga odit sapiente aliquam itaque facere unde mollitia culpa at. Illum consequatur impedit iure ex veritatis possimus et, ut similique dolorem, nostrum, ducimus itaque debitis.'
        ]);
        $response->assertStatus(200);
    }
}
