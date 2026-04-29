<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
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
}
