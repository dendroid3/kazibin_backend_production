<?php

namespace App\Http\Controllers\Api\Liaison;

use App\Http\Controllers\Controller;

use App\Services\Liaison\WritersService;

class WritersController extends Controller
{
    public function getAll(WritersService $writers_service){

        return response() -> json([
            'writers' => $writers_service -> getAll()
        ]);
        
    }

    public function getAllPaginated(WritersService $writers_service){

        return response() -> json(
            $writers_service -> getAllPaginated()
        );
        
    }

    public function getMyWriters(WritersService $writers_service){
       
        return response() -> json([
            'writers' => $writers_service -> getMyWriters(),
            'status' => 200
        ]);
    }
}
