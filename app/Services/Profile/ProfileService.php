<?php

namespace App\Services\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\Liaisonrequest;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class ProfileService {
    public function getDashboardDetails(Request $request){
        $posted_all = Task::where('broker_id', Auth::user() -> broker -> id) -> count();
        
        $posted_unassigned = Task::where([
            ['broker_id', Auth::user() -> broker -> id],
            ['status', 1]
        ]) -> count();
        
        $posted_underway = Task::where([
            ['broker_id', Auth::user() -> broker -> id],
            ['status', 2]
        ]) -> count();
        
        $posted_completed = Task::where([
            ['broker_id', Auth::user() -> broker -> id],
            ['status', 3]
        ]) -> count();
        
        $posted_revision = Task::where([
            ['broker_id', Auth::user() -> broker -> id],
            ['status', 8]
        ]) -> count();

        $posted_canceled = Task::where([
            ['broker_id', Auth::user() -> broker -> id],
            ['status', 4]
        ]) -> count();

        $posted_invoiced = Task::where([
            ['broker_id', Auth::user() -> broker -> id],
            ['status', 5]
        ]) -> count();

        $posted_diputed = Task::where([
            ['broker_id', Auth::user() -> broker -> id],
            ['status', 7]
        ]) -> count();
        
        $posted_paid = Task::where([
            ['broker_id', Auth::user() -> broker -> id],
            ['status', 6]
        ]) -> count();

        $taken_all = Task::where('writer_id', Auth::user() -> writer -> id) -> count();
        
        $taken_underway = Task::where([
            ['writer_id', Auth::user() -> writer -> id],
            ['status', 2]
        ]) -> count();
        
        $taken_completed = Task::where([
            ['writer_id', Auth::user() -> writer -> id],
            ['status', 3]
        ]) -> count();
        
        $taken_revision = Task::where([
            ['writer_id', Auth::user() -> writer -> id],
            ['status', 8]
        ]) -> count();

        $taken_canceled = Task::where([
            ['writer_id', Auth::user() -> writer -> id],
            ['status', 4]
        ]) -> count();

        $taken_invoiced = Task::where([
            ['writer_id', Auth::user() -> writer -> id],
            ['status', 5]
        ]) -> count();
        
        $taken_paid = Task::where([
            ['writer_id', Auth::user() -> writer -> id],
            ['status', 6]
        ]) -> count();

        $taken_disputed = Task::where([
            ['writer_id', Auth::user() -> writer -> id],
            ['status', 7]
        ]) -> count();

        $offers_all = Auth::user() -> writer -> offers -> count();
        $offers_accepted = Auth::user() -> writer -> offers -> where('status', 4) -> count();
        $offers_rejected = Auth::user() -> writer -> offers -> where('status', 3) -> count();
        $offers_cancelled = Auth::user() -> writer -> offers -> where('status', 2) -> count();
        $offers_pending = Auth::user() -> writer -> offers -> where('status', 1) -> count();
        
        $bids_all = Auth::user() -> writer -> bids -> count();
        $bids_accepted = Auth::user() -> writer -> bids -> where('status', 4) -> count();
        $bids_rejected = Auth::user() -> writer -> bids -> where('status', 3) -> count();
        $bids_pulled = Auth::user() -> writer -> bids -> where('status', 2) -> count();
        $bids_pending = Auth::user() -> writer -> bids -> where('status', 1) -> count();

        $credited_invoices_count = Auth::user() -> broker -> Invoices -> count();
        $pending_credited_invoices_count = Auth::user() -> broker -> Invoices -> where('status', 1) -> count();
        $unconfirmed_credited_invoices_count = Auth::user() -> broker -> Invoices -> where('status', 2) -> count();
        $paid_credited_invoices_count = Auth::user() -> broker -> Invoices -> where('status', 3) -> count();

        $debited_invoices_count = Auth::user() -> writer -> Invoices -> count();
        $pending_debited_invoices_count = Auth::user() -> writer -> Invoices  -> where('status', 1)  -> count();
        $unconfirmed_debited_invoices_count = Auth::user() -> writer -> Invoices  -> where('status', 2)  -> count();
        $paid_debited_invoices_count = Auth::user() -> writer -> Invoices  -> where('status', 3)  -> count();
        $invoices_count = $credited_invoices_count + $debited_invoices_count;

        $requests_count = Liaisonrequest::where('writer_id', Auth::user() -> writer -> id) -> count() + Liaisonrequest::where('broker_id', Auth::user() -> broker -> id) -> count();
        
        $network_count = Auth::user() -> broker -> writers -> count() + Auth::user() -> writer -> brokers -> count();

        $transactions_count = Auth::user() -> transactions -> count();
        $total_debit = Auth::user() -> transactions() -> where('type', 'Debit') -> sum('amount');
        $total_credit = Auth::user() -> transactions() -> where('type', 'Credit') -> sum('amount');
        $balance = $total_debit - $total_credit;

        return [
            'posted' => [
                'posted_all' => $posted_all,
                'posted_unassigned' => $posted_unassigned,
                'posted_underway' => $posted_underway,
                'posted_completed' => $posted_completed,
                'posted_canceled' => $posted_canceled,
                'posted_paid' => $posted_paid,
                'posted_revision' => $posted_revision,
                'posted_invoiced' => $posted_invoiced,
                'posted_diputed' => $posted_diputed
            ],
            
            'taken' => [
                'taken_all' => $taken_all,
                'taken_underway' => $taken_underway,
                'taken_completed' => $taken_completed,
                'taken_canceled' => $taken_canceled,
                'taken_paid' => $taken_paid,
                'taken_invoiced' => $taken_invoiced,
                'taken_revision' => $taken_revision,
                'taken_disputed' => $taken_disputed
            ],
            
            'offers' => [
                'offers_all' => $offers_all,
                'offers_accepted' => $offers_accepted,
                'offers_rejected' => $offers_rejected,
                'offers_cancelled' => $offers_cancelled,
                'offers_pending' => $offers_pending,
            ],
            
            'bids' => [
                'bids_all' => $bids_all,
                'bids_accepted' => $bids_accepted,
                'bids_rejected' => $bids_rejected,
                'bids_pulled' => $bids_pulled,
                'bids_pending' => $bids_pending,
                'bids_is_green' => false
            ],

            'invoices' =>[
                'invoices_count' => $invoices_count,
                'credited' => [
                    'all' => $credited_invoices_count,
                    'pending' => $pending_credited_invoices_count,
                    'unconfirmed' => $unconfirmed_credited_invoices_count,
                    'paid' => $paid_credited_invoices_count
                ],
                'debited' => [
                    'all' => $debited_invoices_count,
                    'pending' => $pending_debited_invoices_count,
                    'unconfirmed' => $unconfirmed_debited_invoices_count,
                    'paid' => $paid_debited_invoices_count
                ]
            ],

            'requests_count' => $requests_count,

            'network_count' => $network_count,

            'transactions' => [
                'balance' => $balance,
                'total_debit' => $total_debit,
                'total_credit' => $total_credit,
                'count' => $transactions_count
            ]
        ];
    }

    public function getBrokerMetrics(Request $request){
        $user = User::find($request -> user_id);
        $broker = $user -> broker;
        $broker -> average_rating = round($broker -> ratings() -> avg('rating'), 1);
        $broker -> number_of_reviews = $broker -> ratings -> count();
        $writers_count = count($broker -> writers); # -> count;
        $total_tasks =  count($broker -> tasks);
        $available_tasks = $broker -> tasks -> where('status', 1);
        $cancelled_tasks =  count($broker -> tasks -> where('status', 4));
        $paid_tasks =  count($broker -> tasks -> where('status', 6));
        $invoices_count = count($broker-> Invoices);

        return [
            'writers_count' => $writers_count,
            'total_tasks' => $total_tasks,
            'available_tasks' => $available_tasks,
            'cancelled_tasks' => $cancelled_tasks,
            'paid_tasks' => $paid_tasks,
            'invoices_count' => $invoices_count,
            'broker' => $user
        ];
    }

    public function getWriterMetrics(Request $request){
        $user = User::find($request -> user_id);
        $writer = $user -> writer;
        $writer -> average_rating = round($writer -> ratings() -> avg('rating'), 1);
        $writer -> number_of_reviews = $writer -> ratings -> count();

        $brokers_count = count($writer -> brokers); # -> count;
        $total_tasks =  count($writer -> tasks);
        $available_tasks = count($writer -> tasks -> where('status', 2));
        $cancelled_tasks =  count($writer -> tasks -> where('status', 4));
        $paid_tasks =  count($writer -> tasks -> where('status', 6));
        $invoices_count = count($writer-> Invoices);

        return [
            'brokers_count' => $brokers_count,
            'total_tasks' => $total_tasks,
            'available_tasks' => $available_tasks,
            'cancelled_tasks' => $cancelled_tasks,
            'paid_tasks' => $paid_tasks,
            'invoices_count' => $invoices_count,
            'writer' => $writer
        ];
    }

    public function changeMyBio(Request $request){
        $user = Auth::user();
        $user -> bio = $request -> bio;
        $user -> push();

        return $user;
    }

    public function changeAvailability(Request $request){
        $user = Auth::user();
        $user -> availabile = $request -> available;
        $user -> push();

        return $user;
    }

    public function setMyInterests(Request $request){
        $user = Auth::user();
        $user -> interests = $request -> interests;
        $user -> push();

        return $user;
    }
    
}