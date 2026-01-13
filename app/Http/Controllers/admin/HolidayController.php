<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class HolidayController extends Controller
{
    public function index()
    {
        // You can paginate if you want: Holiday::orderBy('holiday_date','desc')->paginate(20)
        $holidays = Holiday::orderBy('holiday_date', 'desc')->get();

        return view('admin.pages.holidays', compact('holidays'));
    }

    public function storeSingle(Request $request)
    {
        $data = $request->validate([
            'holiday_name' => 'required|string|max:255',
            'holiday_date' => 'required|date',
        ]);

        // Prevent duplicate date entries
        Holiday::firstOrCreate(
            ['holiday_date' => $data['holiday_date']],
            ['holiday_name' => $data['holiday_name']]
        );

        return back()->with('success', 'Holiday added successfully.');
    }

    public function storeWeekly(Request $request)
    {
        $data = $request->validate([
            'holiday_name' => 'required|string|max:255',
            'weekday'      => 'required|integer|min:0|max:6', // 0=Sun ... 6=Sat
            'from_date'    => 'required|date',
            'to_date'      => 'required|date|after_or_equal:from_date',
        ]);

        $from = Carbon::parse($data['from_date'])->startOfDay();
        $to   = Carbon::parse($data['to_date'])->startOfDay();

        $period = CarbonPeriod::create($from, $to);

        $created = 0;
        foreach ($period as $day) {
            if ((int)$day->dayOfWeek === (int)$data['weekday']) {
                $holiday = Holiday::firstOrCreate(
                    ['holiday_date' => $day->toDateString()],
                    ['holiday_name' => $data['holiday_name']]
                );

                if ($holiday->wasRecentlyCreated) {
                    $created++;
                }
            }
        }

        return back()->with('success', "Generated holidays: {$created}");
    }

    public function destroy($id)
    {
        Holiday::where('id', $id)->delete();
        return back()->with('success', 'Holiday deleted successfully.');
    }
}
