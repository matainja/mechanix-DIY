<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 0)->latest()->paginate(10);


        return view('admin.pages.users', compact('users'));
    }
}
