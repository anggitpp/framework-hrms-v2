<?php

namespace App\Services\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\AppMasterData;
use App\Repositories\Setting\AppMasterDataRepository;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AppMasterDataService extends Controller
{

    private AppMasterDataRepository $appMasterDataRepository;
    public function __construct()
    {
        $this->appMasterDataRepository = new AppMasterDataRepository();
    }

    /**
     * @param string $code
     * @return Builder
     */
    public function getMasters(string $code): Builder
    {
        return $this->appMasterDataRepository->getMasters($code);
    }

    public function getMasterById(int $id) : AppMasterData
    {
        return $this->appMasterDataRepository->getById($id);
    }

    public function getMasterForArray(string $code, int $parentId = 0, string $orderField = 'order', $isForImport = false) : array
    {
        $query = $this->getMasters($code);
        if($parentId != 0) $query->where('parent_id', $parentId);

        if($isForImport) return $query->orderBy($orderField)->pluck('id', DB::raw('lower(name)'))->toArray();
        return $query->orderBy($orderField)->pluck('name', 'id')->toArray();
    }

    public function getMasterByParentId(int $parentId, string $orderField = 'order', array $columns = ['id', 'name']) : Collection
    {
        return $this->appMasterDataRepository->getMasters()
            ->where('parent_id', $parentId)
            ->select($columns)
            ->orderBy($orderField)
            ->get();
    }
}
