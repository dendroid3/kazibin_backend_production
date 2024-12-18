<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Tasker;

class AdminController extends Controller
{
    public function taskers(Request $request)
    {
        $status = request()->get('status');

        Log::info($status);

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
        // ->withCount('tasker.user') // Count Managedaccounts related to Tasker
        ->paginate(1);

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
}
