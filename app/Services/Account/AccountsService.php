<?php

namespace App\Services\Account;

use App\Models\Account;
use App\Models\Accountfile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// use Illuminate\Support\Facades\Log;

class AccountsService
{
    public function create($request) {
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

        return $account -> title . " writing account successfully posted.";
    }

    public function getMine() {
        $accounts = Auth::user() -> accounts() -> paginate(10);

        foreach ($accounts as $account) {
            $account -> Files;
        }

        return $accounts;
    }

    public function getSomeForDisplay() {
        $accounts = Account::query() -> where('display', true) -> take(10) -> get();

        foreach ($accounts as $account) {
            $account -> User;
        }

        return $accounts;
    }

    public function getAllPaginated() {
        $accounts = Account::query() -> where('display', true) -> paginate(10);

        foreach ($accounts as $account) {
            $account -> User;
        }

        return $accounts;
    }
}
