<?php

namespace App\Services\Transaction;

use Illuminate\Support\Facades\Auth;
use App\Services\SystemLog\LogCreationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;

// use App\Events\MpesaTransactionComplete;

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

    public function getTransactions(Request $request)
    {
        $transactions = Auth::user() -> transactions() -> orderBy('created_at', 'DESC') -> take(10) -> get();

        return $transactions;
    }
    
    public function getTransactionsPaginated(Request $request){
        $transactions = Auth::user() -> transactions() -> orderBy('created_at', 'DESC') -> paginate(10);#-> take(10) -> get();

        return $transactions;
    }

    public function getAccessToken()
    {
        $response = Http::withHeaders([
            "Authorization" => "Basic " . base64_encode( env('MPESA_CONSUMER_KEY') .':'.env('MPESA_CONSUMER_SECRET') )
        ])
        -> get(env('MPESA_AUTH_ENDPOINT'));
     
        return $response['access_token'];
    }

    public function requestForCompletionOfTransactionFromCustomer(Request $request)
    {
        $time_now = Carbon::now() -> format('YmdHis');
        $data = [
            "BusinessShortCode" => env('MPESA_SHORTCODE'),
            "Password" => base64_encode(env('MPESA_SHORTCODE') . env('MPESA_PASSKEY') . $time_now),
            "Timestamp" => $time_now,
            "TransactionType" => "CustomerPayBillOnline",
            "Amount" => $request -> amount,
            "PartyA" => $request -> phone_number,
            "PartyB" => env('MPESA_SHORTCODE'),
            "PhoneNumber" => $request -> phone_number,
            "CallBackURL" => env('MPESA_DEPOSIT_REQUEST_CALLBACK'),
            "AccountReference" => strtoupper(Auth::user() -> code . ": " . Auth::user() -> username),
            "TransactionDesc" => "Payment of X" 
        ];
        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $this -> getAccessToken()
        ])
        -> post(env('MPESA_STK_ENDPOINT'), $data);

        $decoded_response = json_decode($response);

        if(isset($decoded_response -> errorCode))
        {
            if($decoded_response -> errorCode)
            {
                return [
                    'message' => 'Could not initiate transaction, kindly try again after a few minutes'
                ];
            }
        }

        $Mpesa = new Mpesa;
        $Mpesa -> checkout_request_id = $response['CheckoutRequestID'];
        $Mpesa -> user_id = Auth::user() -> id;
        $Mpesa -> amount = $request['amount'];
        $Mpesa -> paying_phone_number = $request['phone_number'];

        // request for MPesa PIN not made
        if(isset($decoded_response -> ResponseCode))
        {
            if($decoded_response -> ResponseCode > 0)
            {
                $Mpesa -> status = 1;
                $Mpesa -> save();
                return [
                    'message' => 'Could not initiate transaction, kindly try again after a few minutes'
                ];
            }   
        }

        // request for MPesa PIN made successfully : the status will be the default 0!
        $Mpesa -> save();

        return true;
        // return $this -> getAccessToken();
    }

    public function recordTransaction(Request $request)
    {
        $Mpesa = Mpesa::query() -> where('checkout_request_id', $request['Body']['stkCallback']['CheckoutRequestID']) -> first();

        if($request['Body']['stkCallback']['ResultCode'] > 0)
        {
            $Mpesa -> status = 1;
            $Mpesa -> push();

            $Message = 'Deposit of ' . $Mpesa -> amount . ' from MPesa was unsuccessful.';

            LogCreationService -> createSystemMessage($Mpesa -> user_id, $Message, $Mpesa -> id, "Deposit Unsuccessful", 'error');

            // event(new MpesaTransactionComplete($Message, Auth::user() -> id));

            return false;
        }

        // Record Mpesa for admin
        $Mpesa -> receipt_number = $request['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
        $Mpesa -> transaction_date = $request['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'];
        $Mpesa -> status = 2;
        $Mpesa -> push();

        //Debit user account in transactions
        $transaction = new Transaction;
        $transaction -> user_id = $Mpesa -> user_id;
        $transaction -> mpesa_transaction_id = $Mpesa -> receipt_number;
        $transaction -> type = "Debit";
        $transaction -> amount = $Mpesa -> amount;
        $transaction -> description = "Deposit from Mpesa, reference code: " . $Mpesa -> receipt_number . ".";
        $transaction ->save();

        $Message = 'Deposit of ' . $Mpesa -> amount . ' from MPesa made successfully.';

        LogCreationService -> createSystemMessage($Mpesa -> user_id, $Message, $Mpesa -> id, "Deposit Unsuccessful");

        // event(new MpesaTransactionComplete($Message, Auth::user() -> id, 'success'));

        return;

    }
}
