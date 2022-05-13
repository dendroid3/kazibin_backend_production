<?php

namespace App\Http\Controllers\Api\Liaison;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class WritersController extends Controller
{
    public function getAll(Request $request){
        $writers = DB::table('users') -> get();
        return response() -> json([
            'writers' => $writers
        ]);
    }

    public function getMyWriters(Request $request){
        /*
            Should get my writers.
            
        */
    }
}
