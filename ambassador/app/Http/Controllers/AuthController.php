<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Link;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(UserRequest $request)
    {
        $data = $request->safe()->only(['first_name', 'last_name', 'email']) + [
            'password' => Hash::make($request->input('password'))
        ];
        $user = User::create($data);
        return response($user, Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $attempt = Auth::attempt($request->only(['email', 'password']));
        if($attempt){
            $user = Auth::user();
            $ability = $user->is_admin ? 'admin' : 'ambassador';
            $token = $user->createToken('token', [$ability])->plainTextToken;
            $cookie = cookie('sanctum_token', $token, 60*24*30*3);
            return response([
                'message' => 'Login success',
                'user' => $user,
                'token' => $token
            ], Response::HTTP_ACCEPTED)->withCookie($cookie);
        }else{
            return response([
                'message' => 'Invalid Credentials'
            ], Response::HTTP_FORBIDDEN);
        }
    }
    public function user(Request $request)
    {
        $user = new UserResource($request->user());
        return response([
            'user' => $user
        ], Response::HTTP_OK);
    }
    public function logout()
    {
        $cookie = Cookie::forget('sanctum_token');
        return response([
            'message' => 'Logout Success'
        ])->withCookie($cookie);
    }
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'email' => ['email', Rule::unique("users")->ignore($user->id)]
        ]);
        $user->update($request->only(['first_name', 'last_name', 'email']));
        return response([
            'user' => $user
        ], Response::HTTP_ACCEPTED);
    }
    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'password' => 'required|min:6',
            'password_confirm' => 'required|same:password'
        ]);
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);
        return response([
            'user' => $user
        ], Response::HTTP_ACCEPTED);
    }
    public function showLinks(Request $request, $id){
        return Link::where('user_id', $id)->get();
    }
}
