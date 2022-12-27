<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppMenuRequest;
use App\Models\Setting\AppMenu;
use App\Models\Setting\AppModul;
use App\Models\Setting\AppSubModul;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse as RedirectResponseAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class AppMenuController extends Controller
{
    public array $statusOption;
    public array $moduls;
    public array $arrPermission;

    public function __construct()
    {
        $this->middleware('auth');
        $this->statusOption = defaultStatus();
        $this->moduls = AppModul::active()
            ->orderBy('order')
            ->pluck('name', 'id')
            ->toArray();
        $this->arrPermission = array("view" => "View", "add" => "Create", "edit" => "Edit", "delete" => "Delete", "lvl1" => "Access Lvl 1", "lvl2" => "Access Lvl 2", "lvl3" => "Access Lvl 3");

        \View::share('statusOption', $this->statusOption);
        \View::share('moduls', $this->moduls);
        \View::share('arrPermission', $this->arrPermission);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $firstModul = AppModul::active()->orderBy('order')->value('id');
        $submoduls = AppSubModul::active()
            ->whereAppModulId($firstModul)
            ->orderBy('order')
            ->pluck('name', 'id')
            ->toArray();
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $filterModul = $request->get('combo_1') ?? $firstModul; //GET FILTER
            $getFirstChildFromModul = AppSubModul::whereAppModulId($filterModul)
                ->orderBy('order')
                ->value('id');
            $filterSubModul = $request->get('combo_2') ?? $getFirstChildFromModul;
            $isSubModulAreChildFromSelectedModul = AppSubModul::whereAppModulId($filterModul)->whereId($filterSubModul)->exists();
            $filterSubModul = $isSubModulAreChildFromSelectedModul ? $filterSubModul : $getFirstChildFromModul;
            return DataTables::of(AppMenu::whereAppModulId($filterModul)
                ->whereAppSubModulId($filterSubModul)
                ->select(['id', 'target', 'name', 'description', 'order', 'status']))
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

            return view('settings.app-menu.index', compact('submoduls'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(Request $request)
    {
        $data['filterModul'] = $request->get('filterModul') ?? AppModul::active()->orderBy('order')->value('id'); //GET FILTER

        $getFirstChildFromModul = AppSubModul::whereAppModulId($data['filterModul'])
            ->orderBy('order')
            ->value('id');
        $filterSubModul = $request->get('filterSubModul') ?? $getFirstChildFromModul;
        $isSubModulAreChildFromSelectedModul = AppSubModul::whereAppModulId($data['filterModul'])->whereId($filterSubModul)->exists();
        $data['filterSubModul'] = $isSubModulAreChildFromSelectedModul ? $filterSubModul : $getFirstChildFromModul;

        $data['menus'] = AppMenu::active()
            ->whereAppSubModulId($data['filterSubModul'])
            ->orderBy('order')
            ->pluck('name', 'id')->toArray();
        $data['submoduls'] = AppSubModul::active()
            ->where('app_modul_id', $data['filterModul'])
            ->orderBy('order')
            ->pluck('name', 'id')
            ->toArray();

        return view('settings.app-menu.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AppMenuRequest $request
     * @return JsonResponse
     */
    public function store(AppMenuRequest $request)
    {
        DB::beginTransaction();

        try {
            $menu = AppMenu::create([
                'parent_id' => $request->input('parent_id'),
                'app_modul_id' => $request->input('app_modul_id'),
                'app_sub_modul_id' => $request->input('app_sub_modul_id'),
                'name' => $request->input('name'),
                'target' => $request->input('target'),
                'description' => $request->input('description'),
                'icon' => $request->input('icon'),
                'parameter' => $request->input('parameter'),
                'order' => $request->input('order'),
                'status' => $request->input('status'),
            ]);

            $modul = AppModul::find($request->input('app_modul_id'));

            foreach ($request->input('permission') as $key => $value) {
                Permission::create([
                    'name' => $key." ".$modul->target."/".$request->input('target'),
                    'type_id' => $menu->id,
                    'method' => $key,
                ]);
            }

            DB::commit();

            return response()->json([
                'success'=>'Menu berhasil disimpan',
                'url'=> route('settings.app-menus.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success'=>$e->getMessage(),
                'url'=> route('settings.app-menus.index')
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
        $data['menu'] = AppMenu::findOrFail($id);
        $data['submoduls'] = AppSubModul::active()
            ->whereAppModulId($data['menu']->app_modul_id)
            ->orderBy('order')
            ->pluck('name', 'id')
            ->toArray();
        $data['menus'] = AppMenu::active()
            ->whereAppSubModulId($data['menu']->app_sub_modul_id)
            ->orderBy('order')
            ->pluck('name', 'id')->toArray();
        $modul = AppModul::find($data['menu']->app_modul_id);
        $permissions = Permission::where('name', 'like', '%'.$modul->target.'/'.$data['menu']->target.'%')->pluck( 'name')->toArray();
        foreach ($permissions as $permission) {
            $method = explode(" ", $permission)[0];
            $data['permissions'][$method] = $method;
        }

        return view('settings.app-menu.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AppMenuRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(AppMenuRequest $request, int $id)
    {
        DB::beginTransaction();

        try {
            $menu = AppMenu::findOrFail($id);
            $modul = AppModul::find($request->input('app_modul_id'));
            if($menu->target != $request->input('target'))
                Permission::where('name', 'like', '%'.$modul->target."/".$menu->target.'%')->where('type', 'menu')->delete();
            $menu->update([
                'parent_id' => $request->input('parent_id'),
                'app_modul_id' => $request->input('app_modul_id'),
                'app_sub_modul_id' => $request->input('app_sub_modul_id'),
                'name' => $request->input('name'),
                'target' => $request->input('target'),
                'description' => $request->input('description'),
                'icon' => $request->input('icon'),
                'parameter' => $request->input('parameter'),
                'order' => $request->input('order'),
                'status' => $request->input('status'),
            ]);

            foreach ($this->arrPermission as $key => $value) {
                $permission = Permission::where('name', $key." ".$modul->target."/".$request->input('target'))->first();
                if ($permission) {
                    if (!isset($request->input('permission')[$key])) {
                        $permission->delete();
                    }
                } else {
                    if (isset($request->input('permission')[$key])) {
                        Permission::create([
                            'name' => $key." ".$modul->target."/".$request->input('target'),
                            'type_id' => $menu->id,
                            'method' => $key,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success'=>'Menu berhasil diupdate',
                'url'=> route('settings.app-menus.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success'=>$e->getMessage(),
                'url'=> route('settings.app-menus.index')
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
        DB::beginTransaction();

        try {
            $menu = AppMenu::findOrFail($id);
            $modul = AppModul::where('id', $menu->app_modul_id)->first();
            $path_menu = $modul->target."/".$menu->target;
            Permission::where('name', 'like', '%' . $path_menu. '%')->delete();
            $menu->delete();

            Alert::success('Success', 'Data Menu berhasil dihapus!');

            DB::commit();

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();

            Alert::error('Gagal', 'Data Menu gagal dihapus!');

            return redirect()->back();
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function createChild(int $id, Request $request)
    {
        $data['filterModul'] = $request->get('filterModul') ?? AppModul::orderBy('order')->value('id'); //GET FILTER

        $getFirstChildFromModul = AppSubModul::whereAppModulId($data['filterModul'])
            ->orderBy('order')
            ->value('id');
        $filterSubModul = $request->get('filterSubModul') ?? $getFirstChildFromModul;
        $isSubModulAreChildFromSelectedModul = AppSubModul::whereAppModulId($data['filterModul'])->whereId($filterSubModul)->exists();
        $data['filterSubModul'] = $isSubModulAreChildFromSelectedModul ? $filterSubModul : $getFirstChildFromModul;

        $data['menus'] = AppMenu::active()
            ->whereAppSubModulId($data['filterSubModul'])
            ->orderBy('order')
            ->pluck('name', 'id')->toArray();
        $data['submoduls'] = AppSubModul::active()
            ->where('app_modul_id', $data['filterModul'])
            ->orderBy('order')
            ->pluck('name', 'id')
            ->toArray();
        $data['parent_id'] = $id;

        return view('settings.app-menu.form', $data);
    }

    public function subModuls($id){
        return AppSubModul::active()
            ->whereAppModulId($id)
            ->select(['id', 'name'])
            ->orderBy('order')
            ->get();
    }

    public function menus($id){
        return AppMenu::active()
            ->whereAppSubModulId($id)
            ->select(['id', 'name'])
            ->orderBy('order')
            ->get();
    }
}
