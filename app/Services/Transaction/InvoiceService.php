<?php

namespace App\Services\Transaction;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\SystemLog\LogCreationService;

use App\Models\Invoice;
use App\Models\Bonus;
use App\Models\Fine;
use App\Models\Task;
use App\Models\Taskmessage;
use App\Models\Broker;
use App\Models\Writer;

class InvoiceService{
    public function createInvoice(Request $request, LogCreationService $log_creation){
        $invoice = new Invoice;
        $invoice -> status = 1;
        $invoice -> broker_id = $request -> broker_id;
        $invoice -> writer_id = $request -> writer_id;
        $invoice -> tasks_signature = $request -> tasks_signature;
        $invoice -> amount = $request -> amount;
        $invoice -> code = strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(1)) . rand(99,999);
        $invoice -> save();

        $bonuses = [];
        if (is_array($request -> bonuses) || is_object($request -> bonuses)){
            foreach ($request -> bonuses as $bonus) {
                $bonus_paid = new Bonus;
                $bonus_paid -> invoice_id = $invoice -> id;
                $bonus_paid -> amount = $bonus['amount'];
                $bonus_paid -> description = $bonus['description'];
                $bonus_paid -> save();
                array_push($bonuses, $bonus_paid);
            }
        }
        // create fine
        $fines = [];
        if (is_array($request -> fines) || is_object($request -> fines)){

            foreach ($request -> fines as $fine) {
                $fine_deducted = new Fine;
                $fine_deducted -> invoice_id = $invoice -> id;
                $fine_deducted -> amount = $fine['amount'];
                $fine_deducted -> description = $fine['description'];
                $fine_deducted -> save();
                array_push($fines, $fine_deducted);
            }
        }

        //get task list + status to 
        $task_ids = explode("_", $request -> tasks_signature);
        $task = task::find($task_ids[0]);

        foreach ($task_ids as $task_id) {
            if( array_search($task_id, $task_ids) < ( count($task_ids) - 1 )){
                // $invoices_task = $task -> id;
                $task = task::find($task_id);
                $task -> status  = 5;
                $task -> invoice_id  = $invoice -> id;
                $task -> updated_at  = Carbon::now();
                $task -> push();

                // $task -> Invoice() -> attach($invoice -> id);
                
                $message = new Taskmessage;
                $message -> id = Str::orderedUuid() -> toString();
                $message -> user_id = 1;
                $message -> task_id = $task_id;
                $message -> message = '--- Task invoiced. Invoice code: ' . $invoice -> code . ' ---';
                $message -> save();
            }
        }

        $writer = $task -> writer -> user;

        $broker_message = "Invoice code: " . $invoice -> code . " created. It is worth KES " . $invoice -> amount .
        " for " . (count($task_ids) - 1) . ' task(s), the recipient is:' . $writer -> code .
        ": " . $writer -> username . '.';

        $log_creation -> createSystemMessage(
            Auth::user() -> id,
            $broker_message,
            $task -> writer -> user -> id, 
            'Invoice Created'
        );

        $writer_message = Auth::user() -> username . " has created an invoice code: ". $invoice -> code . ". It is worth KES " . $invoice -> amount .
        " for " . (count($task_ids) - 1) . ' task(s).' ;

        $log_creation -> createSystemMessage(
            $task -> writer -> user -> id, 
            $broker_message,
            Auth::user() -> id,
            'Invoice Created'
        );

        if(Auth::user() -> writer -> id === $task -> writer_id){
            return ['message' => $writer_message];
        } else {
            return ['message' => $broker_message];
        }

    }

    public function markPaid(Request $request, LogCreationService $log_creation){
        $invoice = Invoice::find($request -> invoice_id);

        if(Auth::user() -> writer -> id == $invoice -> writer_id){
            return $this -> markPaidAsWriter($invoice, $log_creation);
        } else {
            return $this -> markPaidAsBroker($invoice, $log_creation);
        }
    }

    public function markPaidAsWriter($invoice, LogCreationService $log_creation){
        
        $invoice -> status = 3;
        $invoice -> push();

        $task_ids = explode("_", $invoice -> tasks_signature);
        foreach ($task_ids as $task_id) {
            if( array_search($task_id, $task_ids) < ( count($task_ids) - 1 )){
                $task = task::find($task_id);
                $task -> status  = 6;
                $task -> updated_at  = Carbon::now();
                $task -> push();
                
                $message = new Taskmessage;
                $message -> id = Str::orderedUuid() -> toString();
                $message -> user_id = 1;
                $message -> task_id = $task_id;
                $message -> message = '--- Task paid through invoice ' . $invoice -> code . '. ---';
                $message -> save();
            }
        }

        $writer = $task -> writer -> user;

        $broker_message = "Invoice code: " . $invoice -> code . " paid.";

        $log_creation -> createSystemMessage(
            Auth::user() -> id,
            $broker_message,
            $task -> writer -> user -> id, 
            'Invoice Paid'
        );

        $writer_message = "Invoice code: " . $invoice -> code . " paid.";

        $log_creation -> createSystemMessage(
            $task -> writer -> user -> id, 
            $broker_message,
            Auth::user() -> id,
            'Invoice Paid'
        );

        return ['message' => $writer_message];
    }

    public function markPaidAsBroker($invoice, LogCreationService $log_creation){
        
        $invoice -> status = 2;
        $invoice -> push();

        $task_ids = explode("_", $invoice -> tasks_signature);
        foreach ($task_ids as $task_id) {
            if( array_search($task_id, $task_ids) < ( count($task_ids) - 1 )){
                $task = task::find($task_id);
                $task -> status  = 8;
                $task -> updated_at  = Carbon::now();
                $task -> push();
                
                $message = new Taskmessage;
                $message -> id = Str::orderedUuid() -> toString();
                $message -> user_id = 1;
                $message -> task_id = $task_id;
                $message -> message = '--- Task paid through invoice ' . $invoice -> code . '. ' . $task -> writer -> user -> username . ' should confirm this. ---';
                $message -> save();
            }
        }

        $writer = $task -> writer -> user;

        $broker_message = "Invoice code: " . $invoice -> code . " marked as paid." . 
        $writer -> username . ' should confirm this.';

        $log_creation -> createSystemMessage(
            Auth::user() -> id,
            $broker_message,
            $task -> writer -> user -> id, 
            'Invoice Paid (Unconfirmed)'
        );

        $writer_message = Auth::user() -> username . " claims to have paid an invoice code: ". $invoice -> code . 
        ". Kindly confirm this." ;

        $log_creation -> createSystemMessage(
            $task -> writer -> user -> id, 
            $broker_message,
            Auth::user() -> id,
            'Invoice Paid (Unconfirmed)'
        );

        return ['message' => $broker_message];
        // return $invoice;
    }

    public function confirmPaid(Request $request, LogCreationService $log_creation){
        $invoice = Invoice::find($request -> invoice_id);
        
        $invoice -> status = 3;
        $invoice -> push();

        $task_ids = explode("_", $invoice -> tasks_signature);
        foreach ($task_ids as $task_id) {
            if( array_search($task_id, $task_ids) < ( count($task_ids) - 1 )){
                $task = task::find($task_id);
                $task -> status  = 6;
                $task -> updated_at  = Carbon::now();
                $task -> push();
                
                $message = new Taskmessage;
                $message -> id = Str::orderedUuid() -> toString();
                $message -> user_id = 1;
                $message -> task_id = $task_id;
                $message -> message = '--- Payment Confirmed ---';
                $message -> save();
            }
        }

        $writer = $task -> writer -> user;

        $broker_message = "Payment of invoice code: " . $invoice -> code . " confirmed.";

        $log_creation -> createSystemMessage(
            Auth::user() -> id,
            $broker_message,
            $task -> writer -> user -> id, 
            'Invoice Payment Confirmed'
        );

        $writer_message = "Payment of invoice code: " . $invoice -> code . " confirmed.";

        $log_creation -> createSystemMessage(
            $task -> writer -> user -> id, 
            $broker_message,
            Auth::user() -> id,
            'Invoice Payment Confirmed'
        );

        return ['message' => $writer_message];
    }

    public function getNetworkInDeficit(Request $request)
    {
        $writers_i_owe_ids = Task::where('broker_id', Auth::user() -> broker ->id) 
                        -> where('status', 3) 
                        -> select('writer_id') 
                        -> distinct() 
                        -> get();
    
        $writers_i_owe = array();

        foreach ($writers_i_owe_ids as $writer_id) {
            $writer_i_owe = Writer::find($writer_id)-> first() -> user() -> select('code', 'username') -> first();
            array_push($writers_i_owe, $writer_i_owe);
        }

        $brokers_that_owe_me_ids = Task::where('writer_id', Auth::user() -> writer ->id) 
                        -> where('status', 3) 
                        -> select('broker_id') 
                        -> distinct() 
                        -> get();

        $brokers_that_owe_me = array();

        foreach ($brokers_that_owe_me_ids as $broker_id) {
            $broker_that_owes_me = Broker::find($broker_id) -> first() -> user() -> select('code', 'username') -> first();
            array_push($brokers_that_owe_me, $broker_that_owes_me);
        }

        return [
            'writers_i_owe' => $writers_i_owe,
            'brokers_that_owe_me' => $brokers_that_owe_me,
        ];
    }

    public function getInvoice(Request $request){
        $invoice = Invoice::find($request -> invoice_id);
        $invoice -> tasks = Task::where('invoice_id', $invoice -> id)
                                -> select('full_pay', 'created_at', 'topic', 'code')
                                -> get();
        $invoice -> bonuses;
        $invoice -> fines;
        if($request -> recipient == 'broker'){
            $invoice -> broker -> user -> username;
        } else if($request -> recipient == 'writer'){
            $invoice -> writer -> user -> username;
        }


        return ['invoice' => $invoice];
    }

    public function getInvoices(Request $request){
        $credited_invoices = Auth::user() -> broker -> Invoices -> take(10);
        foreach ($credited_invoices as $credited_invoice) {
            $credited_invoice -> writer -> user;
        }

        $debited_invoices = Auth::user() -> writer -> Invoices -> take(10);
        foreach ($debited_invoices as $debited_invoice) {
            $debited_invoice -> broker -> user;
        }

        return [
            'credited_invoices' => $credited_invoices,
            'debited_invoices' => $debited_invoices
        ];
    }
    
    public function getInvoicesPaginated(Request $request){
    
        if(!$request -> status){
            if($request -> credited){
                $credited_invoices = Invoice::where('broker_id', Auth::user() -> broker -> id) -> paginate(10);
                foreach ($credited_invoices as $credited_invoice) {
                    $credited_invoice -> writer -> user;
                }
                return $credited_invoices;
            } else {
                $debited_invoices = Invoice::where('writer_id', Auth::user() -> writer -> id) -> paginate(10);
                foreach ($debited_invoices as $debited_invoice) {
                    $debited_invoice -> broker -> user;
                }
                return $debited_invoices;
            }
        } else {
            if($request -> credited){
                $credited_invoices = Invoice::where('broker_id', Auth::user() -> broker -> id) -> where('status', $request -> status) -> paginate(10);
                foreach ($credited_invoices as $credited_invoice) {
                    $credited_invoice -> writer -> user;
                }
                return $credited_invoices;
            } else {
                $debited_invoices = Invoice::where('writer_id', Auth::user() -> writer -> id) -> where('status', $request -> status) -> paginate(10);

                foreach ($debited_invoices as $debited_invoice) {
                    $debited_invoice -> broker -> user;
                }
                return $debited_invoices;
            }
        }
    }

}