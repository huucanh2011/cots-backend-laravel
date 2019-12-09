<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Like;

class LikesController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function create(Request $request)
    {
        $like = Like::create($request->all(), [
            'user_id' => auth()->user()->id,
            'post_id' => $request->post_id
        ]);

        return $this->respond($like);
    }

    public function destroy($id)
    {
        $like = Like::findOrFail($id);
        $like->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
