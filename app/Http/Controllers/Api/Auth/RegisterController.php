<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Mail\VerificationEmail;
use App\Models\User;

class RegisterController extends Controller
{
    
    public function create(Request $request){

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'max:25', 'bail'],
            'phone_number' => ['required', 'unique:users', 'between:9,10', 'bail'],
            'email' => ['required', 'unique:users',  'bail'],
            'pass' => ['required', 'min:6'],
        ]);
        
        if ($validator->fails()) {
            return  response() -> json([
                'errors' => $validator -> errors()
            ], 201);
        }

        $user = new User;
        $user -> id = Str::orderedUuid() -> toString();
        $user -> username = $request -> username;
        $user -> phone_number = $request -> phone_number;
        $user -> email = $request -> email;
        $user -> password = Hash::make($request['pass']);
        $user -> save();

        /* 
            We create a unique code that will be used to identify the user on the platform. This negates the need for usernames to be unique or a need for any other unique identifier, 
            save for the uuid used to identify the user in the database.
        */
        if($user){
            $user -> email_verification = $this -> getRandomString(40);
            $user -> phone_verification = Str::random(3) . '-' . Str::random(3);
            $user -> code = strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(floor(rand(4,7))));
            $user -> push(); 
        }

        $verification_email = $this->sendVerificationEmail($user);
        
        return response() 
                -> json([
                    'user' => $user,
                    //We use laravel passport for authentication, It is the one responsible for creating this token.
                    'token' => $user ->createToken(env('APP_NAME'))-> accessToken, 
                ], 200);
    }

    public function createProfile(Request $request){
        $user = Auth::user();
        $user -> level = 0;
        $user -> course = $request -> course;
        $user -> bio = $request -> bio;
        $user -> push();

        return response() -> json([
            'user' => $user,
            'success' => 'profile created'
        ]);
    }

    public function verifyEmailAccount(Request $request){
        //check verification code exists
        $user = DB::table('users') -> where([
            ['email_verification', '=', $request->email_verification],
            ['email', '=', $request->email]
        ]) -> first();
        if(!$user){
            $user_verified = DB::table('users') -> where([
                ['email_verification', '=', null],
                ['email', '=', $request->email]
            ]) -> first();

            //If verification code does is null and email exists, then the email has already been verified
            if($user_verified){
                return response() -> json([
                    'error' => 'email already verified'
                ], 202);
            }

            //If the email does not exist, or exists; but the verification code is not null but is not the same as sent in the request, then the verification fails.
            return response() -> json([
                'error' => 'verification failed'
            ], 201);
        }

        /*
            If both the email and verification code match a record in the database, then the verification code in the record is put to null to signify that it has been verified,
            take home point is that verified accounts have a null record in the 'email_verification' field.
        */
        $user = DB::table('users') -> where([
            ['email_verification', '=', $request->email_verification],
            ['email', '=', $request->email]
        ]) -> update([
            'email_verification' => null
        ]);

        return response() -> json([
            'success' => 'verification succeeded'
        ]);
    }

    public function getRandomString($number){
        // This function is meant to ensure that no two 'email_verification' field data are similar.
        $random_string = Str::random($number);
        $exist = DB::table('users')->where('email_verification', $random_string) -> exists();
        while ($exist) {
            $this -> getRandomString($number);
        }
        return $random_string;
    }

    public function sendVerificationEmail($user){
        //This function is for sending the verification email once the user is created.
        Mail::to($user -> email)->send(new VerificationEmail($user));
        return new VerificationEmail($user);
    }
}
