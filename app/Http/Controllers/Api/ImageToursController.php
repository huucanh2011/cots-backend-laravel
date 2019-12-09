<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\ImageTour;

class ImageToursController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $imageTours = ImageTour::with('tour')->latest()->get();

        return $this->respond($imageTours);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if($request->hasFile('image_name'))
        {
            $file = $request->file('image_name');
            $disk = Storage::disk('gcs');
            $path = $disk->put('tours', $file);
            $name = Str::after($path, 'tours/'); 
            $data['image_name'] = $name;

            try {
                $imageTour = ImageTour::create($data);
            } catch (Exception $e) {
                return response()->json($e, 400);
            }

            return $this->respond($imageTour);
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
        $imageTour = ImageTour::with('tour')->where('tour_id', $id)->latest()->get();
        
        return $this->respond($imageTour);
    }

    public function destroy($id)
    {
        $imageTour = ImageTour::where('tour_id', $id)->delete();
        
        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
