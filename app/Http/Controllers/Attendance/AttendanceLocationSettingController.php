<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceLocationSetting;
use App\Models\Setting\AppMasterData;
use Barryvdh\Debugbar\Facades\Debugbar;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AttendanceLocationSettingController extends Controller
{
    public array $types;

    public function __construct()
    {
        $this->middleware('auth');

        $this->types = array(
            '1' => 'WFO',
            '2' => 'WFH',
            '3' => 'Bebas',
        );
        \View::share('types', $this->types);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(DB::table('app_master_data as t1')
                ->leftJoin('attendance_location_settings as t2', 't1.id', 't2.location_id')
                ->select([
                    't1.id',
                    't1.name',
                    't2.address',
                    't2.wfh',
                    't2.latitude',
                    't2.longitude',
                    't2.radius'])
                ->where('app_master_category_code', 'ELK'))
                ->filter(function ($query) use ($filter) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%")->orWhere('address', 'like', "%{$filter}%");
                })
                ->editColumn('wfh', function ($model) {
                    return view('components.views.status', [
                        'status' => $model->wfh,
                    ]);
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                    ]);
                })
                ->addIndexColumn()
                ->make(true);
        }

        return view('attendances.location-setting.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $data['master'] = AppMasterData::find($id);
        $data['location'] = AttendanceLocationSetting::where('location_id', $id)->first();

        return view('attendances.location-setting.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        Debugbar::info($request->all());
        try {
            AttendanceLocationSetting::updateOrCreate(
                [
                    'location_id' => $id,
                ],[
                'address' => $request->input('address'),
                'wfh' => $request->input('wfh') ?? 'f',
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'radius' => $request->input('radius'),
            ]);

            return response()->json([
                'success'=>'Setting Lokasi berhasil diupdate',
                'url'=> route('attendances.location-settings.index')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal '.$e->getMessage(),
                'url'=> route('attendances.location-settings.index')
            ]);
        }
    }
}
