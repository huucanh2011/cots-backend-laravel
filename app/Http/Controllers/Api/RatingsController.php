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
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ratings = Rating::with('user', 'partner')->paginate(10);

        return $this->respond($ratings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rating = Rating::create([
            'rating_content' => $request->rating_content,
            'rating_scores' => $request->rating_scores,
            'user_id' => auth()->user()->id,
            'partner_id' => $request->partner_id
        ]);

        return $this->respond($rating);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rating = Rating::findOrFail($id);

        return $this->respond($rating);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rating = Rating::findOrFail($id);
        $rating->update($request->all());

        return $this->respond($rating);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rating = Rating::findOrFail($id);
        $rating->delete();

        return response()->json([
            'message' => 'Delete successfully'
        ]);
    }
}
