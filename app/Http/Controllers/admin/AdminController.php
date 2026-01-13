<?php

// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Holiday;
use Illuminate\Http\Request;

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
        $bookings = Booking::all();
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
}

