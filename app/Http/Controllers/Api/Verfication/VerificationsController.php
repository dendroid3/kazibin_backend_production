<?php

namespace App\Http\Controllers\Api\Verfication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SystemLog\LogCreationService;
use App\Services\Transaction\TransactionService;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Verification;
use App\Models\Mpesa;
use App\Models\Revenue;

class VerificationsController extends Controller
{
    //there should be a part to send samples
    //additionally, they should be able to check graduation credentials
    public function initiateVerification(Request $request, LogCreationService $log_service, TransactionService $transaction_service)
    {
        // Change this destination to Digital Ocean Spaces.

        $front_url = cloudinary()->upload($request->file('front_id')->getRealPath())->getSecurePath();
        $back_id = cloudinary()->upload($request->file('back_id')->getRealPath())->getSecurePath();
        $passport_url = cloudinary()->upload($request->file('passport')->getRealPath())->getSecurePath();

        // We do not need to claim transactions anynmore!
        
        $transaction_service -> claimTransaction($request, $log_service);
        
        $transaction = new Transaction;
        $transaction -> user_id = Auth::user() -> id;
        $transaction -> type = "Credit";
        $transaction -> description = "Amount charged to initialise the verification process";
        $transaction -> amount = env('PAYMENT_VERIFICATION_COST');
        $transaction -> save();

        $verification = new Verification;
        $verification -> user_id = Auth::user() -> id;
        $verification -> transaction_id = $transaction -> id;
        $verification -> front_id_url = $front_url;
        $verification -> back_id_url = $back_id;
        $verification -> passport_url = $passport_url;
        $verification -> save();

        $revenue = new Revenue;
        $revenue -> transaction_id = $transaction -> id;
        $revenue -> type = "Verification";
        $revenue -> amount = env('PAYMENT_VERIFICATION_COST');
        $revenue -> save();

        $user_message = 'You successfully initialised the verification process, the admins will go through your application and resolve in, at most, 72 hours.';

        $log_service -> createSystemMessage(
            Auth::user() -> id,
            $user_message,
            $verification -> id,
            'Verification Initialised'
        );

        return response() -> json(
            $user_message
        );
    
    }
}
