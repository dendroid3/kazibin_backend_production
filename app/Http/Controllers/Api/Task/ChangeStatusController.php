<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SystemLog\LogCreationService;
use App\Services\Task\StatusService;

class ChangeStatusController extends Controller
{
    public function markComplete(Request $request, StatusService $status_service, LogCreationService $log_creation){
        return response() -> json([
            $status_service -> markComplete($request, $log_creation)
        ]);
    }

    public function markCancel(Request $request, StatusService $status_service, LogCreationService $log_creation){
        return response() -> json([
            $status_service -> markCancel($request, $log_creation)
        ]);
    }

    public function markInvoiced(Request $request, StatusService $status_service, LogCreationService $log_creation){
        return response() -> json([
            $status_service -> markInvoiced($request, $log_creation)
        ]);
    }

    public function markPaid(Request $request, StatusService $status_service, LogCreationService $log_creation){
        return response() -> json([
            $status_service -> markPaid($request, $log_creation)
        ]);
    }
}
