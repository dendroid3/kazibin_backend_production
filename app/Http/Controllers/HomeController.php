<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        $statistics = $this -> getStatistics();
        return view('home', ['user' => Auth::user(), 'statistics' => $statistics]);
    }

    public function getStatistics()
    {
        $verifications = 1;
        $taskers = Tasker::count();
        $managed_accounts = Managedaccount::count();
        $services = Service::count();
        $users = User::query() -> where('role', "User") -> count();
        $total_revenue = "40,000,000";

        return [
            'users' => $users,
            'verifications' => $verifications,
            'taskers' => $taskers,
            'managed accounts' => $managed_accounts,
            'services' => $services,
            'total revenue' => $total_revenue,
        ];
    }
}
