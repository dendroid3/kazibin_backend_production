<?php

namespace App\Services\ManagedAccount;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

use App\Services\SystemLog\LogCreationService;

use App\Models\ManagedAccount;
// use Illuminate\Support\Facades\Log;

class ManagedAccountService
{
    public function create ($request, $log_creation_service)
    {
        $account = new ManagedAccount();
        $account -> user_id = Auth::user() -> id;
        $account -> code = strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(3));
        $account -> status = 'pending';
        $account -> provider = $request -> provider;       
        $account -> email = $request -> email;
        $account -> provider_identifier = $request -> provider_identifier ?? null;
        $account -> proxy = $request -> proxy ?? null;
        $account -> save();

        $log_creation_service -> createSystemMessage(
            Auth::user() -> id,
            "Account Management Request Created for " . $account -> provider . " with email " . $account -> email . " and code " . $account -> code . ". Waiting for approval.",
            $account -> id, 
            'Account Management Request Created'
        );

        return "Request submitted successfully. We will get back to you shortly.";
    }

    public function get ($request)
    {
        $accounts = ManagedAccount::query() 
        -> where('user_id', Auth::user() -> id) 
        -> when($request -> has('is_filtered'), function ($query) use ($request) {
            return $query -> where('status', $request -> filter_code);
        })
        -> with([
            'details', 
            'revenue' => function ($query) {
                $query -> where('type', 'Debit');
            }
        ]) 
        -> paginate(10);

        $accounts -> getCollection() -> transform(function ($account) {
            // $totalRevenue = $account -> revenue -> where('type', 'Debit') -> sum('amount');
            $totalOwing = $account -> revenue -> where('type', 'Credit') -> sum('amount'); //($totalRevenue - $account -> revenue -> where('type', 'Credit') -> sum('amount')) * (($account -> tasker_rate + $account -> jobraq_rate) / 100);

            Log::info("totalOwing" . $totalOwing);
            // $account -> total_revenue = $totalRevenue;
            $account -> total_owing = $totalOwing . " (" . $account -> tasker_rate + $account -> jobraq_rate . "%)";

            return $account;
        });

        return $accounts;
    }
}