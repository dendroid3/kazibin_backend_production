<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Service;
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
        $taskers = 2;
        $managed_accounts = 3;
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
