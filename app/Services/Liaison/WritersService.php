<?php

namespace App\Services\Liaison;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Task;
use App\Models\Taskoffer;
use App\Models\Bid;
use App\Models\Invoice;

class WritersService {
  public function getAll(){
    $writers = User::query()
    -> where('id', '!=', Auth::user() -> id)
    -> orderBy('writer_score', 'DESC')
    -> take(10)
    -> get();
    
    foreach ($writers as $writer) {
      $writer -> writer;
      
      $writer -> brokers = count($writer -> writer -> brokers);
      $writer -> total_tasks =  count($writer -> writer -> tasks);
      $writer -> underway_tasks = count($writer -> writer -> tasks -> where('status', 2));
      $writer -> cancelled_tasks =  count($writer -> writer -> tasks -> where('status', 4));
    }

    return $writers;
  }
  
  public function getAllPaginated(){
    $writers = User::query()
    -> where('id', '!=', Auth::user() -> id)
    -> orderBy('writer_score', 'DESC')
    -> paginate(10);
    
    foreach ($writers as $writer) {
      $writer -> writer;
      
      $writer -> brokers = count($writer -> writer -> brokers);
      $writer -> total_tasks =  count($writer -> writer -> tasks);
      $writer -> underway_tasks = count($writer -> writer -> tasks -> where('status', 2));
      $writer -> cancelled_tasks =  count($writer -> writer -> tasks -> where('status', 4));
    }

    return $writers;
  }

  public function getMyWriters(){
    $writers = Auth::user() -> broker -> writers -> take(10);

    $writers_refined = array();

    foreach ($writers as $writer) {
        $writer_refined = $writer -> user() -> select('id', 'username', 'email', 'phone_number', 'availabile', 'code', 'credential_verification') -> first();
        //get interaction details
        $tasks = $writer -> tasks -> where('broker_id', Auth::user() -> broker -> id) -> all();
        $writer_refined -> tasks_done = count($tasks);
        $writer_refined -> total_amount = 0;
        
        $writer_refined -> underway = 0;
        $writer_refined -> complete = 0;
        $writer_refined -> cancelled = 0;
        $writer_refined -> paid = 0;
        
        foreach ($tasks as $task) {
          if($task -> status == 2){
            $writer_refined -> total_amount =+ $task -> full_pay;
            $writer_refined -> underway++;
          } else if($task -> status == 3){
            $writer_refined -> total_amount =+ $task -> full_pay;
            $writer_refined -> complete++;
          } else if($task -> status == 4){
            $writer_refined -> cancelled++;
          }else if(($task -> status == 6) || ($task -> status == 8)){
            $writer_refined -> total_amount =+ $task -> full_pay;
            $writer_refined -> paid++;
          }
        }
        $writer_refined -> writer_id= $writer -> id; 
        array_push($writers_refined, $writer_refined);
    }

    return $writers_refined;
  }

  public function getMyWriter(Request $request){
    $writer_tasks = Task::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> select('code', 'created_at', 'difficulty', 'expiry_time', 'full_pay', 'id', 'page_cost', 'pages', 'status', 'topic', 'type', 'unit')
    -> orderBy('updated_at', 'DESC') -> paginate(10);

    $total_tasks = Task::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> count();
    
    $available_tasks = Task::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 1) -> count();

    $underway_tasks = Task::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 2) -> count();
    
    $complete_tasks = Task::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 3) -> count();

    $cancelled_tasks = Task::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 4) -> count();

    
    $paid_tasks = Task::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> whereIn('status', [6, 8]) -> count(); 

    return [
      'tasks' => $writer_tasks,
      'broker_writer_metrics' => [
        'total' => $total_tasks,
        'available' => $available_tasks,
        'underway' => $underway_tasks,
        'complete' => $complete_tasks,
        'cancelled' => $cancelled_tasks,
        'paid' => $paid_tasks,
      ]
    ];
  }

  public function getMyWriterOFfers(Request $request){
    $offers = Taskoffer::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> orderBy('updated_at', 'DESC') -> paginate(10);

    $total_offers = Taskoffer::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> count();

    $pending_offers = Taskoffer::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 1)
    -> count();

    $accepted_offers = Taskoffer::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 4)
    -> count();
    
    $rejected_offers = Taskoffer::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 3)
    -> count();
    
    $cancelled_offers = Taskoffer::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 2)
    -> count();

    return [
      'offers' => $offers,
      'broker_writer_offers' => [
        'total' => $total_offers,
        'pending' => $pending_offers,
        'accepted' => $accepted_offers,
        'rejected' => $rejected_offers,
        'cancelled' => $cancelled_offers,
      ]
    ];
  }

  public function getMyWriterBids(Request $request){
    $bids = Bid::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> orderBy('updated_at', 'DESC') -> paginate(10);

    foreach ($bids as $bid) {
      $bid -> task -> broker -> user;
      if($bid  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
        $bid -> unread_message = true;
      }
    }
    $total_bids = Bid::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> count();

    $pending_bids = Bid::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 1)
    -> count();

    $pulled_bids = Bid::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 2)
    -> count();
    
    $rejected_bids = Bid::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 3)
    -> count();
    
    $won_bids = Bid::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 4)
    -> count();
    
    $lost_bids = Bid::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 5)
    -> count();

    return [
      'bids' => $bids,
      'broker_writer_bids' => [
        'total' => $total_bids,
        'pending' => $pending_bids,
        'pulled' => $pulled_bids,
        'rejected' => $rejected_bids,
        'won' => $won_bids,
        'lost' => $lost_bids,
      ]
    ];
  }

  public function getMyWriterInvoices(Request $request){
    $invoices = Invoice::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> orderBy('updated_at', 'DESC') -> paginate(10);

    $total_invoices = Invoice::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> count();

    $pending_invoices = Invoice::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> where('status', 1)
    -> count();

    $paid_invoices = Invoice::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> whereIn('status', [2, 3])
    -> count();

    return [
      'invoices' => $invoices,
      'broker_writer_invoices' => [
        'total' => $total_invoices,
        'pending' => $pending_invoices,
        'paid' => $paid_invoices,
      ]
    ];
  }
}