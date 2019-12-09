<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Tour;
use App\Location;

class SearchController extends ApiController
{
    public function search(Request $request)
    {
        $search = $request->q;
        $locations = Location::where('location_name', 'LIKE', '%'.$search.'%')
                            ->orWhere('address', 'LIKE', '%'.$search.'%')
                            ->latest()
                            ->paginate(6);

        $tours = Tour::where('tour_name', 'LIKE', '%'.$search.'%')
                            ->orWhere('from_place', 'LIKE', '%'.$search.'%')
                            ->orWhere('to_place', 'LIKE', '%'.$search.'%')
                            ->latest()
                            ->paginate(6);

        return response()->json([
            'locations' => $locations,
            'tours' => $tours
        ]);
    }
}
