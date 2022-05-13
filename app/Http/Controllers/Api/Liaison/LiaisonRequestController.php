<?php

namespace App\Http\Controllers\Api\Liaison;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Liaisonrequest;
use App\Models\Log;

class LiaisonRequestController extends Controller
{
    public function sendRequestToWriter(Request $request){
        $liaison_request = new Liaisonrequest;
        $liaison_request -> id =  Str::orderedUuid() -> toString();
        $liaison_request -> initiator_id = Auth::user() -> id;
        $liaison_request -> broker_id = Auth::user() -> id;
        $liaison_request -> writer_id = $request -> writer_id;
        $liaison_request -> save();
        $this -> createLogOnRequestToWriter($liaison_request, Auth::user());
        return response() -> json([
            'liaison_request' => $liaison_request
        ]);
    }

    public function rejectRequest(){}

    public function pullRequest(){}

    public function acceptRequest(){}

    public function createLogOnRequestToWriter($liaison_request, $initiator){
        $broker_log_stub_1 = 'Request sent to writer. Username: ';
        $other_party = DB::table('users') -> where('id', $liaison_request -> writer_id) -> first();
        $broker_log_stub_2 = $other_party -> username;
        $broker_log_stub_3 = '. Code: ';
        $broker_log_stub_4 = $other_party -> code;

        $broker_message = $broker_log_stub_1 . $broker_log_stub_2 . $broker_log_stub_3 . $broker_log_stub_4;

        $log = new Log;
        $log -> user_id = $initiator['id'];
        $log -> foreign_id = $liaison_request['id'];
        $log -> code = 'request';
        $log -> message = $broker_message;
        $log -> save();

        
        $writer_log_stub_1 = 'Request recieved from broker. Username: ';
        $writer_log_stub_2 = $initiator['username'];
        $writer_log_stub_3 = '. Code: ';
        $writer_log_stub_4 = $initiator['code'];
        $writer_log_stub_5 = '. Accept to be offered tasks by this broker.';

        $writer_message = $writer_log_stub_1 . $writer_log_stub_2 . $writer_log_stub_3 . $writer_log_stub_4 . $writer_log_stub_5;

        $log = new Log;
        $log -> user_id = $liaison_request['writer_id'];
        $log -> foreign_id = $liaison_request['id'];
        $log -> code = 'request';
        $log -> message = $writer_message;
        $log -> save();
    }

    public function getLiaisonRequestsFromBrokers(Request $request){
        $requests = DB::table('liaisonrequests') -> where([
            ['writer_id', '=', Auth::user() -> id],
            ['initiator_id', '!=', Auth::user() -> id]
        ]) -> get();

        return response() -> json([
            'requests' => $requests
        ]);
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
