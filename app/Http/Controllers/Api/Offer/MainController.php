<?php

namespace App\Http\Controllers\Api\Offer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function accept(Request $request){
        $offer = Taskoffer::find($request -> offer_id);
    }

    public function reject(Request $request){
        $offer = Taskoffer::find($request -> offer_id);

    }

    public function pull(Request $request){
        $offer = Taskoffer::find($request -> offer_id);
    }
}
