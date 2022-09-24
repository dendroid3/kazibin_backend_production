<?php

namespace App\Http\Controllers\APi\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Transaction\InvoiceService;
use App\Services\SystemLog\LogCreationService;


class InvoiceController extends Controller
{
    public function createInvoice(Request $request, InvoiceService $invoice_service, LogCreationService $log_creation){
        return response() -> json(
            $invoice_service -> createInvoice($request, $log_creation)
        );
    }

    public function getNetworkInDeficit(Request $request, InvoiceService $invoice_service){
        return response() -> json(
            $invoice_service -> getNetworkInDeficit($request)
        );
    }

    public function getInvoice(Request $request, InvoiceService $invoice_service){
        return response() -> json([
            $invoice_service -> getInvoice($request)
        ]);
    }

    public function getInvoices(Request $request, InvoiceService $invoice_service){
        return response() -> json([
            $invoice_service -> getInvoices($request)
        ]);
    }

    public function getInvoicesPaginated(Request $request, InvoiceService $invoice_service){
        return response() -> json(
            $invoice_service -> getInvoicesPaginated($request)
        );
    }

    public function markPaid(Request $request, InvoiceService $invoice_service, LogCreationService $log_creation){
        return response() -> json(
            $invoice_service -> markPaid($request, $log_creation)
        );
    }

    public function confirmPaid(Request $request, InvoiceService $invoice_service, LogCreationService $log_creation){
        return response() -> json(
            $invoice_service -> confirmPaid($request, $log_creation)
        );
    }
}
