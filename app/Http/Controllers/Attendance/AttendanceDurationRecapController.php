<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\Attendance\AttendanceDurationRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use FPDFTable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Yajra\DataTables\DataTables;

ini_set('memory_limit', '4096M');

class AttendanceDurationRecapController extends Controller
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
        $filterMonth = $request->get('filterMonth') ?? date('m');
        $filterYear = $request->get('filterYear') ?? date('Y');

        $units = AppMasterData::whereAppMasterCategoryCode('EMU')->pluck('name', 'id')->toArray();
        $ranks = AppMasterData::whereAppMasterCategoryCode('EP')->pluck('name', 'id')->toArray();

        $user = Auth::user();

        $totalDays = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;

        $data['units'] = $units;
        $data['ranks'] = $ranks;
        $data['filterMonth'] = $filterMonth;
        $data['filterYear'] = $filterYear;
        $data['totalDays'] = $totalDays;

        \Session::put('user', $user);

        return view('attendances.duration-recap.index', $data);
    }

    public function data(Request $request, $filterMonth, $filterYear){
        $user = \Session::get('user');
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterUnit = $request->get('combo_3');
            $filterRank = $request->get('combo_4');

            $totalDays = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;

            $employees = DB::table('employees as t1')->join('employee_positions as t2', function ($join){
                $join->on('t1.id', 't2.employee_id');
                $join->where('t2.status', 't');
            })->select('t1.id', 't1.name', 't1.employee_number');
            if($filterRank) $employees->where('rank_id', $filterRank);
            if($filterUnit) $employees->where('unit_id', $filterUnit);
            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                $employees->where('t2.leader_id', $user->employee_id);

            $arrData = $this->datas($employees->get(), $filterMonth, $filterYear);
            $table = DataTables::of($employees)
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('name', 'like', "%$filter%")
                            ->orWhere('employee_number', 'like', "%$filter%");
                    });

                });
            for ($i = 1; $i <= $totalDays; $i++) {
                $date = $filterYear.'-'.str_pad($filterMonth, 2, '0', STR_PAD_LEFT).'-' . str_pad($i, 2, '0', STR_PAD_LEFT);

                $table->addColumn('day_' . $i, function ($row) use ($i, $arrData, $date) {
                    return $arrData[$row->id][$date] ?? '';

                });
            }
            $table->addColumn('total', function ($row) use ($totalDays, $arrData) {
                $totalDuration = '';
                if(isset($arrData[$row->id]['total'])) $totalDuration = convertMinutesToTime($arrData[$row->id]['total'] / 60);

                return $totalDuration;
            });
            return $table->addIndexColumn()
                ->make();
        }
    }

    public function export(Request $request)
    {
        $user = \Session::get('user');
        $filterMonth = $request->get('filterMonth');
        $filterYear = $request->get('filterYear');
        $totalDays = Carbon::create($filterYear, $filterMonth, 1)->daysInMonth;

        $data = [];
        $sql = DB::table('employees as t1')->join('employee_positions as t2', function ($join) {
            $join->on('t1.id', 't2.employee_id');
            $join->where('t2.status', 't');
        });
        if (!empty($request->get('filter')))
            $sql->where('name', 'like', '%' . $request->get('filter') . '%')
                ->orWhere('employee_number', 'like', '%' . $request->get('filter') . '%');
        if ($request->get('combo_3') && $request->get('combo_3') != 'undefined') $sql->where('t2.unit_id', $request->get('combo_3'));
        if ($request->get('combo_4') && $request->get('combo_4') != 'undefined') $sql->where('t2.rank_id', $request->get('combo_4'));
        if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path())) $sql->where('t2.leader_id', $user->employee_id);
        $employees = $sql->select(['t1.id', 't1.name', 't1.employee_number'])->orderBy('t1.name')->get();

        $arrData = $this->datas($employees, $filterMonth, $filterYear);

        $no = 0;
        foreach ($employees as $employee) {
            $no++;

            $data[$employee->id] = [
                'no' => $no,
                'employee_number' => $employee->employee_number . " ",
                'name' => $employee->name,
            ];

            for ($i = 1; $i <= $totalDays; $i++) {
                $date = $filterYear . '-' . str_pad($filterMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $data[$employee->id][(string)$i] = $arrData[$employee->id][$date];
            }
            $data[$employee->id]['total'] = convertMinutesToTime($arrData[$employee->id]['total'] / 60);
        }


        return Excel::download(new AttendanceDurationRecapExport(
            [
                'data' => $data,
                'headerTitle' => 'Data Rekap Durasi',
                'headerSubtitle' => "PERIODE : ".numToMonth($request->get('filterMonth')).' '.$request->get('filterYear'),
                'totalDays' => Carbon::create($request->get('filterYear').'-'.$request->get('filterMonth'))->daysInMonth,
            ]
        ), 'Rekap Durasi.xlsx');
    }

    public function datas($employees, $filterMonth, $filterYear)
    {
        $startDate = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($filterYear, $filterMonth, 1)->endOfMonth()->format('Y-m-d');

        $schedules = AttendanceWorkSchedule::whereBetween('date', [$startDate, $endDate])->select(['employee_id', 'date'])->whereNot('shift_id', '0')->get();
        $attendances = Attendance::whereBetween('start_date', [$startDate, $endDate])->select(['duration', 'employee_id', 'type', 'start_date'])->get();

        $arrSchedules = [];
        foreach ($schedules as $schedule) {
            $arrSchedules[$schedule->employee_id][$schedule->date] = $schedule;
        }

        $arrAttendances = [];
        foreach ($attendances as $attendance) {
            $arrAttendances[$attendance->employee_id][$attendance->start_date] = $attendance;
        }

        $arrData = [];
        $dateNow = Carbon::now()->format('Y-m-d');
        foreach ($employees as $employee) {
            $arrData[$employee->id]['total'] = 0;
            for ($i = 1; $i <= Carbon::create($filterYear, $filterMonth, 1)->daysInMonth; $i++) {
                $date = Carbon::create($filterYear, $filterMonth, $i)->format('Y-m-d');
                $carbonDate = Carbon::create($date);
                $isFuture = false;
                if($dateNow < $date) $isFuture = true;
                if(isset($arrSchedules[$employee->id][$date]) && !isset($arrAttendances[$employee->id][$date])) {
                    $arrData[$employee->id][$date] = $carbonDate < $dateNow ? "A" : '';
                }else{
                    $data = $arrAttendances[$employee->id][$date] ?? null;
                    if($data){
                        if($data->type == 'C'){
                            $arrData[$employee->id][$date] = 'C';
                        }else if($data->type == 'I'){
                            $arrData[$employee->id][$date] = 'I';
                        }else{
                            $arrData[$employee->id][$date] = $data->duration;
                            $duration = convertTimeToSeconds($data->duration);
                            $arrData[$employee->id]['total'] += $duration;
                        }
                    }
                }

                $data = $arrData[$employee->id][$date] ?? '';
                if(!$isFuture) $data = empty($data) && $carbonDate->isWeekday() ? 'A' : $data;

                $arrData[$employee->id][$date] = substr($data, 0, 5);
            }
        }
        return $arrData;
    }
}
