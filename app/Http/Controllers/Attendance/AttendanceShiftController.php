<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\ShiftRequest;
use App\Services\Attendance\AttendanceShiftService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceShiftController extends Controller
{
    public array $statusOption;
    private AttendanceShiftService $attendanceShiftService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->attendanceShiftService = new AttendanceShiftService();
        $this->appMasterDataService = new AppMasterDataService();

        \View::share('statusOption', $this->statusOption);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        return $this->attendanceShiftService->data($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $locations = $this->appMasterDataService->getMasterForArray('ELK');

        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'locations' => $locations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ShiftRequest $request
     * @return JsonResponse
     */
    public function store(ShiftRequest $request)
    {
        $response = $this->attendanceShiftService->saveShift($request);

        return response()->json([
            'success' => $response['message'],
            'url' => route(Str::replace('/', '.', $this->menu_path()) . '.index')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        return view(Str::replace('/', '.', $this->menu_path()) . '.form', [
            'locations' => $this->appMasterDataService->getMasterForArray('ELK'),
            'shift' => $this->attendanceShiftService->getShiftById($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ShiftRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ShiftRequest $request, int $id)
    {
        $response = $this->attendanceShiftService->saveShift($request, $id);

        return response()->json([
            'success' => $response['message'],
            'url' => route(Str::replace('/', '.', $this->menu_path()) . '.index')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $this->attendanceShiftService->deleteShift($id);

        return redirect()->back();

    }
}
