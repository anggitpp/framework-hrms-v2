<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeePosition;
use App\Models\Setting\AppMasterData;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ESSPositionController extends Controller
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
        $employee = Employee::find(Auth::user()->employee_id);
        $employee->position->location_id = AppMasterData::find($employee->position->location_id)->name ?? '';
        $employee->position->position_id = AppMasterData::find($employee->position->position_id)->name ?? '';
        $employee->position->grade_id = AppMasterData::find($employee->position->grade_id)->name ?? '';
        $employee->position->unit_id = AppMasterData::find($employee->position->unit_id)->name ?? '';

        return view('ess.position.index', compact('employee'));
    }

    public function data(Request $request)
    {
        $masters = AppMasterData::whereIn('app_master_category_code', ['EP', 'EG', 'EMP'])->pluck('name', 'id')->toArray();
        if($request->ajax()){
            return DataTables::of(EmployeePosition::select(['id', 'sk_number', 'position_id', 'rank_id', 'grade_id', 'start_date', 'end_date', 'status'])
                ->where('employee_id', Auth::user()->employee_id))
                ->editColumn('position_id', function ($data) use ($masters) {
                    return $masters[$data->position_id] ?? '';
                })
                ->editColumn('rank_id', function ($data) use ($masters) {
                    return $masters[$data->rank_id] ?? '';
                })
                ->editColumn('grade_id', function ($data) use ($masters) {
                    return $masters[$data->grade_id] ?? '';
                })
                ->addColumn('period', function ($model) {
                    $endPeriod = $model->end_date ? Carbon::create($model->end_date)->format('M Y') : 'current';

                    return Carbon::create($model->start_date)->format('M Y')." - ".$endPeriod;
                })
                ->editColumn('status', function ($model) {
                    return view('components.views.status', [
                        'status' => $model->status,
                    ]);
                })
                ->addIndexColumn()
                ->make();
        }
    }
}
