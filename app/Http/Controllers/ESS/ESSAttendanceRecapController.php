<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceShift;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ESSAttendanceRecapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $employeeId = Auth::user()->employee_id;
        //SET DEFAULT FILTER
        $data['filterMonth'] = $request->get('filterMonth') ?? date('m');
        $data['filterYear'] = $request->get('filterYear') ?? date('Y');

        //GET DATA SELECTED EMPLOYEE
        $data['employee'] = Employee::with('position')->find($employeeId);
        $data['employee']->position->position_id = AppMasterData::find($data['employee']->position->position_id)->name ?? '';
        $data['employee']->position->grade_id = AppMasterData::find($data['employee']->position->grade_id)->name ?? '';
        $data['employee']->position->location_id = AppMasterData::find($data['employee']->position->location_id)->name ?? '';
        $data['employee']->position->unit_id = AppMasterData::find($data['employee']->position->unit_id)->name ?? '';

        $startMonth = Carbon::createFromDate($data['filterYear'], $data['filterMonth'], 1)->startOfMonth();
        $endMonth = Carbon::createFromDate($data['filterYear'], $data['filterMonth'], 1)->endOfMonth();
        $data['attendances'] = Attendance::whereEmployeeId($employeeId)->whereBetween('start_date', [$startMonth, $endMonth])->orderBy('start_date')->get();
        $schedules = AttendanceWorkSchedule::whereEmployeeId($employeeId)->whereBetween('date', [$startMonth, $endMonth])->orderBy('date')->get();
        foreach ($schedules as $schedule) {
            $data['schedules'][$schedule->date] = $schedule;
        }
        $data['defaultShift'] = AttendanceShift::find($data['employee']->position->shift_id);

        return view('ess.attendance-recap.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param String $date
     * @return Application|Factory|View
     */
    public function edit(int $id, String $date)
    {
        $data['shifts'] = AttendanceShift::pluck('name', 'id')->toArray();
        $data['schedule'] = AttendanceWorkSchedule::whereEmployeeId($id)->whereDate('date', $date)->first();

        return view('attendances.work-schedule.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @param String $date
     * @return JsonResponse
     */
    public function update(Request $request, int $id, String $date)
    {
        $schedule = AttendanceWorkSchedule::whereEmployeeId($id)->whereDate('date', $date)->first();
        $schedule->shift_id = $request->get('shift_id') ?? 0;
        $schedule->start_time = $request->get('start_time');
        $schedule->end_time = $request->get('end_time');
        $schedule->description = $request->get('description');
        $schedule->save();

        return response()->json([
            'success'=>'Data Jadwal berhasil diupdate',
            'url'=> route('attendances.work-schedule.index')
        ]);
    }

    public function shift(Request $request)
    {
        $id = $request->get('shift_id');
        $shift = AttendanceShift::find($id);
        $shift->start = Carbon::parse($shift->start)->format('H:i');
        $shift->end = Carbon::parse($shift->end)->format('H:i');
        return response()->json([
            'start' => $shift->start,
            'end' => $shift->end,
        ]);
    }
}
