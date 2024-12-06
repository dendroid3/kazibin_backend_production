<?php

namespace Tests\Feature\Marketplace;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use App\Models\Account;
use App\Models\User;

class MarketplaceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public $user; 

    public function createUser() 
     {
        $user_details = User::factory() -> make(['pass' => 'password']);
 
        $registered_user = $this->post('/api/register', $user_details -> toArray());

        $this -> user = $registered_user;
 
        return $registered_user;
     }

    public function test_user_can_create_account()
    {
        Account::truncate();
        $user = $this -> user ? $this -> user : $this -> createUser();

        $account_factory =  Account::factory() -> make([]);
        $account_details =  ["account" => $account_factory];

        $add_account_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $user['token'],
        ])->json('POST', '/api/marketplace/create', $account_details);

        $add_account_response -> assertStatus(200);

        // We will have to check if the account has been recorded
        $account_exists = DB::table('accounts') -> where([
            'title' => $account_factory -> title,
            'type' => $account_factory -> type,
            'profile_origin' => $account_factory -> profile_origin,
            'profile_gender' => $account_factory -> profile_gender,
            'total_orders' => $account_factory -> total_orders,
            'pending_orders' => $account_factory -> pending_orders,
            'cost' => $account_factory -> cost,
            'negotiable' => $account_factory -> negotiable,
            'rating' => $account_factory -> rating,
            'expiry' => $account_factory -> expiry
        ]) -> exists();

        $this -> assertEquals(true, $account_exists);

    }

    public function test_user_can_edit_account()
    {
        $user = $this -> user ? $this -> user : $this -> createUser();

        // First we create it.
        $account_factory =  Account::factory() -> make([]);
        $account_details =  ["account" => $account_factory];

        $add_account_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $user['token'],
        ])->json('POST', '/api/marketplace/create', $account_details);


        $account_exists = DB::table('accounts') -> where([
            'type' => $account_factory -> type,
            'title' => $account_factory -> title,
            'profile_origin' => $account_factory -> profile_origin,
            'profile_gender' => $account_factory -> profile_gender,
            'total_orders' => $account_factory -> total_orders,
            'pending_orders' => $account_factory -> pending_orders,
            'amount_earned' => $account_factory -> amount_earned,
            'cost' => $account_factory -> cost,
            'negotiable' => $account_factory -> negotiable,
            'rating' => $account_factory -> rating,
            'expiry' => $account_factory -> expiry
        ]) -> exists();

        $this -> assertEquals(true, $account_exists);

        // Then we fetch it
        $account_to_be_edited = DB::table('accounts') -> where([
            'type' => $account_factory -> type,
            'title' => $account_factory -> title,
            'profile_origin' => $account_factory -> profile_origin,
            'profile_gender' => $account_factory -> profile_gender,
            'total_orders' => $account_factory -> total_orders,
            'pending_orders' => $account_factory -> pending_orders,
            'amount_earned' => $account_factory -> amount_earned,
            'cost' => $account_factory -> cost,
            'negotiable' => $account_factory -> negotiable,
            'rating' => $account_factory -> rating,
            'expiry' => $account_factory -> expiry
        ]) -> first();


        $account_to_be_edited_id = $account_to_be_edited -> id;
        $new_account_details =  Account::factory() -> make();
        $new_account_details -> id = $account_to_be_edited_id;
        $new_account_details_formatted = ["account" => $new_account_details];

        $update_account_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $user['token'],
        ])->json('POST', '/api/marketplace/update', $new_account_details_formatted);

        $update_account_response -> assertStatus(200);

        $edited_account_exists = DB::table('accounts') -> where([
            'id' => $account_to_be_edited_id,
            'type' => $new_account_details -> type,
            'title' => $new_account_details -> title,
            'profile_origin' => $new_account_details -> profile_origin,
            'profile_gender' => $new_account_details -> profile_gender,
            'total_orders' => $new_account_details -> total_orders,
            'pending_orders' => $new_account_details -> pending_orders,
            'amount_earned' => $new_account_details -> amount_earned,
            'cost' => $new_account_details -> cost,
            'negotiable' => $new_account_details -> negotiable,
            'rating' => $new_account_details -> rating,
            'expiry' => $new_account_details -> expiry
        ]) -> exists();

        $this -> assertEquals(true, $edited_account_exists);
    }

    public function test_user_can_delete_account()
    {
        Account::truncate();
        $user = $this -> user ? $this -> user : $this -> createUser();

        $account_factory =  Account::factory() -> make([]);
        $account_details =  ["account" => $account_factory];

        $add_account_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $user['token'],
        ])->json('POST', '/api/marketplace/create', $account_details);

        $add_account_response -> assertStatus(200);

        $total_number_of_accounts_in_db = Account::count();
        $this -> assertEquals(1, $total_number_of_accounts_in_db);

        $created_account = Account::first();

        $account_to_be_deleted_id = ["id" => $created_account -> id];

        $delete_account_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $user['token'],
        ])->json('DELETE', '/api/marketplace/delete', $account_to_be_deleted_id);

        $delete_account_response -> assertStatus(200);
        $total_number_of_accounts_in_db = Account::count();
        $this -> assertEquals(0, $total_number_of_accounts_in_db);
    }

    public function test_user_can_fetch_own_accounts()
    {
        $user = $this -> user ? $this -> user : $this -> createUser();

        $account_factory =  Account::factory() -> make([]);
        $account_details =  ["account" => $account_factory];

        $add_account_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $user['token'],
        ])->json('POST', '/api/marketplace/create', $account_details);

        $add_account_response -> assertStatus(200);

        $fetch_users_own_accounts_response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $user['token'],
        ])->json('POST', '/api/marketplace/get_mine');

        $fetch_users_own_accounts_response -> assertStatus(200);
    }

    public function test_logged_in_user_can_view_some_accounts()
    {
        $user = $this -> user ? $this -> user : $this -> createUser();

        $get_some_for_display_result = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $user['token'],
        ])->json('GET', '/api/marketplace/get_for_display');

        $get_some_for_display_result -> assertStatus(200);
    }

    public function test_guest_user_can_view_some_accounts()
    {
        $get_some_for_display_result = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->json('GET', '/api/marketplace/get_for_display_guest');

        $get_some_for_display_result -> assertStatus(200);
    }

    public function test_logged_in_user_can_view_paginated_accounts()
    {
        $user = $this -> user ? $this -> user : $this -> createUser();

        $get_paginated_for_display_result = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $user['token'],
        ])->json('GET', '/api/marketplace/get_paginated');

        $get_paginated_for_display_result -> assertStatus(200);
    }

    public function test_guest_user_can_view_paginated_accounts()
    {
        $get_paginated_for_display_result = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->json('GET', '/api/marketplace/get_paginated_guest');

        $get_paginated_for_display_result -> assertStatus(200);
    }

    public function test_user_can_view_a_specific_account()
    {

        $account_code = DB::table('accounts') -> select('code') -> first();

        $get_current_result = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->json('POST', '/api/marketplace/get_current', ["account_code" => $account_code -> code]);

        $get_current_result -> assertStatus(200);
    }

}
