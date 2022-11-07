<?php

namespace App\Services\Download;

use Illuminate\Http\Request;

use App\Models\Taskoffermessage;
use Illuminate\Support\Facades\Log;

class DownloadService {
    public function download(Request $request)
    {
        switch ($request -> file_location) {
            case 'offer':
                // $offer_message = Taskoffermessage::find($request -> message_id);
                
                // $filename = $offer_message -> message;
                // $tempImage = tempnam(sys_get_temp_dir(), $filename);
                // copy($offer_message -> type, $tempImage);
                
                // return response()->download($tempImage, $filename);
                            
                Log::info('called in service');
                $filename = 'file-name.docx';
                $tempImage = tempnam(sys_get_temp_dir(), $filename);
                copy('http://localhost/amnesia.docx', $tempImage);
                
                return response()->download($tempImage, $filename);
                break;
            
            default:
                # code...
                break;
        }
    }
}