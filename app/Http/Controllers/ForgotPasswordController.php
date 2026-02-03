<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetOtpMail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
     public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email not found'], 422);
        }

        // anti spam (60 sec)
        if ($user->otp_last_sent_at && now()->diffInSeconds($user->otp_last_sent_at) < 60) {
            return response()->json(['error' => 'Wait 60s before retry'], 429);
        }

        $otp = strtoupper(Str::random(6));
        $user->update([
            'pass_reset_otp' => Hash::make($otp), // secure
            'otp_created_at' => now(),
            'otp_last_sent_at' => now(),
        ]);

        Mail::to($user->email)->send(new ResetOtpMail($otp));

        return response()->json(['message' => 'OTP sent']);
    }



    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->otp, $user->pass_reset_otp)) {
            return response()->json(['error' => 'Invalid OTP'], 422);
        }

        if (now()->diffInMinutes($user->otp_created_at) > 5) {
            return response()->json(['error' => 'OTP expired'], 422);
        }

        return response()->json(['success' => true]);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'pass_reset_otp' => null,
            'otp_created_at' => null,
            'otp_last_sent_at' => null,
        ]);

        return response()->json(['message' => 'Password updated']);
    }
}
