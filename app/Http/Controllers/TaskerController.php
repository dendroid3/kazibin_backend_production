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
        ->paginate(1);

        Log::info($accounts);
        return view('tasker/managed_accounts', [
            'accounts' => $accounts,
        ]);
    } 

    public function addEarning(Request $request)
    {
        $revenue = new Managedaccountrevenue;
        $revenue -> type = "Debit";
        $revenue -> amount = $request -> amount;
        $revenue -> managedaccount_id = $request -> managed_account_id;
        $revenue -> description = $request -> description;
        $revenue -> save();

        return redirect()->back();
    }
}
