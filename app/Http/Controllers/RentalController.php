<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index()
    {
        // Rental listing page
        return view('pages.rentals');
    }

    public function details($item)
    {
        // Temporary static data (later it will move to DB)
        $rentals = [
            'four-post' => [
                'title' => 'Four-Post Lift',
                'image' => asset('assets/images/rentals/four-post.png'),
                'price' => '$45 / hour',
                'note'  => 'Heavy-duty four-post lift for storage & repairs.',
                'features' => [
                    'Heavy-duty construction',
                    'Perfect for storage & repairs',
                    'Stable & safe platform',
                ],
            ],

            'two-post' => [
                'title' => 'Two-Post Lift',
                'image' => asset('assets/images/rentals/two-post.png'),
                'price' => '$40 / hour',
                'note'  => 'Professional two-post workshop lift.',
                'features' => [
                    'Professional grade',
                    'Easy underbody access',
                    'Fast lift operation',
                ],
            ],

            'scissor' => [
                'title' => 'Scissor Lift',
                'image' => asset('assets/images/rentals/scissor.png'),
                'price' => '$35 / hour',
                'note'  => 'Compact scissor lift for quick jobs.',
                'features' => [
                    'Compact design',
                    'Quick lifting',
                    'Space efficient',
                ],
            ],

            'engine-hoist' => [
                'title' => 'Engine Hoist',
                'image' => asset('assets/images/rentals/engine-hoist.png'),
                'price' => '$30 / hour',
                'note'  => 'Engine hoist for heavy engine work.',
                'features' => [
                    'Heavy duty',
                    'Easy mobility',
                    'High load capacity',
                ],
            ],

            'diag-scanner' => [
                'title' => 'Diagnostic Scanner',
                'image' => asset('assets/images/rentals/diag-scanner.png'),
                'price' => '$20 / hour',
                'note'  => 'Professional diagnostic scanning tool.',
                'features' => [
                    'OEM level diagnostics',
                    'Fast scanning',
                    'Multi-brand support',
                ],
            ],

            'ac-r134a' => [
                'title' => 'AC Machine (R134a)',
                'image' => asset('assets/images/rentals/ac-machine-r134a.png'),
                'price' => '$50 / hour',
                'note'  => 'AC service machine for R134a systems.',
                'features' => [
                    'Full AC service',
                    'Accurate gas measurement',
                    'Professional grade',
                ],
            ],

            'ac-r1234yf' => [
                'title' => 'AC Machine (R1234yf)',
                'image' => asset('assets/images/rentals/ac-machine-r1234yf.png'),
                'price' => '$60 / hour',
                'note'  => 'AC service machine for R1234yf systems.',
                'features' => [
                    'Modern AC systems',
                    'High precision',
                    'Eco gas compatible',
                ],
            ],

            'tool-rental' => [
                'title' => 'Tool Rentals',
                'image' => asset('assets/images/rentals/tool-rentals.png'),
                'price' => 'Various',
                'note'  => 'Wide range of tools available.',
                'features' => [
                    'Many tools available',
                    'Daily & hourly rental',
                    'Professional quality',
                ],
            ],
        ];

        if (!isset($rentals[$item])) {
            abort(404);
        }

        $rental = $rentals[$item];

        return view('pages.rental-details', compact('rental', 'item'));
    }
}
