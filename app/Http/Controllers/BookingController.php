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

    public function store(StoreBookingRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $date        = $request->date;
            $startHour   = (int) substr($request->start, 0, 2);
            $hours       = (int) $request->hours;
            $workstation = (int) $request->workstation;

            $times = [];
            for ($i = 0; $i < $hours; $i++) {
                $hour    = $startHour + $i;
                $times[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
            }

            $exists = BookingSlot::where('date', $date)
                ->where('workstation', $workstation)
                ->whereIn('time', $times)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => false,
                    'message' => 'One or more slots already booked',
                ], 409);
            }

            $booking = Booking::create([
                'user_id'      => auth()->id(),
                'date'         => $date,
                'product_id'   => $request->product_id,
                'start_time'   => $request->start,
                'hours'        => $hours,
                'lift_type'    => $request->lift,
                'workstation'  => $workstation,
                'package_hours' => $request->package,
                'rate_per_hour' => $request->total / $hours,
                'total'        => $request->total,
                'status'       => 'confirmed',
            ]);

            foreach ($times as $time) {
                BookingSlot::create([
                    'booking_id'  => $booking->id,
                    'date'        => $date,
                    'time'        => $time,
                    'workstation' => $workstation,
                    'status'      => 'booked',
                ]);
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
            ->where('status', 'booked')
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
    public function storeGuestBooking(Request $request)
    {
        // ⚠️  dd() removed — it was killing every request

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

            $times = [];
            for ($i = 0; $i < $hours; $i++) {
                $hour    = $startHour + $i;
                $times[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
            }

            // Check for any already-booked or pending slots in the same window
            $exists = BookingSlot::where('date', $date)
                ->where('workstation', $workstation)
                ->whereIn('time', $times)
                ->whereIn('status', ['booked', 'pending'])
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
                BookingSlot::create([
                    'booking_id'  => $booking->id,
                    'date'        => $date,
                    'time'        => $time,
                    'workstation' => $workstation,
                    'status'      => 'pending',
                ]);
            }

            return response()->json([
                'status'     => true,
                'booking_id' => $booking->id,
                'expires_at' => $expiresAt->toIso8601String(),
            ]);
        });
    }
}