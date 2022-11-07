<?php

namespace App\Http\Controllers\Api\Download;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Download\DownloadService;

class DownloadController extends Controller
{
    public function download(Request $request, DownloadService $download_service)
    {
        return $download_service -> download($request);
    }
}
