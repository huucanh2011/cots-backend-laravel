<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Role;

class RolesController extends ApiController
{
    public function __construct()
    {
        $this->middleware(['admin', 'jwt.auth']);
    }
    
    public function index()
    {
        return $this->respond(Role::paginate(5));
    }

    public function search(Request $request)
    {
        if($search = $request->q)
        {
            $roles = Role::where('role_name', 'LIKE', '%'.$search.'%')->paginate(5);
        }
        else
        {
            $roles = Role::paginate(5);
        }
        return $this->respond($roles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|unique:roles|string|max:255',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $role = Role::create($request->all());

        return $this->respond($role);
    }

    public function show($id)
    {
        return $this->respond(Role::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update($request->all());

        return $this->respond($role);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role = $role->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
