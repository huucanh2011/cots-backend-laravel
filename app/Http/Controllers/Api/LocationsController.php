<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Location;
use App\Post;
use App\ImageLocation;
use DB;

class LocationsController extends ApiController
{
    public function __construct()
    {
        $this->middleware(['jwt.auth', 'admin'], ['except' => ['index', 'show', 'search', 'topLocation']]);
    }

    public function index()
    {
        $locations = Location::latest()->paginate(10);

        return $this->respond($locations);
    }

    public function search(Request $request)
    {
        if($search = $request->q)
        {
            $locations = Location::where('location_name', 'LIKE', '%'.$search.'%')
                                  ->orWhere('address', 'LIKE', '%'.$search.'%')
                        ->latest()
                        ->paginate(10);
        }
        else
        {
            $locations = Location::latest()->paginate(10);
        }
        return $this->respond($locations);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $location = Location::create([
            'location_name' => $request->location_name,
            'address' => $request->address,
            'description' => $request->description
        ]);

        return $this->respond($location);
    }

    public function show($id)
    {
        $location = DB::table('locations')
            ->where('locations.id', $id)
            ->where('locations.is_active', 1)
            ->leftJoin('posts', 'posts.location_id', '=', 'locations.id')
            ->select('locations.location_name', 'locations.description', 'locations.address', DB::raw('AVG(posts.post_scores) as avg_location_scores'))
            ->groupBy('locations.location_name', 'locations.description', 'locations.address')
            ->take(1)
            ->get();

        $imageLocations = ImageLocation::where('location_id', $id)->get();

        $posts = Post::with('user', 'location')
            ->where('location_id', $id)
            ->latest()
            ->paginate(10);
        
        return response()->json([
            'location' => $location[0],
            'imageLocations' => $imageLocations,
            'posts' => $posts
        ]);
    }

    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);
        $location->update([
            'location_name' => $request->location_name,
            'address' => $request->address,
            'description' => $request->description
        ]);

        return $this->respond($location);
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }

    public function topLocation()
    {
        $locations = DB::table('locations')
            ->leftJoin('posts', 'locations.id', '=', 'posts.location_id')
            ->select('locations.id', 'locations.location_name', DB::raw('AVG(posts.post_scores) as avg_location_scores'))
            ->groupBy('locations.id', 'locations.location_name')
            ->orderBy('avg_location_scores', 'desc')
            ->take(10)
            ->get();

        return $this->respond($locations);
    }
}
