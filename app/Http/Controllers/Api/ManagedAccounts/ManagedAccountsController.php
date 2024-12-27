<?php

namespace App\Http\Controllers\Api\ManagedAccounts;

use App\Http\Controllers\Controller;
use App\Services\SystemLog\LogCreationService;
use App\Services\ManagedAccount\ManagedAccountService;
use Illuminate\Http\Request;

class ManagedAccountsController extends Controller
{
    public function create(Request $request, ManagedAccountService $managed_account_service, LogCreationService $log_creation_service)
    {
        return response()->json($managed_account_service->create($request, $log_creation_service));
    }

    public function get(Request $request, ManagedAccountService $managed_account_service)
    {
        return response()->json([
            'accounts' => $managed_account_service->get($request)
        ]);
    }
}
