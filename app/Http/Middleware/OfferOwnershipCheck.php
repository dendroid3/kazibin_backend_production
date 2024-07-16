<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Taskoffer;
use Illuminate\Support\Facades\Log;

class OfferOwnershipCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $offer_owner  = Taskoffer::find($request->input('offer_id'));
        if(Auth::user() -> Writer ->  id === $offer_owner -> writer_id || Auth::user() -> Broker -> id === $offer_owner -> broker_id){
            return $next($request);
        } else {
            return response() -> json([
                'error' => 'not found'
            ], 202);
        }
    }
}
