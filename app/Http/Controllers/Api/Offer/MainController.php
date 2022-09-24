<?php

namespace App\Http\Controllers\Api\Offer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\SystemLog\LogCreationService;
use App\Services\Offer\OfferService;

class MainController extends Controller
{
    // public function accept(Request $request){
    //     $offer = Taskoffer::find($request -> offer_id);
    // }

    // public function reject(Request $request){
    //     $offer = Taskoffer::find($request -> offer_id);

    // }

    // public function pull(Request $request){
    //     $offer = Taskoffer::find($request -> offer_id);
    // }

    public function getMine(OfferService $offer_service){
        return response() -> json([
            'offers' => $offer_service -> getMine(),
            'status' => 200
        ]);
    }

    public function getMinePaginated(Request $request, OfferService $offer_service){
        return response() -> json(
            $offer_service -> getMinePaginated($request),
        );
    }
    
    public function getOfferMessages(Request $request, OfferService $offer_service){
        return response() -> json([
            'messages' => $offer_service -> getOfferMessages($request)
        ]);
    }

    public function sendOfferMessages(Request $request, OfferService $offer_service){
        return response() -> json(
            $offer_service -> sendOfferMessages($request),
        );
    }

    public function accept(Request $request, LogCreationService $log_service, OfferService $offer_service){
        return response() -> json([
            'message' => $offer_service -> acceptOffer($request, $log_service),
            'status' => 200
        ]);
    }

    public function reject(Request $request, LogCreationService $log_service, OfferService $offer_service){
        return response() -> json([
            'message' => $offer_service -> rejectOffer($request, $log_service),
            'status' => 200
        ]);
    }

    public function cancel(Request $request, LogCreationService $log_service, OfferService $offer_service){
        return response() -> json([
            'message' => $offer_service -> cancelOffer($request, $log_service),
            'status' => 200
        ]);
    }
    
}
