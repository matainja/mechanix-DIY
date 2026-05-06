<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Lifts',
                'slug' => 'lifts',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Direct Booking',
                'slug' => 'direct-booking',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Rentals',
                'slug' => 'rentals',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Discounts',
                'slug' => 'discounts',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
