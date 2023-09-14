<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Task;

class OwnershipCheck
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
        $task_owner  = Task::find($request->input('task_id'));
        
        if(Auth::user() -> broker -> id === $task_owner -> broker_id){
            return $next($request);
        } else {
            return response() -> json([
                'error' => 'not found'
            ], 202);

        }
    }
}
