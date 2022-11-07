<?php

namespace App\Services\Download;

use Illuminate\Http\Request;

use App\Models\Taskoffermessage;
use App\Models\Bidmessage;
use App\Models\Requestmessage;
use App\Models\Taskmessage;
use App\Models\Taskfile;

class DownloadService {
    public function download(Request $request)
    {

        switch ($request -> type) {
            case 'offer':
                $offer_message = Taskoffermessage::find($request -> message_id);
                
                $filename = $offer_message -> message;
                $tempImage = tempnam(sys_get_temp_dir(), $filename);
                copy($offer_message -> type, $tempImage);
                        
                return response()->download($tempImage, $filename);
                break;
                
            case 'bid':
                $bid_message = Bidmessage::find($request -> message_id);
                
                $filename = $bid_message -> message;
                $tempImage = tempnam(sys_get_temp_dir(), $filename);
                copy($bid_message -> type, $tempImage);
                        
                return response()->download($tempImage, $filename);
                break;
                
            case 'request':
                $request_message = Requestmessage::find($request -> message_id);
                
                $filename = $request_message -> message;
                $tempImage = tempnam(sys_get_temp_dir(), $filename);
                copy($request_message -> type, $tempImage);
                        
                return response()->download($tempImage, $filename);
                break;
                
            case 'task_chat':
                $task_message = Taskmessage::find($request -> message_id);
                
                $filename = $task_message -> message;
                $tempImage = tempnam(sys_get_temp_dir(), $filename);
                copy($task_message -> type, $tempImage);
                        
                return response()->download($tempImage, $filename);
                break;
            
            default:
                $task_file = Taskfile::find($request -> file_id);
                
                $filename = $task_file -> name;
                $tempImage = tempnam(sys_get_temp_dir(), $filename);
                copy($task_file -> url, $tempImage);
                    
                return response()->download($tempImage, $filename);
                break;
        }
    }
}