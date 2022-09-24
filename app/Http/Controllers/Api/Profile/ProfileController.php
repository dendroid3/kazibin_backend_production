<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Profile\ProfileService;

class ProfileController extends Controller
{
    public function getDashboardDetails(Request $request, ProfileService $profile_service){

        return response() -> json([
            $profile_service -> getDashboardDetails($request)
        ]);
      
    }

    public function getBrokerMetrics(Request $request, ProfileService $profile_service){

        return response() -> json([
            $profile_service -> getBrokerMetrics($request)
        ]);
    
    }

    public function getWriterMetrics(Request $request, ProfileService $profile_service){

        return response() -> json([
            $profile_service -> getWriterMetrics($request)
        ]);
    
    }
    
    public function changeAvailability(Request $request, ProfileService $profile_service){

        return response() -> json([
            $profile_service -> changeAvailability($request)
        ]);
    
    }

    public function changeMyBio(Request $request, ProfileService $profile_service){

        return response() -> json([
            $profile_service -> changeMyBio($request)
        ]);
    
    }

    public function setMyInterests(Request $request, ProfileService $profile_service){

        return response() -> json([
            $profile_service -> setMyInterests($request)
        ]);
    
    }
}
