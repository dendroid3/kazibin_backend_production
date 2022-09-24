<?php

namespace App\Http\Controllers\Api\Liaison;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Services\Liaison\LiaisonsService;
use App\Services\SystemLog\LogCreationService;

use App\Models\Log;

class LiaisonRequestController extends Controller
{
    

    public function sendRequestToWriter(Request $request, LiaisonsService $liaison_service, LogCreationService $log_creation){

        return response() -> json([
            'liaison_request' => $liaison_service -> sendRequestToWriter($request, $log_creation)
        ]);

    }

    public function sendRequestToBroker(Request $request, LiaisonsService $liaison_service, LogCreationService $log_creation){

        return response() -> json([
            'liaison_request' => $liaison_service -> sendRequestToBroker($request, $log_creation)
        ]);


    }

    public function rejectRequest(Request $request, LiaisonsService $liaison_service, LogCreationService $log_creation){
        switch ($request -> base) {
            case 'reject_request_from_broker':
                $response = $liaison_service -> rejectRequestFromBroker($request, $log_creation);
                break;
            
            default:
                $response = $liaison_service -> rejectRequestFromWriter($request, $log_creation);
                break;
        }

        return response() -> json([
            'message' => $response,
            'status' => 200
        ]);
    }

    public function cancelRequest(Request $request, LiaisonsService $liaison_service, LogCreationService $log_creation){}

    public function acceptRequest(Request $request, LiaisonsService $liaison_service, LogCreationService $log_creation){
        switch ($request -> base) {
            case 'attach_broker_to_me':
                $response = $liaison_service -> attachBrokerToMe($request, $log_creation);
                break;
            
            default:
                $response = $liaison_service -> attachWriterToMe($request, $log_creation);
                break;
        }
        return response() -> json([
            'message' => $response,
            'status' => 200
        ]);
    }


    public function getLiaisonRequests(LiaisonsService $liaison_service){
    
        return response() -> json([
            'requests' => $liaison_service -> getLiaisonRequests()
        ]);
        
    }

    public function getLiaisonRequestsPaginated(Request $request, LiaisonsService $liaison_service){
        
        return response() -> json(
            $liaison_service -> getLiaisonRequestsPaginated($request)
        );

    }

    public function getLiaisonRequestsToBrokers(Request $request){
        $requests = DB::table('liaisonrequests') -> where([
            ['writer_id', '=', Auth::user() -> id],
            ['initiator_id', '=', Auth::user() -> id]
        ]) -> get();

        return response() -> json([
            'requests' => $requests
        ]);
    }

    public function getLiaisonRequestsFromWriters(Request $request){
        $requests = DB::table('liaisonrequests') -> where([
            ['broker_id', '=', Auth::user() -> id],
            ['initiator_id', '!=', Auth::user() -> id]
        ]) -> get();

        return response() -> json([
            'requests' => $requests
        ]);
    }

    public function getLiaisonRequestsToWriters(Request $request){
        $requests = DB::table('liaisonrequests') -> where([
            ['writer_id', '=', Auth::user() -> id],
            ['initiator_id', '=', Auth::user() -> id]
        ]) -> get();

        return response() -> json([
            'requests' => $requests
        ]);
    }
}
