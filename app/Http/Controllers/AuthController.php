<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->only('first_name', 'last_name', 'email') + [
            'password' => Hash::make($request->input('password')),
            'is_admin' => 1
        ]);
        return response($user, Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        if(!Auth::attempt($request->only('email', 'password'))){
            return response([
                'error' => 'invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $jwt = $user->createToken('token',['admin'])->plainTextToken;
        $cookie = cookie('jwt', $jwt, 60 * 24); // 24 hours
        
        return response([
            'message' => 'Login Successful'
        ])->withCookie($cookie);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function logout()
    {
        $cookie = Cookie::forget('jwt');
        return response([
            'message' => 'logout success'
        ])->withCookie($cookie);
    }

    public function updateInfo(UpdateInfoRequest $request) 
    {
        $user = $request->user();
        $user->update($request->only('first_namae', 'last_name', 'email'));
        return response($user, Response::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response($user, Response::HTTP_ACCEPTED);
    }
}
