<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $req) {
        $data = $req->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6',
            'role'=>'required|in:merchant,customer'
        ]);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $token = JWTAuth::fromUser($user);
        return response()->json(['user'=>$user,'token'=>$token],201);
    }

    public function login(Request $req) {
        $credentials = $req->only('email','password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error'=>'Invalid credentials'],401);
        }
        return response()->json(['token'=>$token,'user'=>auth()->user()]);
    }

    public function me() {
        return response()->json(auth()->user());
    }
}
