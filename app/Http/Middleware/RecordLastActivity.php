<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RecordLastActivity
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
        $difference_in_time_in_minutes = (strtotime(Carbon::now()) - strtotime(Auth::user() -> last_activity)) / 60;
        if($difference_in_time_in_minutes > 5) {
            $user = Auth::user();
            $user -> last_activity = Carbon::now();
            $user -> push();
        }
        return $next($request);
    }
}
