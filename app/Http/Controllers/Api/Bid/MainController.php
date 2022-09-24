<?php

namespace App\Http\Controllers\Api\Bid;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Bid\BidsService;
use App\Services\SystemLog\LogCreationService;

class MainController extends Controller
{
    public function create(Request $request, BidsService $bids_service, LogCreationService $log_service){

        return response() -> json(
            $bids_service -> registerBid($request, $log_service)
        );

    }

    public function getMyBids(BidsService $bids_service){

        return response() -> json([
            'bids' => $bids_service -> getMyBids()
        ]);

    }

    public function getMyBidsPaginated(Request $request, BidsService $bids_service){

        return response() -> json(
            $bids_service -> getMyBidsPaginated($request)
        );

    }
    

    public function sendBidMessage(Request $request, BidsService $bids_service){
        
        return response() -> json(
            $bids_service -> sendBidMessage($request)
        );

    }

    public function getBidMessages(Request $request, BidsService $bid_service){

        return response() -> json(
            $bid_service -> getBidMessages($request)
        );
        
    }

    public function acceptBid(Request $request,  LogCreationService $log_service, BidsService $bids_service){

        return response() -> json(
            $bids_service -> acceptBid($request, $log_service)
        );

    }

    public function rejectBid(Request $request,  LogCreationService $log_service, BidsService $bids_service){

        return response() -> json(
            $bids_service -> rejectBid($request, $log_service)
        );

    }

    public function pullBid(Request $request, LogCreationService $log_service, BidsService $bids_service){

        return response() -> json(
            $bids_service -> pullBid($request, $log_service)
        );

    }
}
