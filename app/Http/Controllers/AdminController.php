<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Tasker;
use App\Models\ManagedAccount;
use App\Models\Managedaccountrevenue;
use Mail;

use App\Services\SystemLog\LogCreationService;

class AdminController extends Controller
{
    public function getTaskers(Request $request)
    {
        $status = request()->get('status');

        $taskers = User::query()
        ->where('role', 'tasker')
        ->when($status, function ($query) use ($status) {
            $query->whereHas('tasker', function ($query) use ($status) {
                $query->where('status', $status); // Filter by status in the Tasker model
            });
        })
        ->with([
            'tasker', // Eager load Tasker
            'tasker.managedAccounts', // Eager load Managedaccounts of Tasker
            'tasker.managedAccounts.revenue' => function ($query) {
                $query->where('type', 'Debit'); // Filter by "Debit" type for revenue
            },
        ])
        ->orderBy('last_activity', 'desc')
        // ->withCount('tasker.user') // Count Managedaccounts related to Tasker
        ->paginate(10);

        // Calculate total revenue for each tasker
        $taskers->getCollection()->transform(function ($tasker) {
            $totalRevenue = $tasker->tasker->managedAccounts->flatMap(function ($managedAccount) {
                return $managedAccount->revenue; // Get all related revenue entries
            })->where('type', 'Debit') // Only include 'Debit' type revenue
            ->sum('amount'); // Sum the 'amount' fields

            // Attach the total revenue as a custom attribute
            $tasker->total_revenue = $totalRevenue;

            return $tasker;
        });


        $total_taskers = User::query() -> where('role', 'tasker') -> count();
        $taskers_statuses = ["Probation", "Standard", "Premium"];
        $taskers_statistics = array();

        foreach ($taskers_statuses as $key => $value) {
            $taskers_in_this_status = Tasker::query() -> where('status', $value) -> count();

            $taskers_statistics[$value] = $taskers_in_this_status;
        }


        Log::info($taskers);
        return view('admin/taskers', [
            'user' => Auth::user(), 
            'taskers' => $taskers,
            'taskers_statistics' => $taskers_statistics,
        ]);
    }

    public function createTasker(Request $request)
    {
        $user = User::find($request->user_id);
        $user -> role = "Tasker";
        $user -> push();

        $tasker = new Tasker;
        $tasker -> user_id = $request -> user_id;
        $tasker -> status = "Probation";
        $tasker -> save();

        return response() -> json($tasker);
    }

    public function getTasker(Request $request)
    {
        $tasker = Tasker::query() -> where('id', $request -> tasker_id) 
        -> with('user')
        -> first();

        $managedAccounts = $tasker->managedAccounts()
        ->with([
            'user',
        ])
        ->withSum(['revenue as debit_revenue_sum' => function ($query) {
            $query->where('type', 'Debit');
        }], 'amount') 
        ->paginate(10);  

        $tasker_statistics = [
            'total accounts' => $tasker->managedAccounts->count(),
            'total revenue' => $tasker->managedAccounts->flatMap(function ($managedAccount) {
                return $managedAccount->revenue;
            })->where('type', 'Debit')->sum('amount'),
        ];

        return view('admin/tasker', [
            'tasker' => $tasker,
            'managedAccounts' => $managedAccounts,
            'taskerStatistics' => $tasker_statistics,
        ]);
    }

    public function getManagedAccounts(Request $request)
    {
        $status = request()->get('status');

        $accounts = ManagedAccount::query()
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status); 
        })
        -> with([
            'tasker',
            'user',
            'details',
            'revenue'
        ])
        ->withSum(['revenue as debit_revenue_sum' => function ($query) {
            $query->where('type', 'Debit');
        }], 'amount')
        -> paginate(10);

        Log::info($accounts);
        $managed_accounts_statistics = [
            // 'total_accounts' => ManagedAccount::query() -> count(),
            'pending' => ManagedAccount::query() -> where('status', 'pending') -> count(),
            'active' => ManagedAccount::query() -> where('status', 'active') -> count(),
            'closed' => ManagedAccount::query() -> where('status', 'closed') -> count(),
        ];

        return view('admin/managed_accounts', [
            'accounts' => $accounts,
            'managed_accounts_statistics' => $managed_accounts_statistics
        ]);
    }

    public function getManagedAccount(Request $request)
    {
        $account = ManagedAccount::query() -> where('id', $request -> account_id) 
        -> with([
            'tasker',
            'user',
            'details',
        ])
        -> first();

        $account -> revenue = Managedaccountrevenue::query() -> where('managedaccount_id', $account -> id) -> paginate(10);
        
        $total_revenue = $account->revenue->where('type', 'Debit')->sum('amount');

        $account_statistics = [
            "owner`s" => '$' . ($total_revenue * $account->owner_rate / 100) . " (" . $account->owner_rate . "%)",
            "tasker`s" => '$' . ($total_revenue * $account->tasker_rate / 100) . " (" . $account->tasker_rate . "%)",
            "jobraq`s" => '$' . ($total_revenue * $account->jobraq_rate / 100) . " (" . $account->jobraq_rate . "%)",
        ];

        return view('admin/managed_account', [
            'account' => $account,
            'account_statistics' => $account_statistics
        ]);
    }   

    public function updateManagedAccount(Request $request, LogCreationService $log_creation_service)
    {
        // Get tasker by user_id
        $user = User::where('code', $request -> tasker) -> first();
        if(!$user) {
            return response() -> json(['error' => 'Tasker not found'], 404);
        }

        $tasker = $user -> tasker;
        $tasker_id = $tasker -> id;
        $account = ManagedAccount::find($request -> account_id);

        if(!$account -> tasker && $request -> tasker) {

            $log_creation_service -> createSystemMessage(
                $account -> user_id,
                "Account Management Request Approved for " . $account -> provider . " with email " . $account -> email . " and code " 
                . $account -> code . ". Jobraq will now manage this account at a rate of " . ($request -> jobraq_rate + $request -> tasker_rate) . "%.",
                $account -> id, 
                'Account Management Request Approved'
            );

            $username = $account -> user -> username;

            $payday = $request -> payday;
            $pay_cut = $request -> jobraq_rate + $request -> tasker_rate;
            
            Mail::to("kazibin66@gmail.com")->send(new \App\Mail\AccountManagementRequestApproved($username, $payday, $pay_cut));

            Log::info("username" . $username);
        }

        $account -> status = $request -> status;
        $account -> payday = $request -> payday;
        $account -> tasker_id = $tasker_id;
        $account -> tasker_rate = $request -> tasker_rate;
        $account -> owner_rate = $request -> owner_rate;
        $account -> jobraq_rate = $request -> jobraq_rate;
        $account -> push();

        return redirect()->back();
    }
}
