<?php

namespace Database\Seeders;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Booking::create([
            'user_id' => 1, // Assuming you have a user with ID 1
            'date' => Carbon::today(),
            'start_time' => '09:00',
            'hours' => 2,
            'lift_type' => 'Electric',
            'workstation' => '1',
            'package_hours' => 9,
            'rate_per_hour' => 50,
            'total' => 100,
            'status' => 'confirmed',
        ]);

        Booking::create([
            'user_id' => 2, // Assuming you have a user with ID 2
            'date' => Carbon::today()->addDays(1),
            'start_time' => '10:00',
            'hours' => 3,
            'lift_type' => 'Manual',
            'workstation' => '2',
            'package_hours' => 18,
            'rate_per_hour' => 60,
            'total' => 180,
            'status' => 'pending',
        ]);

        // Add more bookings if needed
    }
}
