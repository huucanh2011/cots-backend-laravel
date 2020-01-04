<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Post;
use App\ImagePost;
use App\Comment;
use App\Like;
use DB;

class PostsController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['index', 'indexHome', 'show']]);
        $this->middleware('admin', ['only' => ['block']]);
    }

    public function index()
    {
        $posts = Post::with('user', 'location', 'likes')->latest()->paginate(10);

        return $this->respond($posts);
    }

    public function indexHome()
    {
        $posts = Post::with('user', 'location', 'imagePosts', 'likes')->where('is_active', 1)->latest()->paginate(5);
        
        return $this->respond($posts);
    }

    public function search(Request $request)
    {
        if($search = $request->q)
        {
            $posts = Post::with('user', 'location')
                        ->where('post_content', 'LIKE', '%'.$search.'%')
                        ->orWhere('post_scores', 'LIKE', '%'.$search.'%')
                        ->latest()
                        ->paginate(10);
        }
        else
        {
            $posts = Post::with('user', 'location')->latest()->paginate(10);
        }
        return $this->respond($posts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_content' => 'required|string',
            'post_scores' => 'required',
            'location_id' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $post = Post::create([
            'post_content' => $request->post_content,
            'post_scores' => $request->post_scores,
            'is_active' => 1,
            'location_id' => $request->location_id,
            'user_id' => auth()->user()->id
        ]);

        return $this->respond(Post::with('user', 'location', 'imagePosts', 'likes')->findOrFail($post->id));
    }

    public function show($id)
    {
        $post = Post::with('user', 'location')->findOrFail($id);

        return $this->respond($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::with('user', 'location', 'imagePosts', 'likes')->findOrFail($id);

        $post->update($request->all());

        return $this->respond($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }

    public function block(Request $request)
    {
        $post = Post::with('user', 'location', 'imagePosts', 'likes')->findOrFail($request->id);
        $post->update(['is_active' => $request->is_active]);

        return $this->respond($post);
    }
}
