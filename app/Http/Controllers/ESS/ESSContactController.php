<?php

namespace App\Http\Controllers\ESS;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeContactRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeContact;
use App\Models\ESS\EssTimesheet;
use App\Models\Setting\AppMasterData;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Str;
use Yajra\DataTables\DataTables;

class ESSContactController extends Controller
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

        return view('ess.contact.index', compact('employee'));
    }

    public function data(Request $request)
    {
        $relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('name', 'id')->toArray();
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            return DataTables::of(EmployeeContact::select(['id', 'relationship_id', 'name', 'phone_number', 'description'])
                ->where('employee_id', Auth::user()->employee_id))
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('name', 'like', "%$filter%")
                            ->orWhere('phone_number', 'like', "%$filter%");
                    });
                })
                ->editColumn('relationship_id', function ($data) use ( $relationships) {
                    return $relationships[$data->relationship_id];
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(Str::replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(Str::replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                    ]);
                })
                ->addIndexColumn()
                ->make();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('name', 'id')->toArray();

        return view('ess.contact.form', compact('relationships'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmployeeContactRequest $request
     * @return JsonResponse
     */
    public function store(EmployeeContactRequest $request)
    {
        try {
            EmployeeContact::create($request->all());

            return response()->json([
                'success'=>'Data Kontak berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal, '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $contact = EmployeeContact::findOrFail($id);
        $relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('name', 'id')->toArray();

        return view('ess.contact.form', [
            'contact' => $contact,
            'relationships' => $relationships
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmployeeContactRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(EmployeeContactRequest $request, int $id)
    {
        try {
            $contact = EmployeeContact::findOrFail($id);
            $contact->update($request->all());

            return response()->json([
                'success'=>'Data Kontak berhasil disimpan',
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'=>'Gagal, '.$e->getMessage(),
                'url'=> route(Str::replace('/', '.', $this->menu_path()).'.index')
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $contact = EmployeeContact::findOrFail($id);
        $contact->delete();

        Alert::success('Data Kontak berhasil dihapus');

        return redirect()->back();
    }
}
