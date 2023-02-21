<?php

namespace App\Services\Liaison;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Broker;
use App\Models\Task;
use App\Models\Taskoffer;
use App\Models\Invoice;
use App\Models\Bid;

class BrokersService {
  public function getAll(){
    $brokers = User::query()
          -> where('id', '!=', Auth::user() -> id)
          -> orderBy('broker_score', 'DESC')
          -> take(10)
          -> get();
    // ? DB::table('users') -> get();
    foreach ($brokers as $broker) {
      $broker -> broker;
      $broker -> writers = count($broker -> broker -> writers);
      $broker -> total_tasks =  count($broker -> broker -> tasks);
      $broker -> available_tasks = count($broker -> broker -> tasks -> where('status', 1));
      $broker -> cancelled_tasks =  count($broker -> broker -> tasks -> where('status', 4));
    }
    return $brokers;
  }
  
  public function getAllPaginated(){
    $brokers = User::query()
          -> where('id', '!=', Auth::user() -> id)
          -> orderBy('broker_score', 'DESC')
          -> paginate(10);
          Log::info('called');
    // ? DB::table('users') -> get();
    foreach ($brokers as $broker) {
      $broker -> broker;
      $broker -> writers = count($broker -> broker -> writers);
      $broker -> total_tasks =  count($broker -> broker -> tasks);
      $broker -> available_tasks = count($broker -> broker -> tasks -> where('status', 1));
      $broker -> cancelled_tasks =  count($broker -> broker -> tasks -> where('status', 4));
    }
    return $brokers;
  }

  public function getMyBrokers(){
    $brokers = Auth::user() -> writer -> brokers() -> paginate(10);
    
    $brokers_refined = array();
    
    foreach ($brokers as $broker) {
        $broker_refined = Broker::find($broker -> pivot -> broker_id) -> user;
        $broker_refined -> broker_id = $broker -> id;
        
        $tasks = $broker -> tasks -> where('writer_id', Auth::user() -> writer -> id) -> all();
        $broker_refined -> tasks_done = count($tasks);
        $broker_refined -> total_amount = 0;

        $broker_refined -> underway = 0;
        $broker_refined -> complete = 0;
        $broker_refined -> cancelled = 0;
        $broker_refined -> paid = 0;
        
        foreach ($tasks as $task) {
          if($task -> status == 2){
            $broker_refined -> total_amount =+ $task -> full_pay;
            $broker_refined -> underway++;
          } else if($task -> status == 3){
            $broker_refined -> total_amount =+ $task -> full_pay;
            $broker_refined -> complete++;
          } else if($task -> status == 4){
            $broker_refined -> cancelled++;
          }else if(($task -> status == 6) || ($task -> status == 8)){
            $broker_refined -> total_amount =+ $task -> full_pay;
            $broker_refined -> paid++;
          }
        }
        array_push($brokers_refined, $broker_refined);

    }
    
    return $brokers_refined;
  }

  public function getOneBroker(Request $request){
    $broker = User::query() -> where('code', $request -> code) -> first();
    $broker -> broker;
    $broker -> writers = count($broker -> broker -> writers);
    $broker -> total_tasks =  count($broker -> broker -> tasks);
    $broker -> available_tasks = count($broker -> broker -> tasks -> where('status', 1));
    $broker -> cancelled_tasks =  count($broker -> broker -> tasks -> where('status', 4));

    return $broker;
  }

  
  public function getMyBroker(Request $request){

    $broker_tasks = Task::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> select('code', 'created_at', 'difficulty', 'expiry_time', 'full_pay', 'id', 'page_cost', 'pages', 'status', 'topic', 'type', 'unit')
    -> orderBy('updated_at', 'DESC') -> paginate(10);

    $total_tasks = Task::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> count();

    
    $available_tasks = Task::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 1) -> count();

    $underway_tasks = Task::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 2) -> count();
    
    $complete_tasks = Task::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 3) -> count();

    $cancelled_tasks = Task::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 4) -> count();

    
    $paid_tasks = Task::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> whereIn('status', [6, 8]) -> count(); 

    return [
      'tasks' => $broker_tasks,
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

  public function getMyBrokerInvoices(Request $request){
    $invoices = Invoice::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> orderBy('updated_at', 'DESC') -> paginate(10);

    $total_invoices = Invoice::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> count();

    $pending_invoices = Invoice::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 1)
    -> count();

    $paid_invoices = Invoice::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
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
  
  public function getMyBrokerOFfers(Request $request){
    $offers = Taskoffer::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> orderBy('updated_at', 'DESC') -> paginate(10);

    $total_offers = Taskoffer::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> count();

    $pending_offers = Taskoffer::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 1)
    -> count();

    $accepted_offers = Taskoffer::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 4)
    -> count();
    
    $rejected_offers = Taskoffer::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 3)
    -> count();
    
    $cancelled_offers = Taskoffer::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
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
  
  public function getMyBrokerBids(Request $request){
    $bids = Bid::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> orderBy('updated_at', 'DESC') -> paginate(10);

    foreach ($bids as $bid) {
      $bid -> task -> broker -> user;
      if($bid  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
        $bid -> unread_message = true;
      }
    }
    $total_bids = Bid::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> count();

    $pending_bids = Bid::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 1)
    -> count();

    $pulled_bids = Bid::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 2)
    -> count();
    
    $rejected_bids = Bid::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 3)
    -> count();
    
    $won_bids = Bid::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
    -> where('status', 4)
    -> count();
    
    $lost_bids = Bid::query() -> where('writer_id', Auth::user() -> writer -> id)
    -> where('broker_id', $request -> broker_id)
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
}