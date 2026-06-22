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
            ->whereIn('id', [15, 16, 18, 21, 22,24])
            ->get();

        return view('pages.booking', compact('product', 'allLiftProducts'));
    }

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
                $hour    = $startHour + $i;
                $times[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00:00';
            }

            // FIX: filter by booking_slots.lift_type directly instead of relying on
            // the join to `bookings.lift_type`. Previously, two different lifts booked
            // at the same date/time/workstation collided into the SAME booking_slots
            // row (no lift_type column existed), so this exists-check and the slot
            // writes below were effectively shared across lifts. With lift_type now
            // a first-class column on booking_slots, each lift's availability check
            // is fully isolated.
            $exists = BookingSlot::join(
                'bookings',
                'booking_slots.booking_id',
                '=',
                'bookings.id'
            )
            ->where('booking_slots.date', $date)
            ->where('booking_slots.workstation', $workstation)
            ->where('booking_slots.lift_type', $lift)
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
                'status'        => 'pending',
                'expires_at'    => $expiresAt,
            ]);

            foreach ($times as $time) {
                // FIX: lift_type is now part of the lookup key, not just the value.
                // Before: ['date'=>$date,'time'=>$time,'workstation'=>$workstation]
                // was the *entire* key, so booking Lift B at the same date/time/
                // workstation as an already-booked Lift A would match Lift A's row
                // and overwrite its booking_id — silently stealing the slot.
                BookingSlot::updateOrCreate(
                    [
                        'date'        => $date,
                        'time'        => $time,
                        'workstation' => $workstation,
                        'lift_type'   => $lift,
                    ],
                    [
                        'booking_id' => $booking->id,
                        'status'     => 'pending',
                    ]
                );
            }

            // Handle add-on alignment rack booking (logged-in user)
            if ($request->addon_lift === 'flat2') {
                $addonBooking = Booking::create([
                    'user_id'       => auth()->id(),
                    'date'          => $date,
                    'product_id'    => null,
                    'start_time'    => $request->start,
                    'hours'         => $hours,
                    'lift_type'     => 'flat2',
                    'workstation'   => $workstation,
                    'package_hours' => $hours,
                    'rate_per_hour' => (float) $request->addon_price,
                    'total'         => (float) $request->addon_price * $hours,
                    'status'        => 'pending',
                ]);

                foreach ($times as $time) {
                    BookingSlot::updateOrCreate(
                        [
                            'date'        => $date,
                            'time'        => $time,
                            'workstation' => $workstation,
                            'lift_type'   => 'flat2',
                        ],
                        [
                            'booking_id' => $addonBooking->id,
                            'status'     => 'pending',
                        ]
                    );
                }
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

        // FIX: compute slot occupancy directly from `bookings` instead of
        // `booking_slots`. On this install, booking_slots had drifted out of
        // sync with the real bookings (rows created/overwritten under the
        // old (date,time,workstation)-only key before lift_type existed, or
        // bookings inserted by other means that never touched booking_slots
        // at all). `bookings` is the table getBlockedTimes() already reads
        // and is verified correct, so the month calendar now derives its
        // per-lift slot map from the SAME source of truth — these two views
        // can no longer disagree.
        $bookings = Booking::select('date', 'start_time', 'hours', 'lift_type', 'status', 'expires_at')
            ->where('workstation', $workstation)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where(function ($q) {
                $q->where('status', 'confirmed')
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'pending')
                            ->where(function ($q3) {
                                $q3->whereNull('expires_at')
                                    ->orWhere('expires_at', '>', now());
                            });
                    });
            })
            ->get();

        // Per-lift list — this is what the frontend uses for per-lift calendar
        // coloring, disabling, and tooltips. Built by expanding each booking's
        // (start_time, hours) into individual hour slots, exactly like
        // getBlockedTimes() does, so the two endpoints can never disagree.
        $bookedSlots = [];

        // Per-lift counts, used to build the per-lift "fully booked" status
        // map below.
        $countsByDateLift = [];

        foreach ($bookings as $b) {
            $liftKey   = $b->lift_type ?: 'all';
            $dateKey   = $b->date instanceof \Carbon\Carbon ? $b->date->toDateString() : (string) $b->date;
            $startHour = (int) substr($b->start_time, 0, 2);

            for ($i = 0; $i < $b->hours; $i++) {
                $time = str_pad($startHour + $i, 2, '0', STR_PAD_LEFT) . ':00:00';

                $bookedSlots[$dateKey . '__' . $liftKey][] = $time;
                $countsByDateLift[$dateKey][$liftKey] = ($countsByDateLift[$dateKey][$liftKey] ?? 0) + 1;
            }
        }

        // De-dupe in case of overlapping records.
        foreach ($bookedSlots as $key => $times) {
            $bookedSlots[$key] = array_values(array_unique($times));
        }

        $holidays = Holiday::whereBetween('holiday_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('holiday_date')
            ->toArray();

        $holidayMap = array_flip($holidays);

        // FIX: dayData is now keyed per (date, lift) instead of just per date.
        // Previously a single global "booked" flag was computed by summing slot
        // counts across ALL lifts combined, which meant a date could show as
        // "fully booked" overall even though the specific lift you cared about
        // still had openings (or vice versa: a lift could be completely full
        // while the date itself didn't trip the combined threshold). The
        // frontend's dayAvailClass()/disable() already expect per-lift data —
        // this makes the backend actually provide it instead of relying on
        // dayFreeRatio() to silently paper over the mismatch.
        $dayData = [];
        $period  = CarbonPeriod::create($start, $end);

        // Known lift keys this workstation tracks. Keep in sync with
        // getLiftStatuses()'s $liftKeyMap below.
        $liftKeys = ['four', 'two', 'scissor', 'flat', 'flat2'];

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

            $perLift = [];
            foreach ($liftKeys as $lk) {
                $count = $countsByDateLift[$dateStr][$lk] ?? 0;
                $perLift[$lk] = $count >= $requiredSlots ? 'booked' : 'available';
            }

            $dayData[$dateStr] = [
                'status'   => 'available', // no global status anymore — see per_lift
                'per_lift' => $perLift,
            ];
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
            'addon_lift'  => 'nullable|string|in:flat2',
            'addon_price' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $request) {
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

            // FIX: same per-lift filter as store() above.
            $exists = BookingSlot::join(
                'bookings',
                'booking_slots.booking_id',
                '=',
                'bookings.id'
            )
            ->where('booking_slots.date', $date)
            ->where('booking_slots.workstation', $workstation)
            ->where('booking_slots.lift_type', $lift)
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

            foreach ($times as $time) {
                // FIX: lift_type in the key — see store() comment above for why.
                BookingSlot::updateOrCreate(
                    [
                        'date'        => $date,
                        'time'        => $time,
                        'workstation' => $workstation,
                        'lift_type'   => $lift,
                    ],
                    [
                        'booking_id' => $booking->id,
                        'status'     => 'pending',
                    ]
                );
            }

            // Handle add-on alignment rack booking (guest)
            if (!empty($validated['addon_lift']) && $validated['addon_lift'] === 'flat2') {
                $addonBooking = Booking::create([
                    'user_id'       => null,
                    'guest_name'    => $validated['guest_name'],
                    'guest_phone'   => $validated['guest_phone'],
                    'date'          => $date,
                    'product_id'    => null,
                    'start_time'    => $validated['start'],
                    'hours'         => $hours,
                    'lift_type'     => 'flat2',
                    'workstation'   => $workstation,
                    'package_hours' => $hours,
                    'rate_per_hour' => (float) ($validated['addon_price'] ?? 0),
                    'total'         => (float) ($validated['addon_price'] ?? 0) * $hours,
                    'status'        => 'pending',
                    'booking_type'  => 'guest',
                    'expires_at'    => $expiresAt,
                ]);

                foreach ($times as $time) {
                    BookingSlot::updateOrCreate(
                        [
                            'date'        => $date,
                            'time'        => $time,
                            'workstation' => $workstation,
                            'lift_type'   => 'flat2',
                        ],
                        [
                            'booking_id' => $addonBooking->id,
                            'status'     => 'pending',
                        ]
                    );
                }
            }

            return response()->json([
                'status'     => true,
                'booking_id' => $booking->id,
                'expires_at' => $expiresAt->toIso8601String(),
            ]);
        });
    }

    public function confirmGuestPayment(Request $request)
    {
        $validated = $request->validate([
            'booking_id'     => 'required|integer|exists:bookings,id',
            'payment_method' => 'required|string',
            'amount'         => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            try {
                $booking = Booking::where('id', $validated['booking_id'])
                    ->where('status', 'pending')
                    ->where('booking_type', 'guest')
                    ->first();

                if (!$booking) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Booking not found or already confirmed.',
                    ], 404);
                }

                if ($booking->expires_at && now()->greaterThan($booking->expires_at)) {
                    BookingSlot::where('booking_id', $booking->id)->delete();
                    $booking->delete();

                    return response()->json([
                        'status'  => false,
                        'message' => 'Booking has expired. Please create a new booking.',
                    ], 410);
                }

                $booking->update(['status' => 'confirmed']);

                BookingSlot::where('booking_id', $booking->id)
                    ->update(['status' => 'booked']);

                return response()->json([
                    'status'  => true,
                    'message' => 'Payment confirmed successfully!',
                    'booking' => $booking,
                ]);

            } catch (\Exception $e) {
                Log::error('Guest payment confirmation error: ' . $e->getMessage());

                return response()->json([
                    'status'  => false,
                    'message' => 'Payment confirmation failed: ' . $e->getMessage(),
                ], 500);
            }
        });
    }

    public function getBlockedTimes(Request $request)
    {
        // NOTE: this already correctly filters by lift_type on the `bookings`
        // table (was never the broken part), kept as-is aside from minor
        // hardening: validate input and only look at active (non-expired,
        // non-cancelled) bookings so stale/expired pending guest bookings
        // don't keep blocking slots forever.
        $request->validate([
            'lift_type' => 'required|string',
            'date'      => 'required|date',
        ]);

        $bookings = Booking::where('lift_type', $request->lift_type)
            ->whereDate('date', $request->date)
            ->where(function ($q) {
                $q->where('status', 'confirmed')
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'pending')
                            ->where(function ($q3) {
                                $q3->whereNull('expires_at')
                                    ->orWhere('expires_at', '>', now());
                            });
                    });
            })
            ->get(['start_time', 'hours']);

        $times = [];

        foreach ($bookings as $booking) {
            $startHour = (int) substr($booking->start_time, 0, 2);

            for ($i = 0; $i < $booking->hours; $i++) {
                $times[] = str_pad($startHour + $i, 2, '0', STR_PAD_LEFT) . ':00:00';
            }
        }

        $times = array_values(array_unique($times));
        sort($times);

        return response()->json([
            'start_times' => $times,
        ]);
    }

    public function checkBookingHours(Request $request)
    {
        $date      = $request->date;
        $lift      = $request->lift;
        $startTime = $request->start_time;
        $hours     = (int) $request->hours;

        $newStart = Carbon::createFromFormat('H:i', $startTime);
        $newEnd   = (clone $newStart)->addHours($hours);

        $bookings = Booking::where('date', $date)
            ->where('lift_type', $lift)
            ->get();

        foreach ($bookings as $booking) {
            $existingStart = Carbon::createFromFormat('H:i:s', $booking->start_time);
            $existingEnd   = (clone $existingStart)->addHours($booking->hours);

            if ($newStart < $existingEnd && $newEnd > $existingStart) {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Time slot occupied.',
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function getLiftStatuses()
    {
        $lifts = Product::whereIn('id', [15, 16, 18, 21, 22,24])
            ->get(['id', 'name', 'status']);

        $liftKeyMap = [
            'four-post'  => 'four',
            'two-post'   => 'two',
            'scissor'    => 'scissor',
            'motorcycle' => 'flat',
            'alignment'  => 'flat2',
        ];

        $result = [];
        foreach ($lifts as $product) {
            $nameLow = strtolower($product->name);
            foreach ($liftKeyMap as $needle => $key) {
                if (str_contains($nameLow, $needle)) {
                    $result[$key] = [
                        'status'     => (int) $product->status,
                        'product_id' => $product->id,
                    ];
                    break;
                }
            }
        }

        return response()->json($result);
    }
}