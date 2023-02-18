<?php

namespace App\Http\Controllers\Api\Liaison;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Liaison\BrokersService;

class BrokersController extends Controller
{
    public function getAll(Request $request, BrokersService $brokers_service){
        
        return response() -> json([
            'brokers' => $brokers_service -> getAll()
        ]);
        
    }

    public function getAllPaginated(Request $request, BrokersService $brokers_service){
        
        return response() -> json(
            $brokers_service -> getAllPaginated()
        );
        
    }

    public function getMyBrokers(BrokersService $brokers_service){
        
        return response() -> json([
            'brokers' => $brokers_service -> getMyBrokers(),
            'status' => 200
        ]);
        
    }

    public function getOneBroker(Request $request, BrokersService $brokers_service){
        
        return response() -> json([
            'broker' => $brokers_service -> getOneBroker($request),
            'status' => 200
        ]);
        
    }
}
