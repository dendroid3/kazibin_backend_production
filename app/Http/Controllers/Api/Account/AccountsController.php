<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Services\Account\AccountsService;
use App\Services\SystemLog\LogCreationService;

class AccountsController extends Controller
{
    public function create(Request $request, AccountsService $accounts_service, LogCreationService $log_service) {
        return response() -> json([
            'message' => $accounts_service -> create($request, $log_service)
        ]);
    }

    public function getMine(Request $request, AccountsService $accounts_service) {
        return response() -> json([
            'accounts' => $accounts_service -> getMine($request)
        ]);
    }

    public function getSomeForDisplay(Request $request, AccountsService $accounts_service) {
        return response() -> json([
            'accounts' => $accounts_service -> getSomeForDisplay($request)
        ]);
    }

    public function getAllPaginated(Request $request, AccountsService $accounts_service) {
        return response() -> json([
            'accounts' => $accounts_service -> getAllPaginated()
        ]);
    }

    public function getCurrentAccount(Request $request, AccountsService $accounts_service) {
        $account = $accounts_service -> getCurrentAccount($request);

        if($account == 404){
            return response() -> json([
                'message' => 'Account not found'
            ], 404);
        }

        return response() -> json([
            'account' => $account
        ]);
    }
    
}
