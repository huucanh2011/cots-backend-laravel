<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\ImagePost;

class ImagePostsController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $imagePosts = ImagePost::with('post')->latest()->get();

        return $this->respond($imagePosts);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if($request->hasFile('image_name'))
        {
            $file = $request->file('image_name');
            $disk = Storage::disk('gcs');
            $path = $disk->put('posts', $file);
            $name = Str::after($path, 'posts/'); 
            $data['image_name'] = $name;

            try {
                $imagePost = ImagePost::create($data);
            } catch (Exception $e) {
                return response()->json($e, 400);
            }

            return $this->respond($imagePost);
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
        $imagePost = ImagePost::with('post')->where('post_id', $id)->latest()->get();
        
        return $this->respond($imagePost);
    }

    public function destroy($id)
    {
        $imagePost = ImagePost::where('post_id', $id)->delete();
        
        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
