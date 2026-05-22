<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Product;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }
    public function index()
    {
        return view('pages.home');
    }



    

    public function comingSoon()
    {
        return view('pages.coming-soon');
    }

    public function membership()
    {
        return view('pages.membership');
    }

    public function contact()
    {
        return view('pages.contactus');
    }
    
    public function privacyPolicy()
    {
        return view('pages.privacypolicy');
    }
        public function commonpage()
    {
         $rentals = Product::with(['images', 'prices'])
            ->latest()
            ->get(); // Remove the where('status', 1) filter to show all products
 
        return view('pages.commonpage', compact('rentals'));
    }

}
