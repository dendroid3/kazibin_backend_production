<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Mail\VerificationEmail;

use App\Models\User;

class RegistrationService {
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'username' => ['required', 'max:25', 'bail'],
      'phone_number' => ['required', 'unique:users', 'between:9,10', 'bail'],
      'email' => ['required', 'unique:users',  'bail'],
      'pass' => ['required', 'min:6'],
    ]);
    
    if ($validator->fails()) {
        return  ['validated' => false, 'errors' => $validator -> errors()];
    }

    $user = new User;
    $user -> id = Str::orderedUuid() -> toString();
    $user -> username = strtoupper($request -> username);
    $user -> phone_number = $request -> phone_number;
    $user -> email = $request -> email;
    $user -> broker_score = 1;
    $user -> writer_score = 1;
    $user -> password = Hash::make($request['pass']);
    $user -> last_activity = Carbon::now();
    $user -> save();

    
    /* 
        We create a unique code that will be used to identify the user on the platform. This negates the need for usernames to be unique or a need for any other unique identifier, 
        save for the uuid used to identify the user in the database.
    */
    if($user){
      $user -> email_verification = $this -> getRandomString(40);
      $user -> phone_verification = Str::random(3) . '-' . Str::random(3);
      $user -> code = strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(3));
      $user -> push(); 
    }

    // $verification_email = $this->sendVerificationEmail($user);
    $user -> writer;
    $user -> broker;

    return [
      'validated' => true,
      'user' => $user,
      'token' => $user ->createToken(env('APP_NAME'))-> accessToken
    ];
  }

  public function sendVerificationEmail($user){
      \Mail::to($user -> email)->send(new \App\Mail\VerficationOfAccount($user));
  }

  public function isAccountVerified(){
    return !(Auth::user() -> email_verification);
  }

  public function verifyEmail(Request $request)
  {
    $account = DB::table('users') 
    -> where('email_verification', $request -> email_verification) 
    -> where('email', $request -> email)
    -> first();

    if(!$account){
      return 201;
    }

    if(!$account -> email_verification){
      return 202;
    }

    DB::table('users') -> where('email_verification', $request -> email_verification) -> update([
        'email_verification' => null
    ]);
    return 200;
  }

  public function getRandomString($number)
  {
    // This function is meant to ensure that no two 'email_verification' field data are similar.
    $random_string = Str::random($number);
    $exist = DB::table('users')->where('email_verification', $random_string) -> exists();
    while ($exist) {
        $this -> getRandomString($number);
    }
    return $random_string;
  }

  public function createProfile(Request $request)
  {
    $user = User::find(Auth::user()->id);
    $user -> level = 0;
    $user -> course = $request -> course;
    $user -> bio = $request -> bio;
    $user -> push();

    return $user;
  }

  public function initialisePasswordReset(Request $request)
  {
    $email_exists = DB::table('users') -> where('email', $request -> email) -> exists();

    if($email_exists)
    {
      $token = $this -> getRandomString(floor(rand(10,30))) . '-' . $this -> getRandomString(floor(rand(10,30))) . '-' . $this -> getRandomString(floor(rand(10,30)));
      DB::table('password_resets') -> insert([
        'email' => $request -> email,
        'token' => $token
      ]);
      \Mail::to($request -> email)->send(new \App\Mail\PasswordResetMail($request -> email, $token));

      return true;
    }
  }
  
  public function resetPassword(Request $request)
  {
    $reset_link_exists = DB::table('password_resets') -> where('email', $request -> email) -> where('token', $request -> token) -> exists();

    if($reset_link_exists)
    {
      $user = User::query() -> where('email', $request -> email) -> first();

      $user -> password = Hash::make($request['password']);

      $user -> push();

      $resets = DB::table('password_resets') -> where([
        'email' => $request -> email
      ]) -> get();

      foreach ($resets as $reset) {
          
        $resets = DB::table('password_resets') -> where([
          'email' => $request -> email,
          'token' => $reset -> token
        ]) -> delete();
      }

      return true;
    }
  }
}
