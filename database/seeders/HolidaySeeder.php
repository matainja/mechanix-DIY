<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Holiday::create([
            'holiday_name' => 'New Year',
            'holiday_date' => '2026-01-01',
        ]);

        Holiday::create([
            'holiday_name' => 'Christmas',
            'holiday_date' => '2026-12-25',
        ]);

        // Add more holidays if needed
    }
}
