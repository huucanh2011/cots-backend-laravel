<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;

class UsersController extends ApiController
{
    public function __construct()
    {
        $this->middleware(['jwt.auth', 'admin'], ['except' => ['show']]);
    }

    public function index()
    {
        $users = User::where('role_id', 3)->with('role')->latest()->paginate(10);

        return $this->respond($users);
    }

    public function search(Request $request)
    {
        if($search = $request->q)
        {
            $users = User::where('role_id', 3)
                        ->with('role')
                        ->where(function ($query) use ($search) {
                            $query->where('name', 'LIKE', '%'.$search.'%')
                                  ->orWhere('email', 'LIKE', '%'.$search.'%')
                                  ->orWhere('phone_number', 'LIKE', '%'.$search.'%')
                                  ->orWhere('address', 'LIKE', '%'.$search.'%');
                        })
                        ->latest()
                        ->paginate(10);
        }
        else
        {
            $users = User::where('role_id', 3)->with('role')->latest()->paginate(10);
        }
        return $this->respond($users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|min:3|max:12',
            'address' => 'required|max:255'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(123456), //password default (123456)
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'description' => $request->description,
            'role_id' => 3
        ]);

        return $this->respond($user);
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        
        return $this->respond($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'description' => $request->description,
        ]);

        return $this->respond($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
