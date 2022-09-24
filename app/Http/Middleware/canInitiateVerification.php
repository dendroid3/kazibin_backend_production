<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mpesa;


class canInitiateVerification
{
    public function handle(Request $request, Closure $next)
    {
        $total_debit = Auth::user() -> transactions() -> where('type', 'Debit') -> sum('amount');
        $total_credit = Auth::user() -> transactions() -> where('type', 'Credit') -> sum('amount');
        $balance = $total_debit - $total_credit;

        if($balance < 500){
            return response() -> json([
                'error' => 'You do not have enough money in your account to initialise the verification process, this process costs 500, kindly top up.'
            ], 201);
        } 

        $verification_applications = Auth::user() -> verifications;


        foreach ($verification_applications as $application) {
            if($application -> status == 1){
                return response() -> json([
                    'error' => 'You already have an application being proccessed. If you made an error in that application kindly write to us via our official email'
                ], 201);
            }
        }

        $mpesa_transaction = Mpesa::where('mpesa_transaction_id', $request -> mpesa_transaction_id) -> first();

        if(!$mpesa_transaction){
            return response() -> json([
                'error' => 'We did not find an mpesa transaction with that code'
            ], 202);
        } else if($mpesa_transaction -> status == 2){
            return response() -> json([
                'error' => 'We found that transaction but it is already claimed'
            ], 202);
        } else if(!($mpesa_transaction -> msisdn == Auth::user() -> phone_number)){
            return response() -> json([
                'error' => 'We found that transaction but it did not originate from the phone number you registered with. Kindly send the money using the phone number ' . Auth::user() -> phone_number
            ], 202);
        }

        return $next($request);
    }
}
