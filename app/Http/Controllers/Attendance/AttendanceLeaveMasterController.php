<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\LeaveMasterRequest;
use App\Models\Attendance\AttendanceLeaveMaster;
use App\Models\Setting\AppMasterData;
use App\Services\Attendance\AttendanceLeaveMasterService;
use App\Services\Setting\AppMasterDataService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class AttendanceLeaveMasterController extends Controller
{
    public array $statusOption;
    public array $genderOption;
    private AttendanceLeaveMasterService $attendanceLeaveMasterService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->genderOption = array("a" => "Semua", "f" => "Perempuan", "m" => "Laki-laki");
        $this->attendanceLeaveMasterService = new AttendanceLeaveMasterService();
        $this->appMasterDataService = new AppMasterDataService();

        \View::share('statusOption', $this->statusOption);
        \View::share('genderOption', $this->genderOption);
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
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->attendanceLeaveMasterService->data($request);
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
     * @param LeaveMasterRequest $request
     * @return JsonResponse
     */
    public function store(LeaveMasterRequest $request)
    {
        $response = $this->attendanceLeaveMasterService->saveLeaveMaster($request);

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
            'master' => $this->attendanceLeaveMasterService->getLeaveMasterById($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LeaveMasterRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(LeaveMasterRequest $request, int $id)
    {
        $response = $this->attendanceLeaveMasterService->saveLeaveMaster($request, $id);

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
        $this->attendanceLeaveMasterService->deleteLeaveMaster($id);

        return redirect()->back();

    }
}
