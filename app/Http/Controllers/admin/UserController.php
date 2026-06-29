<?php

namespace App\Http\Controllers\admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 0)->latest()->paginate(10);


        return view('admin.pages.users', compact('users'));
    }
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {

            foreach ($user->bookings as $booking) {

                $booking->slots()->delete();

                $booking->delete();
            }

            $user->delete();
        });

        return redirect()
            ->back()
            ->with('success', 'User deleted successfully.');
    }
}
