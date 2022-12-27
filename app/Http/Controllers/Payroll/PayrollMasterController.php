<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\HolidayRequest;
use App\Http\Requests\Payroll\MasterRequest;
use App\Models\Attendance\AttendanceHoliday;
use App\Models\Payroll\PayrollMaster;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Str;
use Yajra\DataTables\DataTables;

class PayrollMasterController extends Controller
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
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(PayrollMaster::select(['id', 'code', 'name', 'description', 'status']))
                ->filter(function ($query) use ($filter) {
                    if (isset($filter)) $query->where('name', 'like', "%$filter%");
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(Str::replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(Str::replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ]);
                })
                ->addColumn('status', function ($model) {
                    return view('components.views.status', [
                        'status' => $model->status,
                    ]);
                })
                ->addIndexColumn()
                ->make();
        }

        return view('payrolls.master.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('payrolls.master.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MasterRequest $request
     * @return JsonResponse
     */
    public function store(MasterRequest $request)
    {
        PayrollMaster::create([
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Data Master Gaji berhasil disimpan',
            'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
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
        $data['master'] = PayrollMaster::findOrFail($id);

        return view('payrolls.master.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MasterRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(MasterRequest $request, int $id)
    {
        $master = PayrollMaster::findOrFail($id);
        $master->update([
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success'=>'Data Master Gaji berhasil disimpan',
            'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
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
        $master = PayrollMaster::findOrFail($id);
        $master->delete();

        Alert::success('Success', 'Data Master Gaji berhasil dihapus!');

        return redirect()->back();
    }
}
