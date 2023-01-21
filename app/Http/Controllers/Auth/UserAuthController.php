<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'status' => ['required', 'string', 'in:active,inactive,banned'],
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'status' => $validatedData['status'],
        ]);

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;

        return response(['token' => $token], 200);
    }

    public function login(Request $request)
{
    $validatedData = $request->validate([
        'email' => ['required', 'string', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (!Auth::attempt($validatedData)) {
        return response(['message' => 'Invalid credentials'], 401);
    }

    $user = Auth::user();
    $token = $user->createToken('Laravel Password Grant Client')->accessToken;

    return response(['token' => $token], 200);
}
}
