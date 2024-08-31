<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Account\AccountsService;

class AccountsController extends Controller
{
    public function create(Request $request, AccountsService $accounts_service) {}

    public function getSomeForDisplay(Request $request, AccountsService $accounts_service) {
        return response() -> json([
            'accounts' => $accounts_service -> getSomeForDisplay()
        ]);
    }

    public function getAllPaginated(Request $request, AccountsService $accounts_service) {
        return response() -> json([
            'accounts' => $accounts_service -> getAllPaginated()
        ]);
    }
}
