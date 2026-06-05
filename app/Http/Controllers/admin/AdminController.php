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
    // public function home()
    // {
    //     $totalBookings = Booking::count();
    //     $totalUsers = User::count();
    //     $totalHolidays = Holiday::count();
    //     $totalRevenue = 84;
       


        
    //     return view('admin.pages.home', compact('totalBookings', 'totalUsers', 'totalHolidays','totalRevenue'));
    // }
public function home(Request $request)
{
    // Dashboard counts
    $confirmedBookings = Booking::where('status', 'confirmed')->count();

    $pendingBookings = Booking::where('status', 'pending')->count();

    $cancelledBookings = Booking::where('status', 'cancelled')->count();

    $regularBookings = Booking::where('booking_type', 'regular')->count();

    $guestBookings = Booking::where('booking_type', 'guest')->count();

    $totalUsers = User::count();

    $totalHolidays = Holiday::count();

    // Booking Lists with Pagination
    $confirmedBookingList = Booking::where('status', 'confirmed')
        ->latest()
        ->paginate(10, ['*'], 'confirmed_page');

    $pendingBookingList = Booking::where('status', 'pending')
        ->latest()
        ->paginate(10, ['*'], 'pending_page');

    $cancelledBookingList = Booking::where('status', 'cancelled')
        ->latest()
        ->paginate(10, ['*'], 'cancelled_page');

    $guestBookingList = Booking::where('booking_type', 'guest')
        ->latest()
        ->paginate(10, ['*'], 'guest_page');

    $regularBookingList = Booking::where('booking_type', 'regular')
        ->latest()
        ->paginate(10, ['*'], 'regular_page');

    // Users & Holidays
    $usersList = User::latest()
        ->paginate(10, ['*'], 'users_page');

    $holidaysList = Holiday::latest()
        ->paginate(10, ['*'], 'holidays_page');

    // Total Revenue
    $totalRevenue = Booking::where('status', 'confirmed')
        ->sum('total');

    // Monthly Revenue Filter
    $selectedMonth = $request->month;

    $monthlyRevenueQuery = Booking::where('status', 'confirmed');

    if ($selectedMonth) {
        $monthlyRevenueQuery->whereMonth('date', $selectedMonth);
    }

    $monthlyRevenue = $monthlyRevenueQuery->sum('total');

    // Monthly Revenue Table
    $monthlyRevenues = Booking::select(
            DB::raw('MONTH(date) as month'),
            DB::raw('SUM(total) as revenue')
        )
        ->where('status', 'confirmed')
        ->groupBy(DB::raw('MONTH(date)'))
        ->orderBy('month')
        ->get();

    return view('admin.pages.home', compact(
        'confirmedBookings',
        'pendingBookings',
        'cancelledBookings',
        'regularBookings',
        'guestBookings',
        'totalUsers',
        'totalHolidays',
        'totalRevenue',
        'monthlyRevenue',
        'selectedMonth',
        'monthlyRevenues',
        'confirmedBookingList',
        'pendingBookingList',
        'cancelledBookingList',
        'guestBookingList',
        'regularBookingList',
        'usersList',
        'holidaysList'
    ));
}

    // Bookings
    public function bookings()
    {
        $bookings = Booking::orderBy('id', 'desc')->paginate(10);
        $bookings = Booking::latest()->paginate(10);
$todayBookings = Booking::whereDate('date', today())->latest()->get();

        // dd( $bookings);
        return view('admin.pages.bookings', compact('bookings', 'todayBookings'));
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

