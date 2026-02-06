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
            ->where('status', 1) // only enabled products
            ->latest()
            ->get();

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
        ->where('status', 1)
        ->findOrFail($id);

    return view('pages.rental-details', compact('rental'));
}

}
