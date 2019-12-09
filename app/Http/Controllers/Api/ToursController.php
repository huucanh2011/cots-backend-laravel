<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\DateDepartureTour;
use App\Tour;

class ToursController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['index', 'show', 'getDate']]);
    }

    public function index()
    {
        $tours = Tour::with('user', 'tourCate')->where('is_active', 1)->latest()->paginate(9);

        return $this->respond($tours);
    }

    public function indexPartner() {
        $userId = auth()->user()->id;
        $tours = Tour::with('user', 'tourCate')->where('user_id', $userId)->latest()->paginate(10);

        return $this->respond($tours);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_name' => 'required|string|max:255',
            // 'image_cover' => 'required|image|max:255',
            'description' => 'required|string',
            'from_place' => 'required|string|max:255',
            'to_place' => 'required|string|max:255',
            'number_days' => 'required|numeric',
            'number_persons' => 'required|numeric',
            'tour_price' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if($request->hasFile('image_cover')) {
            $file = $request->file('image_cover');
            $disk = Storage::disk('gcs');
            $path = $disk->put('tours', $file);
            $imgName = Str::after($path, 'tours/'); 
        }

        $tour = Tour::create([
            'tour_name' => $request->tour_name,
            'image_cover' => $imgName,
            'description' => $request->description,
            'from_place' => $request->from_place,
            'to_place' => $request->to_place,
            'number_days' => $request->number_days,
            'number_persons' => $request->number_persons,
            'tour_price' => $request->tour_price,
            'note' => $request->note,
            'tourcate_id' => $request->tourcate_id,
            'is_active' => $request->is_active,
            'user_id' => auth()->user()->id
        ]);

        return $this->respond($tour);
    }

    public function show($id)
    {
        $tour = Tour::with('user', 'tourCate')->findOrFail($id);

        return $this->respond($tour);
    }

    public function update(Request $request, $id)
    {
        $tour = Tour::findOrFail($id);

        if($tour->user_id != auth()->user()->id) {
            return $this->respondUnauthorized();
        }

        $tour->update($request->all());

        return $this->respond($tour);
    }

    public function destroy($id)
    {
        $tour = Tour::findOrFail($id);

        if($tour->user_id != auth()->user()->id) {
            return $this->respondUnauthorized();
        }

        $tour->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }

    public function blockTour(Request $request)
    {
        $tour = Tour::findOrFail($request->id);
        $tour->update([
            'is_active' => $request->is_active
        ]);

        return $this->respond($tour);
    }

    public function getDate($id)
    {
        $dateDepartureTours = DateDepartureTour::with('tour')
            ->where('tour_id', $id)
            ->orderBy('date_departure', 'asc')
            ->get();

        return $this->respond($dateDepartureTours);
    }
    
    public function postDate(Request $request)
    {
        $dateDepartureTour = DateDepartureTour::create($request->all());

        return $this->respond($dateDepartureTour);
    }

    public function deleteDate($id)
    {
        $dateDepartureTour = DateDepartureTour::findOrFail($id);
        $dateDepartureTour->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }

}
