<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanBidOnJob
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // has the money
        // check incomplete and number of bids 
        // for incomplete; check deadline, < a day? ptrauui
        // level
        return $next($request);
    }
}
