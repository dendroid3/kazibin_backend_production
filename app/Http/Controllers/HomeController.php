<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Managedaccountrevenue;
use App\Models\Managedaccount;
use App\Models\Service;
use App\Models\Tasker;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::user() ->role == "user"){
            return response() -> json(['error' => 'Unauthorized'], 401);
        }

        $statistics = $this -> getStatistics();
        return view('home', ['user' => Auth::user(), 'statistics' => $statistics]);
    }

    public function getStatistics()
    {
        if(Auth::user() -> role == "Admin" || Auth::user() -> role == "admin"){
            $verifications = 1;
            $taskers = Tasker::count();
            $managed_accounts = Managedaccount::count();
            $services = Service::count();
            $users = User::query() -> where('role', "User") -> count();
            $total_revenue = number_format(Managedaccountrevenue::query()->where('type', 'Debit')->sum('amount'), 2);
            $total_revenue = "$" . $total_revenue;

            return [
                'users' => $users,
                'verifications' => $verifications,
                'taskers' => $taskers,
                'managed accounts' => $managed_accounts,
                'services' => $services,
                'total revenue' => $total_revenue,
            ];
        } else if(Auth::user() -> role == "Tasker" || Auth::user() -> role == "tasker"){
            $tasker = Tasker::query() 
            -> where('user_id', Auth::user() -> id) 
            -> with([
                'managedAccounts',
                'managedAccounts.revenue'
            ])
            -> first();

            $managed_accounts = $tasker -> managedAccounts -> count();

            $total_revenue = $tasker->managedAccounts->map(function($account) {
                return $account->revenue->where('type', 'Debit')->sum('amount');
            })->sum();
            $total_revenue = number_format($total_revenue, 2);
            $total_revenue = "$" . $total_revenue;

            return [
                'managed accounts' => $managed_accounts,
                'total revenue' => $total_revenue,
            ];
        } 
    }
}
