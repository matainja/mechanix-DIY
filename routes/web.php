<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\HolidayController;
use App\Http\Controllers\AuthPopupController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;


//Page Controller

use App\Http\Controllers\PageController;
use App\Http\Controllers\RentalController;

Route::get('/', [PageController::class, 'home'])->name('home');


// Rental Routes
Route::get('/rentals', [RentalController::class, 'index'])->name('rentals');
Route::get('/rental/{item}', [RentalController::class, 'details'])->name('rental.details');

//Booking Routes
Route::get('/booking', [BookingController::class, 'index'])->name('booking');
Route::post('/booking/confirm', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/calendar-data', [BookingController::class, 'calendarData'])->name('booking.calendarData');




// Coming Soon Page
Route::get('/coming-soon', [PageController::class, 'comingSoon'])->name('coming');



//admin
Route::get('/admin', [AdminController::class, 'home'])->name('admin.home');
Route::get('/admin/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
// Route::get('/admin/holidays', [AdminController::class, 'holidays'])->name('admin.holidays');
Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');

//admin panel
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');

    Route::post('/holidays/store-single', [HolidayController::class, 'storeSingle'])->name('holidays.storeSingle');
    Route::post('/holidays/store-weekly', [HolidayController::class, 'storeWeekly'])->name('holidays.storeWeekly');

    Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('holidays.delete');
});


//Loin & Register Popup

Route::post('/popup-login', [AuthPopupController::class, 'login'])->name('popup.login');
Route::post('/popup-register', [AuthPopupController::class, 'register'])->name('popup.register');