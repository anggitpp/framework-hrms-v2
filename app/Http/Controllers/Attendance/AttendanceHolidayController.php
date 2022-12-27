<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\HolidayRequest;
use App\Models\Attendance\AttendanceHoliday;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class AttendanceHolidayController extends Controller
{
    public array $statusOption;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();

        \View::share('statusOption', $this->statusOption);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $filterYear = $request->get('filter_1') ?? date('Y');
            $filter = $request->get('search')['value'];
            return DataTables::of(AttendanceHoliday::select(['id', 'name', 'start_date', 'end_date', 'description', 'status']))
                ->filter(function ($query) use ($filter, $filterYear) {
                    $query->whereYear('start_date', $filterYear);
                    if (isset($filter)) $query->where('name', 'like', "%$filter%");
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ]);
                })
                ->editColumn('start_date', function ($model) {
                    return $model->start_date ? setDate($model->start_date) : '';
                })
                ->editColumn('end_date', function ($model) {
                    return $model->end_date ? setDate($model->end_date) : '';
                })
                ->addColumn('status', function ($model) {
                    return view('components.views.status', [
                        'status' => $model->status,
                    ]);
                })
                ->addIndexColumn()
                ->make();
        }

        return view('attendances.holiday.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('attendances.holiday.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param HolidayRequest $request
     * @return JsonResponse
     */
    public function store(HolidayRequest $request)
    {
        AttendanceHoliday::create([
            'name' => $request->input('name'),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($request->input('end_date')),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Data Libur Resmi berhasil disimpan',
            'url'=> route(str_replace('/', '.', $this->menu_path()).'.index')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $data['holiday'] = AttendanceHoliday::findOrFail($id);

        return view('attendances.holiday.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param HolidayRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(HolidayRequest $request, int $id)
    {
        $holiday = AttendanceHoliday::findOrFail($id);
        $holiday->update([
            'name' => $request->input('name'),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($request->input('end_date')),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Data Libur Resmi berhasil disimpan',
            'url'=> route(str_replace('/', '.', $this->menu_path()).'.index')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $holiday = AttendanceHoliday::findOrFail($id);
        $holiday->delete();

        Alert::success('Success', 'Data Libur resmi berhasil dihapus!');

        return redirect()->back();

    }
}
