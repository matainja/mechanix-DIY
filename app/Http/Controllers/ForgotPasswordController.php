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

        // // Anti-spam check (60 seconds)
        // if ($user->otp_last_sent_at) {
        //     $secondsSinceLastSent = now()->diffInSeconds($user->otp_last_sent_at);
            
        //     if ($secondsSinceLastSent < 60) {
        //         $remainingSeconds = 60 - $secondsSinceLastSent;
        //         return response()->json([
        //             'error' => "Please wait {$remainingSeconds} seconds before requesting a new OTP",
        //             'remaining_seconds' => $remainingSeconds,
        //             'retry_after' => $remainingSeconds
        //         ], 429);
        //     }
        // }

        // Generate 6-character OTP (alphanumeric)
        $otp = strtoupper(Str::random(6));
        
        // Update user with new OTP
        $user->update([
            'pass_reset_otp' => Hash::make($otp),
            'otp_created_at' => now(),
            'otp_last_sent_at' => now(),
        ]);

        // Send email
        try {
            Mail::to($user->email)->send(new ResetOtpMail($otp));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send email. Please try again.'
            ], 500);
        }

        return response()->json([
            'message' => 'OTP sent successfully to your email',
            'success' => true
        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 422);
        }

        if (!$user->pass_reset_otp) {
            return response()->json(['error' => 'No OTP found. Please request a new one.'], 422);
        }

        // Check if OTP matches
        if (!Hash::check($request->otp, $user->pass_reset_otp)) {
            return response()->json(['error' => 'Invalid OTP'], 422);
        }

        // Check if OTP has expired (5 minutes)
        if (!$user->otp_created_at) {
            return response()->json(['error' => 'Invalid OTP session'], 422);
        }

        $minutesSinceCreation = now()->diffInMinutes($user->otp_created_at);
        
        if ($minutesSinceCreation > 5) {
            // Clear expired OTP
            $user->update([
                'pass_reset_otp' => null,
                'otp_created_at' => null,
            ]);
            
            return response()->json(['error' => 'OTP has expired. Please request a new one.'], 422);
        }

        return response()->json([
            'message' => 'OTP verified successfully',
            'success' => true
        ], 200);
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

        // Verify OTP session is still valid
        if (!$user->otp_created_at) {
            return response()->json(['error' => 'Invalid session. Please start over.'], 422);
        }

        $minutesSinceCreation = now()->diffInMinutes($user->otp_created_at);
        
        if ($minutesSinceCreation > 5) {
            $user->update([
                'pass_reset_otp' => null,
                'otp_created_at' => null,
                'otp_last_sent_at' => null,
            ]);
            
            return response()->json(['error' => 'Session expired. Please start over.'], 422);
        }

        // Update password and clear OTP data
        $user->update([
            'password' => Hash::make($request->password),
            'pass_reset_otp' => null,
            'otp_created_at' => null,
            'otp_last_sent_at' => null,
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
            'success' => true
        ], 200);
    }
}