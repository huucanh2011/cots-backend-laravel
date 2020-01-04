<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Comment;
use App\Post;

class CommentsController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['index', 'show', 'getCommentHome']]);
    }

    public function index()
    {
        $comments = Comment::with('user', 'post')->latest()->paginate(10);

        return $this->respond($comments);
    }

    public function getCommentHome() {
        $comments = Comment::with('user', 'post')->latest()->get();

        return $this->respond($comments);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_content' => 'required|string'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $comment = Comment::create([
            'comment_content' => $request->comment_content,
            'post_id' => $request->post_id,
            'user_id' => auth()->user()->id
        ]);

        return $this->respond(Comment::with('user', 'post')->findOrFail($comment->id));
    }

    public function show($id)
    {
        $comment = Comment::with('user', 'post')->findOrFail($id);
        
        return $this->respond($comment);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update($request->all());

        return $this->respond(Comment::with('user', 'post')->findOrFail($comment->id));
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
