<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use App\User;

class DecentralizationController extends ApiController
{
    public function __construct()
    {
        $this->middleware(['jwt.auth', 'admin']);
    }

    public function index()
    {
        $users = User::with('role')->where('id', '!=', 1)->latest()->paginate(10);

        return $this->respond($users);
    }

    public function search(Request $request)
    {
        if($search = $request->q)
        {
            $users = User::with('role')
                        ->where('id', '!=', 1)
                        ->where(function ($query) use ($search) {
                            $query->where('name', 'LIKE', '%'.$search.'%')
                                  ->orWhere('email', 'LIKE', '%'.$search.'%');
                        })
                        ->latest()
                        ->paginate(10);
        }
        else
        {
            $users = User::with('role')->where('id', '!=', 1)->latest()->paginate(10);
        }
        return $this->respond($users);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update(['role_id' => $request->role_update]);

        return $this->respond($user);
    }

    public function block(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->update(['is_active' => $request->is_active]);

        return $this->respond($user);
    }
}
