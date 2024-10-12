<?php

namespace App\Services\Account;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Services\SystemLog\LogCreationService;

use App\Models\Account;
use App\Models\Accountfile;
use App\Models\Revenue;
use App\Models\Transaction;
// use Illuminate\Support\Facades\Log;

class AccountsService
{
    public function create($request, $log_service) {
        $account_details = json_decode($request -> account, true);
        $account = new Account;
        $account -> user_id = Auth::user() -> id;
        $account -> code = strtoupper(Str::random(2)) . '-' . strtoupper(Str::random(3));
        $account -> title = $account_details['title'];
        $account -> rating = $account_details['rating'];
        $account -> profile_origin = $account_details['profile_origin'];
        $account -> profile_gender = $account_details['profile_gender'];
        $account -> total_orders = $account_details['total_orders'];
        $account -> pending_orders = $account_details['pending_orders'];
        $account -> cost = $account_details['cost'];
        $account -> negotiable = $account_details['negotiable'];
        $account -> display = true;
        $account -> expiry = Carbon::now() -> addDays(7);
        $account -> save();

        if($request -> hasFile('screenshots')){
            $files = $request -> file('screenshots');
            $file_urls = array();
            $i = 0;
    
            foreach ($files as $file) {
                $uploadedFileUrl = Storage::disk('digitalocean')->putFile('\Accounts/' . $account -> code, $request->file('screenshots')[$i], 'public');
                $account_file = new Accountfile;
                $account_file -> account_id = $account -> id;
                $account_file -> url = env('DIGITALOCEAN_SPACES_ENDPOINT') .  $uploadedFileUrl;
                $account_file -> name =  $request -> file('screenshots')[$i] -> getClientOriginalName();
                $account_file -> save();
        
                array_push($file_urls, $account_file);
                $i++;
            }
        }

        // transaction and revenue

        $transaction = new Transaction;
        $transaction -> user_id = Auth::user() -> id;
        $transaction -> type = "Credit";
        $transaction -> account_id = $account -> id;
        $transaction -> description = 'Amount charged to display '. $account -> code . ": " . $account -> title . " writing account";
        $transaction -> amount = env('PAYMENT_ACCOUNT_DISPLAY_COST');
        $transaction -> save();
    
        $revenue = new Revenue;
        $revenue -> transaction_id = $transaction -> id;
        $revenue -> type = "displayAccount";
        $revenue -> amount = env('PAYMENT_ACCOUNT_DISPLAY_COST');
        $revenue -> save();

        $log_message = $account -> code . ": " . $account -> title . " writing account successfully posted and forwarded to kazibin's groups.";

        $log_service -> createSystemMessage(
            Auth::user() -> id, 
            $log_message,
            $account -> id,
            'Account Posted'
        );

        return $account -> title . " writing account successfully posted.";
    }

    public function getMine($request) {
        Log::info($request);

        if($request -> is_filtered){
            $accounts = Auth::user() -> accounts() -> where('display', $request -> filter_code) -> paginate(10);
        } else {
            $accounts = Auth::user() -> accounts() -> paginate(10);
        }

        foreach ($accounts as $account) {
            $account -> Files;
        }

        return $accounts;
    }

    public function getSomeForDisplay($request) {
        $query = Account::query() -> where('display', true);
        
        $fullPath = $request -> path();
        $exploded_url = last(explode('/', $fullPath));

        if($exploded_url != "get_for_display_guest") {
            $query -> where('user_id', '!=', Auth::user() ->id);
        }

        $accounts = $query -> take(10) -> get();
        foreach ($accounts as $account) {
            $account -> User;
            $account -> Files;
        }

        return $accounts;
    }

    public function getAllPaginated() {
        $query = Account::query() -> where('display', true);
        

        $fullPath = $request -> path();
        $exploded_url = last(explode('/', $fullPath));

        if($exploded_url != "get_paginated_guest") {
            $query -> where('user_id', '!=', Auth::user() ->id);
        }

        $accounts = $query -> paginate(10);

        foreach ($accounts as $account) {
            $account -> User;
            $account -> Files;
        }

        return $accounts;
    }

    public function getCurrentAccount(Request $request) {
        $account = Account::query() -> where('code', $request -> account_code) -> first();
        $account -> User;
        $account -> Files;
        
        if(!$account){
            return 404;
        }

        $account -> User;
        $account -> Files;

        return $account;
    }
}
