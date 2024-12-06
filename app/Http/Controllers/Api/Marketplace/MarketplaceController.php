<?php

namespace App\Http\Controllers\Api\Marketplace;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Services\Marketplace\MarketplaceService;
use App\Services\SystemLog\LogCreationService;

class MarketplaceController extends Controller
{
    public function create(Request $request, MarketplaceService $marketplace_service, LogCreationService $log_service) {
        return response() -> json([
            'message' => $marketplace_service -> create($request, $log_service)
        ]);
    } 

    public function update(Request $request, MarketplaceService $marketplace_service, LogCreationService $log_service) {
        return response() -> json([
            'message' => $marketplace_service -> update($request, $log_service)
        ]);
    } 

    public function delete(Request $request, MarketplaceService $marketplace_service, LogCreationService $log_service) {
        return response() -> json([
            'message' => $marketplace_service -> delete($request, $log_service)
        ]);
    } 

    public function getMine(Request $request, MarketplaceService $marketplace_service) {
        return response() -> json([
            'accounts' => $marketplace_service -> getMine($request)
        ]);
    }

    public function getSomeForDisplay(Request $request, MarketplaceService $marketplace_service) {
        return response() -> json([
            'accounts' => $marketplace_service -> getSomeForDisplay($request)
        ]);
    }

    public function getAllPaginated(Request $request, MarketplaceService $marketplace_service) {
        return response() -> json([
            'accounts' => $marketplace_service -> getAllPaginated($request)
        ]);
    }

    public function getCurrentAccount(Request $request, MarketplaceService $marketplace_service) {
        $account = $marketplace_service -> getCurrentAccount($request);

        if($account === 404){
            return response() -> json([
                'message' => 'Account not found'
            ], 404);
        }

        return response() -> json([
            'account' => $account
        ]);
    }
    
}
