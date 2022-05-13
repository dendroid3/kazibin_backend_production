<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function loginUser(Request $request){
        if(Auth::attempt(['email' => $request -> email, 'password' => $request -> pass])){
            $user = Auth::user();
            //We use laravel passport for authentication, It is the one responsible for creating this token.
            $token = $user -> createToken( env('APP_NAME') ) -> accessToken;
            return response() -> json([
                'token' => $token,
                'user' => $user,
            ]);
        } 
        return response() -> json([
            'error' => 'user not found'
        ], 201);
    }

    public function logoutUser(Request $request){
        Auth::user() -> tokens ->each(function($token, $key) {
            $token->delete();
        });
        return response() -> json([
            'success' => 'you are logged out'
        ]);
    }
}
