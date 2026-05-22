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
use App\Http\Controllers\MembershipController;


//Page Controller

use App\Http\Controllers\PageController;
use App\Http\Controllers\RentalController;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::redirect('/login', '/')->name('login');


// Rental Routes
Route::get('/rentals', [RentalController::class, 'index'])->name('rentals');
Route::get('/rental/{item}', [RentalController::class, 'details'])->name('rental.details');

//Booking Routes
Route::get('/booking', [BookingController::class, 'index'])->name('booking');
Route::post('/booking/confirm', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/calendar-data', [BookingController::class, 'calendarData'])->name('booking.calendarData');




// Coming Soon Page
Route::get('/coming-soon', [PageController::class, 'comingSoon'])->name('coming');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/membership', [PageController::class, 'membership'])->name('membership');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy');
Route::get('/commonpage', [PageController::class, 'commonpage'])->name('commonpage');
// testing

//admin
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminController::class, 'home'])->name('home');

        Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');

        // Products - using resource route (creates index, create, store, show, edit, update, destroy)
        Route::resource('products', ProductController::class);
        
        // Additional product routes
        Route::patch('/products/{id}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');
        Route::delete('/products/images/{id}', [ProductController::class, 'deleteImage'])->name('products.images.delete');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users');

        // Holidays
        Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
        Route::post('/holidays/store-single', [HolidayController::class, 'storeSingle'])->name('holidays.storeSingle');
        Route::post('/holidays/store-weekly', [HolidayController::class, 'storeWeekly'])->name('holidays.storeWeekly');
        Route::delete('/holidays/{id}', [HolidayController::class, 'destroy'])->name('holidays.delete');
        Route::post('/holidays/bulk', [HolidayController::class, 'storeBulk'])->name('holidays.storeBulk');
        //membership admin routes
        Route::get('/admin/membership-requests', [MembershipController::class, 'getAllRequests'])->name('admin.membership.requests');
        Route::post('/membership-requests/{id}/approve', [MembershipController::class, 'approveRequest'])->name('admin.membership.approve');
        Route::post('/membership-requests/{id}/reject', [MembershipController::class, 'rejectRequest'])->name('admin.membership.reject');
    
        Route::post('/membership-plans', [MembershipController::class, 'storePlan'])->name('membership.plans.store');
        Route::delete('/membership-plan/{id}', [MembershipController::class, 'deletePlan'])
    ->name('membership.plan.delete');

    Route::post('/bookings/{id}/approve', [AdminController::class, 'approveBooking'])->name('bookings.approve');
Route::post('/bookings/{id}/cancel',  [AdminController::class, 'cancelBooking'])->name('bookings.cancel');
Route::delete('/bookings/{id}',       [AdminController::class, 'deleteBooking'])->name('bookings.delete');
        });

//Loin & Register Popup

Route::post('/popup-login', [AuthPopupController::class, 'login'])->name('popup.login');
Route::post('/popup-register', [AuthPopupController::class, 'register'])->name('popup.register');
Route::post('/logout', [AuthPopupController::class, 'logout'])->name('logout');
Route::post('/booking/guest', [BookingController::class, 'storeGuestBooking'])->name('booking.guest');
Route::post('/booking/guest/confirm-payment', [BookingController::class, 'confirmGuestPayment'])
    ->name('booking.guest.confirm-payment');
Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword']);

// Membership routes
Route::get('/membership/plans', [MembershipController::class, 'getPlans'])->name('membership.plans');
Route::post('/membership/request', [MembershipController::class, 'submitRequest'])->middleware('auth')->name('membership.request');
Route::post('/membership/guest-request', [MembershipController::class, 'submitGuestRequest'])->name('membership.guest-request');
Route::post('/membership/guest-payment', [MembershipController::class, 'guestPayment'])->name('membership.guest-payment');  // ← Add this