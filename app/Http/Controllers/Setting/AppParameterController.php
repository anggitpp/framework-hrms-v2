<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppParameterRequest;
use App\Models\Setting\AppParameter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Services\DataTable;

class AppParameterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
            return DataTables::of(AppParameter::select(['id', 'code', 'name', 'value', 'description']))
                ->filter(function ($query) use ($filter) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%");
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route('settings.app-parameters.edit', $model->id),
                        'url_destroy' => route('settings.app-parameters.destroy', $model->id)
                    ]);
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('settings.app-parameter.index');
    }

    public function data(Request $request){

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('settings.app-parameter.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppParameterRequest $request
     * @return JsonResponse
     */
    public function store(AppParameterRequest $request)
    {
        AppParameter::create($request->all());

        return response()->json([
            'success'=>'Parameter berhasil disimpan',
            'url'=> route('settings.app-parameters.index')
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
        $parameter = AppParameter::findOrFail($id);

        return view('settings.app-parameter.form', compact('parameter'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppParameterRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(AppParameterRequest $request, int $id): JsonResponse
    {
        $parameter = AppParameter::findOrFail($id);
        $parameter->update($request->all());

        return response()->json([
            'success'=>'Parameter berhasil diupdate',
            'url'=> route('settings.app-parameters.index')
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
        $parameter = AppParameter::findOrFail($id);
        $parameter->delete();

        Alert::success('Parameter berhasil dihapus');

        return redirect()->back();
    }
}
