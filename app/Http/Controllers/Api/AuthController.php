<?php

namespace App\Http\Controllers\Api;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\PasswordReset;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['login', 'register', 'forgotpassword', 'resetpassword']]);
    }

    //login
    public function login() {
        $credentials = request(['email', 'password']);
        
        $validator = Validator::make($credentials, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    //register
    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if(request('role_id') == 2) {
            $user = User::create([
                'name' => request('name'),
                'email' => request('email'),
                'password' => Hash::make(request('password')),
                'is_active' => 0,
                'role_id' => 2,
            ]);
        }

        if(request('role_id') == 3) {
            $user = User::create([
                'name' => request('name'),
                'email' => request('email'),
                'password' => Hash::make(request('password')),
                'role_id' => 3,
            ]);
        }

        auth()->login($user);

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token);
    }

    //logout
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    //me
    public function me()
    {
        $token = JWTAuth::fromUser(auth()->user());

        return $this->respondWithToken($token);
    }

    //update details
    public function updateDetails()
    {
        $data = request()->all();

        try {
            $userId = auth()->user()->id;
            $user = User::findOrFail($userId);

            $user->update($data);

            return response()->json([
                'message' => 'Updated infomation successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([ 'message' => $e->getMessage() ]);
        }
    }

    //update avatar
    public function updateAvatar()
    {
        if(request()->hasFile('avatar')) {
            try {
                $userId = auth()->user()->id;
                $user = User::findOrFail($userId);

                $file = request()->file('avatar');
                $disk = Storage::disk('gcs');
                $path = $disk->put('users', $file);
                $name = Str::after($path, 'users/');                 

                $user->update([
                    'avatar' => $name
                ]);

                return response()->json([
                    'avatar' => $name,
                    'message' => 'Updated avatar Successfully!'
                ]);
            } catch (Exception $e) {
                return response()->json([ 'message' => $e->getMessage() ]);
            }
        } else {
            return response()->json([
                'message' => 'File not found!'
            ]);
        }
    }
    
    //refresh
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    //forgot password
    public function forgotpassword()
    {
        $user = User::where('email', request()->email)->firstOrFail();

        $passwordReset = PasswordReset::updateOrCreate(
            [
                'email' => $user->email,
            ],
            [
                'token' => Str::random(60),
            ]
        );
        
        if($passwordReset) {
            $user->sendPasswordResetNotification($passwordReset->token);
        }

        return response()->json([
            'message' => 'Link reset đã được gửi đến email của bạn!'
        ]);
    }

    //reset password
    public function resetpassword($token) {
        request()->validate([
            'password'=>'required|min:6|confirmed',
        ]);
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();
        if(Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();

            return response()->json([
                'message' => 'Token reset không hợp lệ!'
            ]);
        }
        $user = User::where('email', $passwordReset->email)->firstOrFail();
        $updatePasswordUser = $user->update(['password' => Hash::make(request()->password)]);
        $passwordReset->delete();

        return response()->json([
            'message' => 'Success!'
        ]);
    }

    //respond with token
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'user' => auth()->user(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 24 * 7 //7 days
        ]);
    }
}
