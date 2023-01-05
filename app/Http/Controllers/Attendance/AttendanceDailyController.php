<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\Attendance\AttendanceDailyExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\DailyRequest;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceCorrection;
use App\Models\Attendance\AttendanceLeave;
use App\Models\Attendance\AttendanceLocationSetting;
use App\Models\Attendance\AttendancePermission;
use App\Models\Attendance\AttendanceShift;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
use DebugBar\DebugBar;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Str;
use Yajra\DataTables\DataTables;

class AttendanceDailyController extends Controller
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
    public function index()
    {
        $defaultShift = AttendanceShift::orderBy('id')->first();
        $user = Auth::user();

        $employees = Employee::select(['id','name', 'employee_number', DB::raw("CONCAT(employee_number, ' - ', name) as empName")])
            ->whereHas('position', function ($query) use ($user) {
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path())) $query->where('leader_id', $user->employee_id);
            })
            ->pluck('empName', 'id')
            ->toArray();

        Session::put('employees', $employees);
        Session::put('defaultShift', $defaultShift);
        Session::put('user', $user);

        return view('attendances.daily.index');
    }

    public function data(Request $request)
    {
        $defaultShift = Session::get('defaultShift');
        $user = Session::get('user');

        if($request->ajax()){
            $filter = $request->get('search')['value'];

            if(Str::contains($request->get('filter_1'), ' to ')){
                list($startDate, $endDate) = explode(' to ', $request->get('filter_1'));
            }else{
                $startDate = $request->get('filter_1') ?? date('d/m/Y');
                $endDate = $request->get('filter_1') ?? date('d/m/Y');
            }

            $startDate = resetDate($startDate);
            $endDate = resetDate($endDate);

            $schedules = AttendanceWorkSchedule::whereBetween('date', [$startDate, $endDate])->get();

            $permissions = AttendancePermission::whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->get();

            $leaves = AttendanceLeave::whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->get();

            $arrSchedule = [];
            foreach ($schedules as $schedule) {
                $arrSchedule[$schedule->employee_id] = $schedule;
            }

            $attendances = DB::table('attendances as t1')
                ->join('employees as t2', 't1.employee_id', 't2.id')
                ->join('employee_positions as t3', function ($join){
                    $join->on('t2.id', 't3.employee_id');
                    $join->where('t3.status', 't');
                })
                ->select(['t1.id', 't1.type', 't1.type_id', 't2.name', 't2.employee_number', 't1.employee_id', 't1.start_date', 't1.start_time','t1.end_time', 't1.duration', 't1.description'])
                ->whereBetween('t1.start_date', [$startDate, $endDate]);

            if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                $attendances->where('t3.leader_id', $user->employee_id);

            $attendances->orderBy('t2.name')->orderBy('t1.start_date');

            return DataTables::of($attendances)
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('t2.name', 'like', "%$filter%")
                            ->orWhere('t2.employee_number', 'like', "%$filter%");
                    });
                })
                ->editColumn('start_date', function ($model) {
                    return setDate($model->start_date);
                })
                ->editColumn('start_time', function ($model) {
                    return substr($model->start_time,0,5);
                })
                ->editColumn('end_time', function ($model) {
                    return substr($model->end_time,0,5);
                })
                ->editColumn('duration', function ($model) {
                    return substr($model->duration,0,5);
                })
                ->editColumn('category', function ($model) {
                    if($model->type == 'C'){
                        $text = 'Cuti';
                        $badge = 'badge-warning fw-bold';
                    }else if($model->type == 'I') {
                        $text = 'Izin';
                        $badge = 'badge-info fw-bold';
                    }else {
                        $text = 'Hadir';
                        $badge = 'badge-success fw-bold';
                    }
                    return view('components.datatables.badge', [
                        'value' => $text,
                        'class' => $badge,
                    ]);
                })
                ->editColumn('description', function ($model) use ($arrSchedule, $defaultShift, $permissions, $leaves){
                    $description = $model->description;

                    $startShift = $defaultShift->start;
                    if (isset($arrSchedule[$model->employee_id])) $startShift = $arrSchedule[$model->employee_id]->start_time;

                    if($model->start_time > $startShift) {
                        $diffInMinutes = Carbon::parse($model->start_time)->diffInMinutes($startShift);
                        $description = "Terlambat ". $diffInMinutes ." menit";

                        if($diffInMinutes >= 60) $description = Carbon::parse($model->start_time)->diff($startShift)->format('Terlambat %h Jam %I Menit');
                    }

                    if($model->type == 'I') $description = $permissions->find($model->type_id)->category->name ?? '';
                    if($model->type == 'C') $description = $leaves->find($model->type_id)->leaveMaster->name ?? '';

                    return $description;
                })
                ->addColumn('start_shift', function ($model) use ($arrSchedule, $defaultShift) {
                    $startShift = $defaultShift->start;
                    if (isset($arrSchedule[$model->employee_id])) {
                        $startShift = $arrSchedule[$model->employee_id]->start_time;
                    }
                    return substr($startShift,0,5);
                })
                ->addColumn('end_shift', function ($model) use ($arrSchedule, $defaultShift) {
                    $endShift = $defaultShift->end;
                    if (isset($arrSchedule[$model->employee_id])) {
                        $endShift = $arrSchedule[$model->employee_id]->end_time;
                    }
                    return substr($endShift, 0, 5);
                })
                ->addColumn('action', function ($model) {
                    $arrAction = [
                        'menu_path' => $this->menu_path(),
                        'url_destroy' => route(Str::replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ];

                    if($model->type == '1' || $model->type == '2' || $model->type == '3') $arrAction = array_merge($arrAction, ['url_edit' => route(Str::replace('/', '.', $this->menu_path()).'.edit', $model->id)]);
                    return view('components.views.action', $arrAction);
                })
                ->addIndexColumn()
                ->make();
        }
    }

    public function create()
    {
        $employees = Session::get('employees');

        return view('attendances.daily.form', [
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DailyRequest $request
     * @return JsonResponse
     */
    public function store(DailyRequest $request)
    {
        //if end time more than start time, then add carbon + 1 day
        $endDate = $request->get('start_time') > $request->get('end_time') ? Carbon::parse(resetDate($request->get('start_date')))->addDay()->format('d/m/Y') : $request->get('start_date');
        $duration = Carbon::parse($request->get('start_time'))->diff(Carbon::parse($request->get('end_time')))->format('%H:%I');

        Attendance::create([
            'employee_id' => $request->get('employee_id'),
            'type' => 1,
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($endDate),
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('end_time'),
            'duration' => $duration,
            'start_address' => $request->get('start_address'),
            'end_address' => $request->get('end_address'),
            'description' => $request->get('description'),
        ]);

        return response()->json([
            'success'=>'Data Absen berhasil disimpan',
            'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
        ]);
    }

    public function edit($id)
    {
        $employees = Session::get('employees');
        $attendance = Attendance::findOrFail($id);

        return view('attendances.daily.form', [
            'attendance' => $attendance,
            'employees' => $employees,
        ]);
    }

    public function update(DailyRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        //if end time more than start time, then add carbon + 1 day
        $endDate = $request->get('start_time') > $request->get('end_time') ? Carbon::parse(resetDate($request->get('start_date')))->addDay()->format('d/m/Y') : $request->get('start_date');

        \Debugbar::info($endDate);

        $duration = Carbon::parse($request->get('start_time'))->diff(Carbon::parse($request->get('end_time')))->format('%H:%I');

        $attendance->update([
            'employee_id' => $request->get('employee_id'),
            'type' => 1,
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($endDate),
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('end_time'),
            'duration' => $duration,
            'start_address' => $request->get('start_address'),
            'end_address' => $request->get('end_address'),
            'description' => $request->get('description')
        ]);

        return response()->json([
            'success'=>'Data Absen berhasil disimpan',
            'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
        ]);
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        Alert::success('Data Absen berhasil dihapus');

        return redirect()->back();
    }

    public function export(Request $request)
    {
        $defaultShift = AttendanceShift::orderBy('id')->first();

        if(Str::contains($request->get('filter_1'), ' to ')){
            list($startDate, $endDate) = explode(' to ', $request->get('filter_1'));
        }else{
            $startDate = $request->get('filter_1');
            $endDate = $request->get('filter_1');
        }

        $startDate = resetDate($startDate);
        $endDate = resetDate($endDate);

        $schedules = AttendanceWorkSchedule::whereBetween('date', [$startDate, $endDate])->get();

        $permissions = AttendancePermission::whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->get();

        $leaves = AttendanceLeave::whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->get();

        $arrSchedule = [];
        foreach ($schedules as $schedule) {
            $arrSchedule[$schedule->employee_id] = $schedule;
        }

        $sql = DB::table('attendances as t1')->join('employees as t2', 't1.employee_id', 't2.id')
            ->select(['t1.id', 't1.type', 't1.type_id', 't2.name', 't2.employee_number', 't1.employee_id', 't1.start_date', 't1.start_time','t1.end_time', 't1.duration', 't1.description'])
            ->whereBetween('t1.start_date', [$startDate, $endDate]);
        if(!empty($request->get('filter')))
            $sql->where('name', 'like', '%' . $request->get('filter') . '%')
                ->orWhere('employee_number', 'like', '%' . $request->get('filter') . '%');
        $sql->orderBy('t2.name')->orderBy('t1.start_date');

        $attendances = $sql->get();

        $data = [];
        foreach ($attendances as $k => $attendance) {
            $no = $k + 1;
            $startShift = $defaultShift->start;
            if (isset($arrSchedule[$attendance->employee_id])) {
                $startShift = $arrSchedule[$attendance->employee_id]->start_time;
            }

            $endShift = $defaultShift->end;
            if (isset($arrSchedule[$attendance->employee_id])) {
                $endShift = $arrSchedule[$attendance->employee_id]->end_time;
            }

            $description = $attendance->description;

            if($attendance->start_time > $startShift) {
                $diffInMinutes = Carbon::parse($attendance->start_time)->diffInMinutes($startShift);
                $description = "Terlambat ". $diffInMinutes ." menit";

                if($diffInMinutes >= 60) $description = Carbon::parse($attendance->start_time)->diff($startShift)->format('Terlambat %h Jam %I Menit');
            }

            if($attendance->type == 'C'){
                $category = 'Cuti';
            }else if($attendance->type == 'I') {
                $category = 'Izin';
            }else {
                $category = 'Hadir';
            }

            if($attendance->type == 'I') $description = $permissions->find($attendance->type_id)->category->name ?? '';
            if($attendance->type == 'C') $description = $leaves->find($attendance->type_id)->leaveMaster->name ?? '';

            $data[] = [
                'no' => $no,
                'employee_number' => $attendance->employee_number.' ',
                'name' => $attendance->name,
                'start_date' => setDate($attendance->start_date),
                'start_shift' => $startShift,
                'end_shift' => $endShift,
                'start_time' => $attendance->start_time,
                'end_time' => $attendance->end_time,
                'duration' => $attendance->duration,
                'category' => $category,
                'description' => $description,
            ];
        }

        $dateText = $startDate == $endDate ? setDate($startDate, 't') : setDate($startDate, 't') . ' - ' . setDate($endDate, 't');

        return Excel::download(new AttendanceDailyExport(
            [
                'data' => $data,
                'title' => 'Data Absen Harian',
                'subtitle' => "TANGGAL : ".$dateText,
            ]
        ), 'Data Harian.xlsx');
    }
}
