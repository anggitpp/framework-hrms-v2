<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\SyncWorkScheduleRequest;
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
use Illuminate\Support\Facades\DB;

class AttendanceWorkScheduleController extends Controller
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
        //SET DATA FOR FILTER EMPLOYEE
        $data['employees'] = Employee::select(['id','name', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as empName")])
            ->orderBy('name')
            ->pluck('empName', 'id')
            ->toArray();
        //SET DATA FOR SHIFTS
        $shifts = AttendanceShift::select(['id', 'name'])->pluck('name', 'id')->toArray();

        //SET DEFAULT FILTER
        $data['filterMonth'] = $request->get('filterMonth') ?? date('m');
        $data['filterYear'] = $request->get('filterYear') ?? date('Y');
        $data['filterEmployee'] = $request->get('filterEmployee') ?? array_key_first($data['employees']);

        //GET DATA SELECTED EMPLOYEE
        $data['employee'] = Employee::with('position')->find($data['filterEmployee']);
        $data['employee']->position->position_id = AppMasterData::find($data['employee']->position->position_id)->name ?? '';
        $data['employee']->position->grade_id = AppMasterData::find($data['employee']->position->grade_id)->name ?? '';
        $data['employee']->position->location_id = AppMasterData::find($data['employee']->position->location_id)->name ?? '';
        $data['employee']->position->unit_id = AppMasterData::find($data['employee']->position->unit_id)->name ?? '';

        //GET TOTAL DAYS
        $totalDays = Carbon::create($data['filterYear'], $data['filterMonth'])->daysInMonth;

        //check if exist attendance work schedule
        $checkExist = AttendanceWorkSchedule::whereEmployeeId($data['filterEmployee'])
            ->whereMonth('date', $data['filterMonth'])
            ->whereYear('date', $data['filterYear'])
            ->first();

        //get shift employee
        $shift = AttendanceShift::find($data['employee']->position->shift_id);

        //IF NOT EXIST, CREATE ALL WORK SCHEDULE IN SELECTED PERIOD
        if(!$checkExist) {
            if ($shift) {
                //create attendance work schedule
                $data['attendanceWorkSchedules'] = [];
                for ($i = 1; $i <= $totalDays; $i++) {
                    $date = Carbon::create($data['filterYear'], $data['filterMonth'], $i)->format('Y-m-d');
                    $isWeekday = Carbon::create($data['filterYear'], $data['filterMonth'], $i)->isWeekday();
                    $data['attendanceWorkSchedules'][] = [
                        'employee_id' => $data['filterEmployee'],
                        'date' => $date,
                        'shift_id' => $isWeekday ? $shift->id : 0,
                        'start_time' => $isWeekday ? $shift->start : '00:00:00',
                        'end_time' => $isWeekday ? $shift->end : '00:00:00',
                    ];
                }
                AttendanceWorkSchedule::insert($data['attendanceWorkSchedules']);
            }
        }

        //LOOP ALL WORK SCHEDULE IN SELECTED PERIOD
        $schedules = AttendanceWorkSchedule::whereEmployeeId($data['filterEmployee'])
            ->whereMonth('date', $data['filterMonth'])
            ->whereYear('date', $data['filterYear'])
            ->get();

        $arrSchedule = [];
        foreach ($schedules as $schedule) {
            $arrSchedule[$schedule->date] = $schedule;
        }

        $firstShift = AttendanceShift::orderBy('id')->first();

        for ($i = 1; $i <= $totalDays; $i++) {
            $date = Carbon::create($data['filterYear'], $data['filterMonth'], $i)->format('Y-m-d');
            $shiftName = isset($arrSchedule[$date]) ? $arrSchedule[$date]->shift_id == 0 ? "OFF" : $shifts[$arrSchedule[$date]->shift_id] ?? $firstShift->name : $firstShift->name;
            $data['schedules'][$i]['date'] = $date;
            $data['schedules'][$i]['shift'] = $shiftName;
            $data['schedules'][$i]['start'] = $arrSchedule[$date]->start_time ?? $firstShift->start;
            $data['schedules'][$i]['end'] = $arrSchedule[$date]->end_time ?? $firstShift->end;
            $data['schedules'][$i]['description'] = $arrSchedule[$date]->description ?? '';
        }

        return view('attendances.work-schedule.index', $data);
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

    public function sync()
    {
        $employees = Employee::select(['id','name', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as empName")])
            ->orderBy('name')
            ->pluck('empName', 'id')
            ->toArray();
        $locations = AppMasterData::whereAppMasterCategoryCode( 'ELK')->pluck('name', 'id')->toArray();
        $shifts = AttendanceShift::select(['id', 'name'])->pluck('name', 'id')->toArray();

        return view('attendances.work-schedule.form-sync',[
            'employees' => $employees,
            'locations' => $locations,
            'shifts' => $shifts,
        ]);
    }

    public function processSync(SyncWorkScheduleRequest $request)
    {
        $employees = Employee::select('id');
        if($request->get('employee_id')) $employees->whereId($request->get('employee_id'));
        if($request->get('location_id')) $employees->whereEmployeeNumber($request->get('location_id'));
        $employees = $employees->get();
        $shift = AttendanceShift::find($request->get('shift_id'));

        //create list of date from request start date and request end date
        $startDate = Carbon::parse(resetDate($request->get('start_date')));
        $endDate = Carbon::parse(resetDate($request->get('end_date')));
        $dates = [];
        while($startDate->lte($endDate)){
            $dates[] = $startDate->format('Y-m-d');
            $startDate->addDay();
        }

        foreach ($employees as $employee){
            foreach ($dates as $date){
                $carbonDate = Carbon::parse($date);
                if($carbonDate->isWeekend()) continue;
                AttendanceWorkSchedule::updateOrCreate([
                    'employee_id' => $employee->id,
                    'date' => $date,
                ],[
                    'shift_id' => $request->get('shift_id'),
                    'start_time' => $shift->start,
                    'end_time' => $shift->end,
                ]);
            }
        }

        return response()->json([
            'success'=>'Data Jadwal berhasil di sinkronisasi',
            'url'=> route('attendances.work-schedule.index')
        ]);
    }
}
