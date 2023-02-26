<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AppParameterRequest;
use App\Services\Setting\AppParameterService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppParameterController extends Controller
{
    private AppParameterService $appParameterService;

    public function __construct()
    {
        $this->appParameterService = new AppParameterService();
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('settings.app-parameter.index');
    }

    /**
     * @throws Exception
     */
    public function data(Request $request)
    {
        return $this->appParameterService->data($request);

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
        return submitDataHelper($this->appParameterService->saveParameter($request), true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $parameter = $this->appParameterService->getParameterById($id);

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
        return submitDataHelper($this->appParameterService->saveParameter($request, $id), true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        return deleteDataHelper($this->appParameterService->deleteParameter($id));
    }
}
