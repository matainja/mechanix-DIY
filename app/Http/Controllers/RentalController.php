<?php

namespace App\Http\Controllers;

use App\Models\Product;

class RentalController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST RENTALS (cards page)
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $rentals = Product::with(['images', 'prices'])
            ->latest()
            ->get(); // Remove the where('status', 1) filter to show all products

        return view('pages.rentals', compact('rentals'));
    }


    /*
    |--------------------------------------------------------------------------
    | DETAILS PAGE
    |--------------------------------------------------------------------------
    */
    public function details($id)
    {
        $rental = Product::with(['images','prices'])
            ->findOrFail($id); // Remove status filter here too

        // Check if product is available on details page
        if ($rental->status != 1) {
            // Redirect back with message or show unavailable message
            return view('pages.rental-details', compact('rental'))
                ->with('unavailable', true);
        }

        return view('pages.rental-details', compact('rental'));
    }
}