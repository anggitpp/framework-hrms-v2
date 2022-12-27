<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ESS\EssTimesheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    public function performances(Request $request)
    {
        $filterMonth = $request->get('month');
        $filterYear = $request->get('year');
        if(Auth::check()){
            $user = Auth::user();
            $timesheets = EssTimesheet::select(['date'])
                ->whereMonth('date', $filterMonth)
                ->whereYear('date', $filterYear)
                ->whereEmployeeId($user->employee_id)
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->simplePaginate(10);

            foreach ($timesheets as $timesheet) {
                $dailyTimesheet = EssTimesheet::select(['id', 'activity', 'output', 'date', 'start_time', 'end_time', 'duration', 'volume', 'type', 'description'])
                    ->whereDate('date', $timesheet->date)
                    ->whereEmployeeId($user->employee_id);
                $timesheet->total = $dailyTimesheet->count();
                $timesheet->perfomances = $dailyTimesheet->get();
            }

            return response()->json([
                'message' => 'Success',
                'data' => $timesheets
            ]);
        }else{
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function empties(Request $request){
        $filterMonth = $request->get('month');
        $filterYear = $request->get('year');

        if(Auth::check()){
            $user = Auth::user();

            //get list of date in month and year with carbon
            $dates = Carbon::createFromDate($filterYear, $filterMonth, 1)->daysInMonth;
            $dateList = [];
            for ($i = 1; $i <= $dates; $i++) {
                $date = Carbon::createFromDate($filterYear, $filterMonth, $i)->format('Y-m-d');
                $dateNow = Carbon::now()->format('Y-m-d');
                if($date < $dateNow){
                    $carbonDate = Carbon::create($date);
                    if($carbonDate->isWeekday()) {
                        if (!EssTimesheet::whereDate('date', $date)->whereEmployeeId($user->employee_id)->exists()) {
                            $dateList[] = $date;
                        }
                    }
                }
            }

            rsort($dateList);

            return response()->json([
                'message' => 'Success',
                'data' => $dateList
            ]);
        }else{
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function store(Request $request)
    {
        if(Auth::check()){
            $user = Auth::user();

            try {
                $duration = Carbon::parse($request->get('end_time'))->diff(Carbon::parse($request->get('start_time')))->format('%H:%I');

                EssTimesheet::create([
                    'employee_id' => $user->employee_id,
                    'activity' => $request->get('activity'),
                    'output' => $request->get('output'),
                    'date' => $request->get('date'),
                    'start_time' => $request->get('start_time'),
                    'end_time' => $request->get('end_time'),
                    'duration' => $duration,
                    'volume' => $request->get('volume'),
                    'type' => $request->get('type'),
                    'description' => $request->get('description'),
                ]);

                return response()->json([
                    'message' => 'Success',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed',
                    'data' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function edit(int $id)
    {
        if(Auth::check()){
            try {
                $performance = EssTimesheet::find($id);

                return response()->json([
                    'message' => 'Success',
                    'data' => $performance
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed',
                    'data' => $e->getMessage()
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function update(Request $request, int $id)
    {
        if(Auth::check()){
            try {
                $duration = Carbon::parse($request->get('end_time'))->diff(Carbon::parse($request->get('start_time')))->format('%H:%I');

                $timesheet = EssTimesheet::find($id);
                if($timesheet){
                    $timesheet->update([
                        'activity' => $request->get('activity'),
                        'output' => $request->get('output'),
                        'date' => $request->get('date'),
                        'start_time' => $request->get('start_time'),
                        'end_time' => $request->get('end_time'),
                        'duration' => $duration,
                        'volume' => $request->get('volume'),
                        'type' => $request->get('type'),
                        'description' => $request->get('description'),
                    ]);

                    return response()->json([
                        'message' => 'Success',
                    ]);
                }else{
                    return response()->json([
                        'message' => 'Failed, data not found',
                    ], 400);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed',
                    'data' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function delete(int $id)
    {
        if(Auth::check()){
            try {
                EssTimesheet::find($id)->delete();

                return response()->json([
                    'message' => 'Success',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed',
                    'data' => $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }
}
