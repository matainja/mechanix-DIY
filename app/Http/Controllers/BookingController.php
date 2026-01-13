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


class BookingController extends Controller
{
    public function index()
    {

        // Booking page
        return view('pages.booking');
    }


    public function store(StoreBookingRequest $request)
    {

        return DB::transaction(function () use ($request) {

            $date = $request->date;
            $startHour = (int) substr($request->start, 0, 2);
            $hours = (int) $request->hours;
            $workstation = (int) $request->workstation;

            // 1️ Build all slot times
            $times = [];

            for ($i = 0; $i < $hours; $i++) {
                $hour = $startHour + $i;

                // if your system allows cross-day booking, tell me — we extend this
                $times[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
            }

            // 2 Check if ANY slot already booked
            $exists = BookingSlot::where('date', $date)
                ->where('workstation', $workstation)
                ->whereIn('time', $times)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'One or more slots already booked'
                ], 409);
            }

            // 3️ Create booking (parent)
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'date' => $date,
                'start_time' => $request->start,
                'hours' => $hours,
                'lift_type' => $request->lift,
                'workstation' => $workstation,
                'package_hours' => $request->package,
                'rate_per_hour' => $request->total / $hours,
                'total' => $request->total,
                'status' => 'confirmed',
            ]);

            // 4️ Insert slots
            foreach ($times as $time) {
                BookingSlot::create([
                    'booking_id' => $booking->id,
                    'date' => $date,
                    'time' => $time,
                    'workstation' => $workstation,
                    'status' => 'booked',
                ]);
            }

            return response()->json([
                'status' => true,
                'booking_id' => $booking->id
            ]);
        });
    }

    public function calendarData(Request $request)
    {
        $workstation = (int) ($request->workstation ?? 1);

        // default: current month range
        $month = $request->get('month'); // format: YYYY-MM (optional)
        // echo $month."\n";
        $start = $month
            ? Carbon::createFromFormat('Y-m', $month)->startOfMonth()
            : now()->startOfMonth();

        // echo $start."\n";
        $end = (clone $start)->endOfMonth();
        // echo $end."\n";



        // 1) Get booked slots for this workstation in that month
        $slots = BookingSlot::query()
            ->select('date', 'time')
            ->where('workstation', $workstation)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'booked')
            ->get();
        // bookedSlots = { "YYYY-MM-DD": ["09:00","10:00"] }
        $bookedSlots = [];
        foreach ($slots as $s) {
            $bookedSlots[$s->date][] = $s->time;
        }
    
        
        // 2) Build dayData statuses
        // Rules for dayData:
            // - holiday -> unavailable
            // - fully booked day -> booked
            // - else no entry (available)
            
            $holidays = Holiday::whereBetween('holiday_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('holiday_date')
            ->toArray();
            
         
        $holidayMap = array_flip($holidays);
     

        $dayData = [];
        $period = CarbonPeriod::create($start, $end);
        

        foreach ($period as $day) {
            $dateStr = $day->toDateString();

            // Saturday closed (your JS rule), you can also send it as unavailable:
            // if ($day->dayOfWeek === 6) { $dayData[$dateStr] = ['status' => 'unavailable']; continue; }

            // Holiday => unavailable
            if (isset($holidayMap[$dateStr])) {
                $dayData[$dateStr] = ['status' => 'unavailable'];
                continue;
            }
           

            // Fully booked day check (based on your working-hours logic)
            $dayOfWeek = $day->dayOfWeek; // 0 Sun ... 6 Sat
            $requiredSlots = 9; // Sun-Thu 9 slots (9-18)
            if ($dayOfWeek === 5) $requiredSlots = 3; // Friday 9-12 => 3 slots
            if ($dayOfWeek === 6) {
                // Saturday closed => unavailable
                $dayData[$dateStr] = ['status' => 'unavailable'];
                continue;
            }

            $countBooked = isset($bookedSlots[$dateStr]) ? count($bookedSlots[$dateStr]) : 0;

            // If all working slots are booked => booked day
            if ($countBooked >= $requiredSlots) {
                $dayData[$dateStr] = ['status' => 'booked'];
            }
        }
        

        return response()->json([
            'dayData' => $dayData,
            'bookedSlots' => $bookedSlots,
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ]
        ]);
    }
}
