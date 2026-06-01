<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Holiday;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Support\Facades\Log;


class BookingController extends Controller
{
    public function index(Request $request)
    {
        $product = null;
        if ($request->product_id) {
            $product = Product::with(['prices', 'images'])->findOrFail($request->product_id);
        }

        $allLiftProducts = Product::with('prices')
            ->where('status', 1)
            ->whereIn('id', [15, 16, 18, 21, 22])
            ->get();

        return view('pages.booking', compact('product', 'allLiftProducts'));
    }

    // public function store(StoreBookingRequest $request)
    // {
    //     return DB::transaction(function () use ($request) {

    //         $date        = $request->date;
    //         $startHour   = (int) substr($request->start, 0, 2);
    //         $hours       = (int) $request->hours;
    //         $workstation = (int) $request->workstation;

    //        $times = [];

    //             for ($i = 0; $i < $hours; $i++) {

    //                 $hour = $startHour + $i;

    //                 $times[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00:00';
    //             }

    //         $exists = BookingSlot::where('date', $date)
    //             ->where('workstation', $workstation)
    //             ->whereIn('time', $times)
    //             ->exists();

    //         if ($exists) {
    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'One or more slots already booked',
    //             ], 409);
    //         }

    //         $booking = Booking::create([
    //             'user_id'      => auth()->id(),
    //             'date'         => $date,
    //             'product_id'   => $request->product_id,
    //             'start_time'   => $request->start,
    //             'hours'        => $hours,
    //             'lift_type'    => $request->lift,
    //             'workstation'  => $workstation,
    //             'package_hours' => $request->package,
    //             'rate_per_hour' => $request->total / $hours,
    //             'total'        => $request->total,
    //             'status'       => 'confirmed',
    //         ]);

    //         foreach ($times as $time) {

                
    //             BookingSlot::updateOrCreate(
    //                 [
    //                     'date'        => $date,
    //                     'time'        => $time,
    //                     'workstation' => $workstation,
    //                 ],
    //                 [
    //                     'booking_id'  => $booking->id,
    //                     'status'      => 'booked',
    //                 ]
    //             );
    //         }
    //         return response()->json([
    //             'status'     => true,
    //             'booking_id' => $booking->id,
    //         ]);
    //     });
    // }


   public function store(StoreBookingRequest $request)
{
    return DB::transaction(function () use ($request) {

        $date        = $request->date;
        $startHour   = (int) substr($request->start, 0, 2);
        $hours       = (int) $request->hours;
        $workstation = (int) $request->workstation;
        $lift        = $request->lift;

        $times = [];
        for ($i = 0; $i < $hours; $i++) {
            $hour = $startHour + $i;
            $times[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00:00';
        }

        // 🔧 FIX: Check only non-expired slots
        // $exists = BookingSlot::where('date', $date)
        //     ->where('workstation', $workstation)
        //     ->whereIn('time', $times)
        //     ->where(function($query) {
        //         $query->where('status', 'booked')
        //             ->orWhere(function($q) {
        //                 $q->where('status', 'pending')
        //                   ->whereHas('booking', function($b) {
        //                       $b->where('expires_at', '>', now())
        //                         ->orWhereNull('expires_at');
        //                   });
        //             });
        //     })
        //     ->exists();

        // if ($exists) {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => 'One or more slots already booked',
        //     ], 409);
        // }
          $exists = BookingSlot::join(
            'bookings',
            'booking_slots.booking_id',
            '=',
            'bookings.id'
        )
        ->where('booking_slots.date', $date)
        ->where('booking_slots.workstation', $workstation)
        ->where('bookings.lift_type', $lift)
        ->whereIn('booking_slots.time', $times)
        ->where(function ($query) {

            $query->where('booking_slots.status', 'booked')

                ->orWhere(function ($q) {

                    $q->where('booking_slots.status', 'pending')
                    ->where(function ($b) {

                        $b->where('bookings.expires_at', '>', now())
                            ->orWhereNull('bookings.expires_at');

                    });

                });

        })
        ->exists();

        if ($exists) {

            return response()->json([
                'status'  => false,
                'message' => 'One or more slots are already booked or reserved.',
            ], 409);

        }

        $expiresAt = now()->addMinutes(30);

        // Create the booking
        $booking = Booking::create([
            'user_id'       => auth()->id(),
            'date'          => $date,
            'product_id'    => $request->product_id,
            'start_time'    => $request->start,
            'hours'         => $hours,
            'lift_type'     => $request->lift,
            'workstation'   => $workstation,
            'package_hours' => $request->package,
            'rate_per_hour' => $request->total / $hours,
            'total'         => $request->total,
            'status'        => 'confirmed',
            'expires_at'    => $expiresAt
        ]);

        // Create or update booking slots
        foreach ($times as $time) {
            BookingSlot::updateOrCreate(
                [
                    'date'        => $date,
                    'time'        => $time,
                    'workstation' => $workstation,
                ],
                [
                    'booking_id'  => $booking->id,
                    'status'      => 'booked',
                ]
            );
        }

        return response()->json([
            'status'     => true,
            'booking_id' => $booking->id,
        ]);
    });
}

    public function calendarData(Request $request)
    {
        $workstation = (int) ($request->workstation ?? 1);

        $month = $request->get('month');
        $start = $month
            ? Carbon::createFromFormat('Y-m', $month)->startOfMonth()
            : now()->startOfMonth();

        $end = (clone $start)->endOfMonth();

        $slots = BookingSlot::query()
            ->select('date', 'time')
            ->where('workstation', $workstation)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', ['booked', 'pending']) // Include pending slots in calendar
            ->get();

        $bookedSlots = [];
        foreach ($slots as $s) {
            $bookedSlots[$s->date][] = $s->time;
        }

        $holidays = Holiday::whereBetween('holiday_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('holiday_date')
            ->toArray();

        $holidayMap = array_flip($holidays);

        $dayData = [];
        $period  = CarbonPeriod::create($start, $end);

        foreach ($period as $day) {
            $dateStr = $day->toDateString();

            if (isset($holidayMap[$dateStr])) {
                $dayData[$dateStr] = ['status' => 'unavailable'];
                continue;
            }

            $dayOfWeek     = $day->dayOfWeek;
            $requiredSlots = 9;
            if ($dayOfWeek === 5) $requiredSlots = 3;
            if ($dayOfWeek === 6) {
                $dayData[$dateStr] = ['status' => 'unavailable'];
                continue;
            }

            $countBooked = isset($bookedSlots[$dateStr]) ? count($bookedSlots[$dateStr]) : 0;

            if ($countBooked >= $requiredSlots) {
                $dayData[$dateStr] = ['status' => 'booked'];
            }
        }

        return response()->json([
            'dayData'     => $dayData,
            'bookedSlots' => $bookedSlots,
            'range'       => [
                'start' => $start->toDateString(),
                'end'   => $end->toDateString(),
            ],
        ]);
    }

    /**
     * Store a guest (unauthenticated) booking.
     * Slot held for 30 minutes with status = pending.
     */
//     public function storeGuestBooking(Request $request)
//     {
//         $validated = $request->validate([
//             'guest_name'  => 'required|string|max:255',
//             'guest_phone' => 'required|string|max:20',
//             'date'        => 'required|date',
//             'start'       => 'required|string',
//             'hours'       => 'required|integer|min:1',
//             'lift'        => 'required|string',
//             'package'     => 'required|integer',
//             'workstation' => 'required|integer',
//             'total'       => 'required|numeric',
//             'product_id'  => 'nullable|exists:products,id',
//         ]);

//         return DB::transaction(function () use ($validated) {
//             $date        = $validated['date'];
//             $startHour   = (int) substr($validated['start'], 0, 2);
//             $hours       = (int) $validated['hours'];
//             $workstation = (int) $validated['workstation'];

//            $times = [];

// for ($i = 0; $i < $hours; $i++) {

//     $hour = $startHour + $i;

//     $times[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00:00';
// }
//             // Check for any already-booked or pending slots in the same window
//             $exists = BookingSlot::where('date', $date)
//                 ->where('workstation', $workstation)
//                 ->whereIn('time', $times)
//                 ->whereIn('status', ['booked', 'pending'])
//                 ->exists();

//             if ($exists) {
//                 return response()->json([
//                     'status'  => false,
//                     'message' => 'One or more slots are already booked or reserved.',
//                 ], 409);
//             }

//             $expiresAt = now()->addMinutes(30);

//             $booking = Booking::create([
//                 'user_id'       => null,
//                 'guest_name'    => $validated['guest_name'],
//                 'guest_phone'   => $validated['guest_phone'],
//                 'date'          => $date,
//                 'product_id'    => $validated['product_id'] ?? null,
//                 'start_time'    => $validated['start'],
//                 'hours'         => $hours,
//                 'lift_type'     => $validated['lift'],
//                 'workstation'   => $workstation,
//                 'package_hours' => $validated['package'],
//                 'rate_per_hour' => $validated['total'] / $hours,
//                 'total'         => $validated['total'],
//                 'status'        => 'pending',
//                 'booking_type'  => 'guest',
//                 'expires_at'    => $expiresAt,
//             ]);

//            foreach ($times as $time) {

   
//     BookingSlot::updateOrCreate(
//         [
//             'date'        => $date,
//             'time'        => $time,
//             'workstation' => $workstation,
//         ],
//         [
//             'booking_id'  => $booking->id,
//             'status'      => 'booked',
//         ]
//     );
// }

//             return response()->json([
//                 'status'     => true,
//                 'booking_id' => $booking->id,
//                 'expires_at' => $expiresAt->toIso8601String(),
//             ]);
//         });
//     }


public function storeGuestBooking(Request $request)
{
    $validated = $request->validate([
        'guest_name'  => 'required|string|max:255',
        'guest_phone' => 'required|string|max:20',
        'date'        => 'required|date',
        'start'       => 'required|string',
        'hours'       => 'required|integer|min:1',
        'lift'        => 'required|string',
        'package'     => 'required|integer',
        'workstation' => 'required|integer',
        'total'       => 'required|numeric',
        'product_id'  => 'nullable|exists:products,id',
    ]);

    return DB::transaction(function () use ($validated) {
        $date        = $validated['date'];
        $startHour   = (int) substr($validated['start'], 0, 2);
        $hours       = (int) $validated['hours'];
        $workstation = (int) $validated['workstation'];
        $lift        = $validated['lift'];

        $times = [];
        for ($i = 0; $i < $hours; $i++) {
            $hour    = $startHour + $i;
            $times[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00:00';
        }

        // 🔧 FIX: Check only non-expired slots
        // $exists = BookingSlot::where('date', $date)
        //     ->where('workstation', $workstation)
        //     ->whereIn('time', $times)
        //     ->where(function($query) {
        //         $query->where('status', 'booked')
        //             ->orWhere(function($q) {
        //                 $q->where('status', 'pending')
        //                   ->whereHas('booking', function($b) {
        //                       $b->where('expires_at', '>', now())
        //                         ->orWhereNull('expires_at');
        //                   });
        //             });
        //     })
        //     ->exists();

        // if ($exists) {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => 'One or more slots are already booked or reserved.',
        //     ], 409);
        // }
        $exists = BookingSlot::join(
            'bookings',
            'booking_slots.booking_id',
            '=',
            'bookings.id'
        )
        ->where('booking_slots.date', $date)
        ->where('booking_slots.workstation', $workstation)
        ->where('bookings.lift_type', $lift)
        ->whereIn('booking_slots.time', $times)
        ->where(function ($query) {

            $query->where('booking_slots.status', 'booked')

                ->orWhere(function ($q) {

                    $q->where('booking_slots.status', 'pending')
                    ->where(function ($b) {

                        $b->where('bookings.expires_at', '>', now())
                            ->orWhereNull('bookings.expires_at');

                    });

                });

        })
        ->exists();

        if ($exists) {

            return response()->json([
                'status'  => false,
                'message' => 'One or more slots are already booked or reserved.',
            ], 409);

        }
        
        $expiresAt = now()->addMinutes(30);

        // Create the booking
        $booking = Booking::create([
            'user_id'       => null,
            'guest_name'    => $validated['guest_name'],
            'guest_phone'   => $validated['guest_phone'],
            'date'          => $date,
            'product_id'    => $validated['product_id'] ?? null,
            'start_time'    => $validated['start'],
            'hours'         => $hours,
            'lift_type'     => $validated['lift'],
            'workstation'   => $workstation,
            'package_hours' => $validated['package'],
            'rate_per_hour' => $validated['total'] / $hours,
            'total'         => $validated['total'],
            'status'        => 'pending',
            'booking_type'  => 'guest',
            'expires_at'    => $expiresAt,
        ]);

        // Create or update booking slots
        foreach ($times as $time) {
            BookingSlot::updateOrCreate(
                [
                    'date'        => $date,
                    'time'        => $time,
                    'workstation' => $workstation,
                ],
                [
                    'booking_id'  => $booking->id,
                    'status'      => 'pending',
                ]
            );
        }

        return response()->json([
            'status'     => true,
            'booking_id' => $booking->id,
            'expires_at' => $expiresAt->toIso8601String(),
        ]);
    });
}
    /**
     * Confirm guest booking payment and update status to 'confirmed' (booked)
     */
    public function confirmGuestPayment(Request $request)
    {
        $validated = $request->validate([
            'booking_id'     => 'required|integer|exists:bookings,id',
            'payment_method' => 'required|string',
            'amount'         => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            try {
                // Find the booking
                $booking = Booking::where('id', $validated['booking_id'])
                    ->where('status', 'pending')
                    ->where('booking_type', 'guest')
                    ->first();

                if (!$booking) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Booking not found or already confirmed.'
                    ], 404);
                }

                // Check if booking has expired
                if ($booking->expires_at && now()->greaterThan($booking->expires_at)) {
                    // Delete expired booking and slots
                    BookingSlot::where('booking_id', $booking->id)->delete();
                    $booking->delete();

                    return response()->json([
                        'status'  => false,
                        'message' => 'Booking has expired. Please create a new booking.'
                    ], 410);
                }

                // Update booking status to confirmed
                $booking->update([
                    'status' => 'confirmed',
                ]);

                // Update all related booking slots to 'booked'
                BookingSlot::where('booking_id', $booking->id)
                    ->update(['status' => 'booked']);

                return response()->json([
                    'status'  => true,
                    'message' => 'Payment confirmed successfully!',
                    'booking' => $booking
                ]);

            } catch (\Exception $e) {
                Log::error('Guest payment confirmation error: ' . $e->getMessage());
                
                return response()->json([
                    'status'  => false,
                    'message' => 'Payment confirmation failed: ' . $e->getMessage()
                ], 500);
            }
        });
    }

    public function getBlockedTimes(Request $request)
    {
        $bookings = Booking::where('lift_type', $request->lift_type)
            ->whereDate('date', $request->date)
            ->get(['start_time', 'hours']);

        $times = [];

        foreach ($bookings as $booking) {

            $startHour = (int) substr($booking->start_time, 0, 2);

            for ($i = 0; $i < $booking->hours; $i++) {

                $times[] =
                    str_pad($startHour + $i, 2, '0', STR_PAD_LEFT)
                    . ':00:00';
            }
        }

        // remove duplicates + reindex
        $times = array_values(array_unique($times));

        sort($times);

        return response()->json([
            'start_times' => $times
        ]);
    }

    public function checkBookingHours(Request $request)
    {
        $date      = $request->date;
        $lift      = $request->lift;
        $startTime = $request->start_time;
        $hours     = (int) $request->hours;

        // new booking range
        $newStart = Carbon::createFromFormat('H:i', $startTime);
        $newEnd   = (clone $newStart)->addHours($hours);

        $bookings = Booking::where('date', $date)
            ->where('lift_type', $lift)
            ->get();

        foreach ($bookings as $booking) {

            $existingStart = Carbon::createFromFormat(
                'H:i:s',
                $booking->start_time
            );

            $existingEnd = (clone $existingStart)
                ->addHours($booking->hours);

            // overlap check
            if (
                $newStart < $existingEnd &&
                $newEnd > $existingStart
            ) {

                return response()->json([
                    'ok'      => false,
                    'message' => 'Time slot occupied.'
                ]);
            }
        }

        return response()->json([
            'ok' => true
        ]);
    }
}