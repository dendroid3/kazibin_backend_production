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

use App\Models\Account;
use App\Models\Accountfile;
use App\Models\Revenue;
use App\Models\Transaction;
// use Illuminate\Support\Facades\Log;

class ManagedAccountService
{
    public function create (Request $request)
    {

    }

    public function get ($request, $managed_account_service)
    {
        $accounts = ManagedAccount::query() 
        -> where('user_id', Auth::user() -> id) 
        -> with([
            'details', 
            'revenue' => function ($query) {
                $query -> where('type', 'Debit');
            }
        ]) 
        -> paginate(10);

        $accounts -> getCollection() -> transform(function ($account) {
            $totalRevenue = $account -> revenue -> where('type', 'Debit') -> sum('amount');

            $account -> total_revenue = $totalRevenue;

            return $account;
        });

        return $accounts;
    }
}