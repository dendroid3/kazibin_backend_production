<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function loginUser(Request $request){
        if(Auth::attempt(['email' => $request -> email, 'password' => $request -> pass])){
            $user = Auth::user();
            $user -> writer;
            $user -> broker;
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
        DB::table('oauth_access_tokens') -> where('user_id', Auth::user() -> id)  -> delete();

        return response() -> json([
            'logout' => true
        ]);
    }

}
