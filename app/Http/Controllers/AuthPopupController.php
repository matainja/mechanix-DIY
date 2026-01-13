<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthPopupController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], true)) {
            $request->session()->regenerate();

            return response()->json([
                'ok' => true,
                'csrf' => csrf_token(), //  send fresh token
            ]);
        }


        return response()->json(['ok' => false, 'message' => 'Invalid email or password.'], 422);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'required|string|max:30',
            'address' => 'required|string|max:1000',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile_no' => $data['mobile_no'],
            'address' => $data['address'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'ok' => true,
            'csrf' => csrf_token(), //  send fresh token
        ]);

    }
}
