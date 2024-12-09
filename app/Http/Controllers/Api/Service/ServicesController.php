<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServicesController extends Controller
{
    public function get(Request $request)
    {
        $services = array();
        $service_categories = Service::query()
        ->distinct()
        ->pluck('category');

        foreach ($service_categories as $category) {
            $services_in_this_category = Service::query() -> where('category', $category) -> select('name', 'cost') -> get();

            $services[$category] = $services_in_this_category;
        }

        return response() -> json($services);
    }
}
