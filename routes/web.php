<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\HolidayController;
use App\Http\Controllers\AuthPopupController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\ProductController;


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
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminController::class, 'home'])->name('home');

        Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');

        Route::resource('products', ProductController::class)
            ->names('products');

        Route::delete('/products/{id}', [ProductController::class, 'destroy']) ->name('products.destroy');
        Route::get('/products/{product}/edit-data', [ProductController::class, 'edit'])->name('admin.products.editData');
        Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');

        Route::patch(
            '/admin/products/{id}/toggle',
            [ProductController::class, 'toggle']
        )
            ->name('products.toggle');



        // Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users', [UserController::class, 'index'])->name('users');


        // Holidays
        Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');

        Route::post('/holidays/store-single', [HolidayController::class, 'storeSingle'])->name('holidays.storeSingle');

        Route::post('/holidays/store-weekly', [HolidayController::class, 'storeWeekly'])->name('holidays.storeWeekly');

        Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('holidays.delete');
        Route::post('/holidays/bulk', [HolidayController::class, 'storeBulk'])->name('holidays.storeBulk');
    });


//Loin & Register Popup

Route::post('/popup-login', [AuthPopupController::class, 'login'])->name('popup.login');
Route::post('/popup-register', [AuthPopupController::class, 'register'])->name('popup.register');
Route::post('/logout', [AuthPopupController::class, 'logout'])->name('logout');

Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword']);
