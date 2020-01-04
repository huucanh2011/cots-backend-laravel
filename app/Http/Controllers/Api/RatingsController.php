<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Rating;

class RatingsController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['index', 'show']]);
    }
    
    public function index()
    {
        $ratings = Rating::with('user', 'partner')->latest()->paginate(10);

        return $this->respond($ratings);
    }

    public function store(Request $request)
    {
        $rating = Rating::create([
            'rating_content' => $request->rating_content,
            'rating_scores' => $request->rating_scores,
            'user_id' => auth()->user()->id,
            'partner_id' => $request->partner_id
        ]);

        return $this->respond(Rating::with('user', 'partner')->findOrFail($rating->id));
    }

    public function show($id)
    {
        $rating = Rating::with('user', 'partner')->findOrFail($id);

        return $this->respond($rating);
    }

    public function update(Request $request, $id)
    {
        $rating = Rating::findOrFail($id);
        $rating->update($request->all());

        return $this->respond(Rating::with('user', 'partner')->findOrFail($rating->id));
    }

    public function destroy($id)
    {
        $rating = Rating::findOrFail($id);
        $rating->delete();

        return response()->json([
            'message' => 'Delete successfully'
        ]);
    }
}
