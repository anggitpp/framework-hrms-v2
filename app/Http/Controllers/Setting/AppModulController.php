<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppModulRequest;
use App\Models\Setting\AppModul;
use Illuminate\Contracts\Foundation\Application as ApplicationAlias;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse as RedirectResponseAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class AppModulController extends Controller
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
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(AppModul::select(['id', 'name', 'target', 'description', 'status', 'order']))
                ->filter(function ($query) use ($filter) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%");
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ]);
                })
                ->addColumn('status', function ($model) {
                    return view('components.views.status', [
                        'status' => $model->status,
                    ]);
                })
                ->addIndexColumn()
                ->make(true);
        }

        return view('settings.app-modul.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return ApplicationAlias|Factory|View
     */
    public function create()
    {
        return view('settings.app-modul.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppModulRequest $request
     * @return JsonResponse
     */
    public function store(AppModulRequest $request)
    {
        DB::beginTransaction();

        try {
            $modul = AppModul::create([
                'name' => $request->input('name'),
                'target' => $request->input('target'),
                'description' => $request->input('description'),
                'status' => $request->input('status'),
                'icon' => $request->input('icon'),
                'order' => $request->input('order'),
            ]);

            Permission::create([
                'name' => $request->input('target'),
                'type' => 'modul',
                'type_id' => $modul->id,
                ]);

            DB::commit();

            return response()->json([
                'success'=>'Modul berhasil disimpan',
                'url'=> route('settings.app-moduls.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'=> $e->getMessage(),
                'url'=> route('settings.app-moduls.index')
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return ApplicationAlias|Factory|View
     */
    public function edit(int $id)
    {
        $modul = AppModul::findOrFail($id);

        return view('settings.app-modul.form', compact('modul'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppModulRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(AppModulRequest $request, int $id)
    {

        DB::beginTransaction();

        try {
            $modul = AppModul::findOrFail($id);
            if($modul->target != $request->input('target')) Permission::where('name', $modul->target)->delete();

            $modul->update([
                'name' => $request->input('name'),
                'target' => $request->input('target'),
                'description' => $request->input('description'),
                'status' => $request->input('status'),
                'icon' => $request->input('icon'),
                'order' => $request->input('order'),
            ]);

            Permission::updateOrCreate([
                'name' => $request->input('target')
            ], [
                'type' => 'modul',
                'type_id' => $modul->id,
            ]);

            DB::commit();

            return response()->json([
                'success'=>'Modul berhasil diupdate',
                'url'=> route('settings.app-moduls.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'=> $e->getMessage(),
                'url'=> route('settings.app-moduls.index')
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponseAlias
     */
    public function destroy(int $id)
    {
        $modul = AppModul::findOrFail($id);
        $modul->delete();

        Permission::where('name', $modul->target)->where('type', 'modul')->delete();

        Alert::success('Success', 'Data user berhasil dihapus!');

        return redirect()->back();
    }
}
