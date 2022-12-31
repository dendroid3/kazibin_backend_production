<?php

namespace App\Services\Transaction;

use Illuminate\Support\Facades\Auth;
use App\Services\SystemLog\LogCreationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Mpesa;
use App\Models\Transaction;

class TransactionService{
    public function claimTransaction(Request $request, LogCreationService $log_service)
    {
        $mpesa_transaction = Mpesa::where('mpesa_transaction_id', $request -> mpesa_transaction_id) -> first();

        if(!$mpesa_transaction){
            return ['error' => true, 'message' => 'We have not recieved any transaction with that code. Ensure that the recieving organisation`s name is `WAZO TANK 7.` If it is and you get this error message, kindly contact support on 0705 715 099'];
        }

        if($mpesa_transaction -> status == 2){
            if($mpesa_transaction -> User -> id == Auth::user() -> id){
                return ['error' => true, 'message' => 'You already claimed this transaction.'];
            }

            return ['error' => true, 'message' => 'transaction already claimed by ' . $mpesa_transaction -> User -> username . '. Let us know if this is was a mistake.'];
        }


        $transaction = new Transaction;
        $transaction -> user_id = Auth::user() -> id;
        $transaction -> type = "Debit";
        $transaction -> description = 'Deposit from Mpesa. Mpesa transaction id ' . $mpesa_transaction -> mpesa_transaction_id;
        $transaction -> amount = $mpesa_transaction -> amount;
        $transaction -> save();

        $mpesa_transaction -> user_id = Auth::user() -> id;
        $mpesa_transaction -> status = 2;
        $mpesa_transaction -> push();
        
        $user_message = 'You have successfully deposited KES ' . $mpesa_transaction -> amount .' from mpesa transaction code ' . $mpesa_transaction -> mpesa_transaction_id; 

        $log_service -> createSystemMessage(
            Auth::user() -> id,
            $user_message,
            $mpesa_transaction -> id,
            'Mpesa Deposit'
        );

        return ['success' => true, 'message' => $user_message];

    }

    public function getTransactions(Request $request){
        $transactions = Auth::user() -> transactions() -> orderBy('created_at', 'DESC') -> take(10) -> get();

        return $transactions;
    }
    
    public function getTransactionsPaginated(Request $request){
        $transactions = Auth::user() -> transactions() -> orderBy('created_at', 'DESC') -> paginate(10);#-> take(10) -> get();

        return $transactions;
    }
    
}