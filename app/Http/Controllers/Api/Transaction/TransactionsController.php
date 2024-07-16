<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Services\SystemLog\LogCreationService;
use Illuminate\Http\Request;
use App\Services\Transaction\TransactionService;

class TransactionsController extends Controller
{
    public function claimTransaction(Request $request, TransactionService $transaction_service, LogCreationService $log_service)
    {
        return response() -> json(
            $transaction_service -> claimTransaction($request, $log_service)
        );
    }

    public function getTransactions(Request $request, TransactionService $transaction_service)
    {
        return response() -> json(
            $transaction_service -> getTransactions($request)
        );
    }

    public function getTransactionsPaginated(Request $request, TransactionService $transaction_service)
    {
        return response() -> json(
            $transaction_service -> getTransactionsPaginated($request)
        );
    }

    public function depositFromMpesa(Request $request, TransactionService $transaction_service)
    {
        return response() -> json(
            $transaction_service -> requestForCompletionOfTransactionFromCustomer($request)
        );
    }
}
