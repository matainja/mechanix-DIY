<?php

// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    
    // Home Dashboard
    public function home()
    {
        $totalBookings = Booking::count();
        $totalUsers = User::count();
        $totalHolidays = Holiday::count();
        $totalRevenue = 854;
       


        
        return view('admin.pages.home', compact('totalBookings', 'totalUsers', 'totalHolidays','totalRevenue'));
    }

    // Bookings
    public function bookings()
    {
        $bookings = Booking::orderBy('id', 'desc')->paginate(10);
        // dd( $bookings);
        return view('admin.pages.bookings', compact('bookings'));
    }

    // Holidays
    public function holidays()
    {
        $holidays = Holiday::all();
        return view('admin.pages.holidays', compact('holidays'));
    }

    // Manage Users
    public function users()
    {
        $users = User::all();
        return view('admin.pages.users', compact('users'));
    }

   public function approveBooking($id)
{
    $booking = Booking::findOrFail($id);
    $booking->update(['status' => 'confirmed']);
    
    DB::table('booking_slots')
        ->where('booking_id', $id)
        ->update(['status' => 'booked']);

    return response()->json(['status' => true, 'message' => 'Booking confirmed.']);
}

public function cancelBooking($id)
{
    $booking = Booking::findOrFail($id);
    $booking->update(['status' => 'cancelled']);
    return response()->json(['status' => true, 'message' => 'Booking cancelled.']);
}

public function deleteBooking($id)
{
    $booking = Booking::findOrFail($id);
    DB::table('booking_slots')->where('booking_id', $id)->delete();
    $booking->delete();
    return response()->json(['status' => true, 'message' => 'Booking deleted.']);
}
}

