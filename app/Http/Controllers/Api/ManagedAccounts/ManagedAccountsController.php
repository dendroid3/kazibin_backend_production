<?php

namespace App\Http\Controllers\Api\ManagedAccounts;

use App\Http\Controllers\Controller;
use App\Services\ManagedAccounts\ManagedAccountService;
use Illuminate\Http\Request;

class ManagedAccountsController extends Controller
{
    public function get(Request $request, ManagedAccountService $managed_account_service)
    {
        return response()->json($managed_account_service->get($request));
    }
}
