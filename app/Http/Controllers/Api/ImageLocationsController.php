<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\ImageLocation;

class ImageLocationsController extends ApiController
{
    public function __construct()
    {
        $this->middleware(['jwt.auth', 'admin'], ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $imageLocations = ImageLocation::with('location')->latest()->get();

        return $this->respond($imageLocations);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if($request->hasFile('image_name'))
        {
            $file = $request->file('image_name');
            $disk = Storage::disk('gcs');
            $path = $disk->put('locations', $file);
            $name = Str::after($path, 'locations/'); 
            $data['image_name'] = $name;

            try {
                $imageLocations = ImageLocation::create($data);
            } catch (Exception $e) {
                return response()->json($e, 400);
            }

            return $this->respond($imageLocations);
        }
        else
        {
            return response()->json([
                'Upload image failed!'
            ]);
        }
    }

    public function show($id)
    {
        return $this->respond(ImageLocation::findOrFail($id));
    }

    public function destroy($id)
    {
        $imageLocation = ImageLocation::where('location_id', $id);

        if($imageLocation->exists()) {

            $imageLocation->delete();

            return response()->json([
                'message' => 'Deleted successfully'
            ]);
        }
    }
}
