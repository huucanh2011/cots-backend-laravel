<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use DB;

class PartnersController extends ApiController
{
    public function __construct()
    {
        $this->middleware(['jwt.auth', 'admin'], ['except' => ['index', 'search', 'show', 'topPartner']]);
    }

    public function index()
    {
        $partners = User::where('role_id', 2)->with('role')->latest()->paginate(9);

        return $this->respond($partners);
    }

    public function search(Request $request)
    {
        if($search = $request->q)
        {
            $partners = User::where('role_id', 2)
                        ->with('role')
                        ->where(function ($query) use ($search) {
                            $query->where('name', 'LIKE', '%'.$search.'%')
                                  ->orWhere('email', 'LIKE', '%'.$search.'%')
                                  ->orWhere('phone_number', 'LIKE', '%'.$search.'%')
                                  ->orWhere('address', 'LIKE', '%'.$search.'%');
                        })
                        ->latest()
                        ->paginate(9);
        }
        else
        {
            $partners = User::where('role_id', 2)->with('role')->latest()->paginate(9);
        }
        return $this->respond($partners);
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

        $partner = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(123456), //password default (123456)
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'description' => $request->description,
            'role_id' => 2
        ]);

        return $this->respond($partner);
    }

    public function show($id)
    {
        $partner = User::with('role')->findOrFail($id);
        
        return $this->respond($partner);
    }

    public function update(Request $request, $id)
    {
        $partner = User::findOrFail($id);
        $partner->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'description' => $request->description,
        ]);

        return $this->respond($partner);
    }

    public function destroy($id)
    {
        $partner = User::findOrFail($id);
        $partner->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }

    public function topPartner()
    {
        $partners = DB::table('users')
            ->where('role_id', 2)
            ->where('users.is_active', 1)
            ->leftJoin('ratings', 'ratings.partner_id', '=', 'users.id')
            ->select('users.id', 'users.name', DB::raw('AVG(ratings.rating_scores) as avg_partner_scores'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('avg_partner_scores', 'desc')
            ->take(10)
            ->get();

        return $partners;
    }

    public function countBlock()
    {
        $partnersBlock = User::where('role_id', 2)->where('is_active', 0)->count();

        return $partnersBlock;
    }
}
