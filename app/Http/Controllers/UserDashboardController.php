<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $confirmedCount  = $user->bookings()->where('status', 'confirmed')->count();
        $pendingCount    = $user->bookings()->where('status', 'pending')->count();
        $cancelledCount  = $user->bookings()->where('status', 'cancelled')->count();
        $totalSpent      = $user->bookings()->where('status', 'confirmed')->sum('total');

        $confirmedBookings = $user->bookings()->where('status', 'confirmed')->latest()->paginate(10, ['*'], 'confirmed_page');
        $pendingBookings   = $user->bookings()->where('status', 'pending')->latest()->paginate(10, ['*'], 'pending_page');
        $cancelledBookings = $user->bookings()->where('status', 'cancelled')->latest()->paginate(10, ['*'], 'cancelled_page');
        $allBookings       = $user->bookings()->latest()->paginate(10, ['*'], 'all_page');

        return view('admin.pages.user_dashboard', compact(
            'confirmedCount', 'pendingCount', 'cancelledCount', 'totalSpent',
            'confirmedBookings', 'pendingBookings', 'cancelledBookings', 'allBookings'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email'            => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'mobile_no'        => ['nullable', 'string', 'max:30'],
            'current_password' => ['nullable', 'string'],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->email     = $request->email;
        $user->mobile_no = $request->mobile_no;

        if ($request->filled('password')) {
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
    public function settings()
{
    $user = auth()->user();
    $confirmedCount  = $user->bookings()->where('status', 'confirmed')->count();
    $pendingCount    = $user->bookings()->where('status', 'pending')->count();
    $cancelledCount  = $user->bookings()->where('status', 'cancelled')->count();

    return view('admin.pages.profile_settings', compact(
        'confirmedCount', 'pendingCount', 'cancelledCount'
    ));
}
}