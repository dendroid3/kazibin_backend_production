<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Models\Managedaccountrevenue;

class TaskerController extends Controller
{
    public function getManagedAccounts(Request $request)
    {
        $accounts = Auth::user()
        ->tasker
        ->managedAccounts()
        ->withSum(['revenue as debit_revenue_sum' => function ($query) {
            $query->where('type', 'Debit');
        }], 'amount') 
        ->paginate(10);

        Log::info($accounts);
        return view('tasker/managed_accounts', [
            'accounts' => $accounts,
        ]);
    } 

    public function addEarning(Request $request)
    {
        $tasker = Auth::user() -> tasker;
        $tasker -> score = ($tasker -> score) + 1;

        $totalRevenue = $tasker -> managedAccounts -> flatMap(function ($managedAccount) {
            return $managedAccount -> revenue;
        }) -> where('type', 'Debit') -> sum('amount');

        if($totalRevenue > 100 && $totalRevenue < 1000)
        {
            $tasker -> status = "Standard";
        }

        if($totalRevenue > 1000)
        {
            $tasker -> status = "Premium";
        }

        $tasker -> push();

        $revenue = new Managedaccountrevenue;
        $revenue -> type = "Debit";
        $revenue -> amount = $request -> amount;
        $revenue -> managedaccount_id = $request -> managed_account_id;
        $revenue -> description = $request -> description;
        $revenue -> save();

        return redirect()->back();
    }
}
