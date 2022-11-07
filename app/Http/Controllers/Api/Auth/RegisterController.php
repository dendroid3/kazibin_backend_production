<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Services\Auth\RegistrationService;
class RegisterController extends Controller
{
    
    public function create(Request $request, RegistrationService $register_service){

        $response = $register_service -> create($request);
        
        if(!$response['validated']){
            return response() -> json(['errors' => $response['errors']], 201);
        }

        return response() -> json($response);
    
    }

    public function createProfile(Request $request, RegistrationService $register_service){

        return response() -> json([
            'user' => $register_service -> createProfile($request),
            'success' => 'profile created'
        ]);

    }

    public function verifyEmail(Request $request, RegistrationService $register_service){
        $response = $register_service -> verifyEmail($request);

        return response() -> json([
            'success' => $response
        ], $response ? 200 : 201);

    }

    public function isAccountVerified(Request $request, RegistrationService $register_service)
    {
        return response() -> json(
            $register_service ->isAccountVerified()
        );
    }

    public function resendVerificationEmail(RegistrationService $register_service)
    {
        return response() -> json(
            $register_service ->sendVerificationEmail(Auth::user())
        );
    }

    public function initialisePasswordReset(Request $request, RegistrationService $register_service)
    {
        $response = $register_service ->initialisePasswordReset($request);

        return response() -> json([
            'success' => $response
        ], $response ? 200 : 201);
    }
    
    public function resetPassword(Request $request, RegistrationService $register_service)
    {
        $response = $register_service ->resetPassword($request);

        return response() -> json([
            'success' => $response
        ], $response ? 200 : 201);
    }

}
