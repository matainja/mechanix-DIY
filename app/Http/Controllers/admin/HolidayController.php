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
        // $holidays = Holiday::orderBy('holiday_date', 'desc')->simplePaginate(10);
        $holidays = Holiday::latest()->paginate(10);


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

    public function storeBulk(Request $request)
{
    $data = $request->validate([
        'holiday_label' => 'required|string',
        'custom_label'  => 'nullable|string|max:255',
        'from_date'     => 'nullable|date',
        'to_date'       => 'nullable|date|after_or_equal:from_date',
        'weekday'       => 'nullable|integer|min:0|max:6',
        'manual_dates'  => 'nullable|string'
    ]);

    // Decide final name
    $name = $data['holiday_label'] === 'Others'
        ? ($data['custom_label'] ?: 'Holiday')
        : $data['holiday_label'];

    $created = 0;


    /*
    =====================================
    RANGE + REPEAT WEEKDAY
    =====================================
    */
    if ($data['from_date'] && $data['to_date']) {

        $period = \Carbon\CarbonPeriod::create($data['from_date'], $data['to_date']);

        foreach ($period as $day) {

            // if weekday selected → only that weekday
            if ($data['weekday'] !== null) {
                if ($day->dayOfWeek != $data['weekday']) continue;
            }

            $holiday = Holiday::firstOrCreate(
                ['holiday_date' => $day->toDateString()],
                ['holiday_name' => $name]
            );

            if ($holiday->wasRecentlyCreated) $created++;
        }
    }


    /*
    =====================================
    MANUAL MULTIPLE DATES
    =====================================
    */
    if (!empty($data['manual_dates'])) {

        $dates = explode(',', $data['manual_dates']);

        foreach ($dates as $date) {

            $holiday = Holiday::firstOrCreate(
                ['holiday_date' => trim($date)],
                ['holiday_name' => $name]
            );

            if ($holiday->wasRecentlyCreated) $created++;
        }
    }

    return back()->with('success', "Created {$created} holidays successfully 🎉");
}


    public function destroy($id)
    {
        Holiday::where('id', $id)->delete();
        return back()->with('success', 'Holiday deleted successfully.');
    }
}

