<?php
namespace App\Services\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeAssetRequest;
use App\Models\Employee\EmployeeAsset;
use App\Repositories\Employee\EmployeeAssetRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeAssetService extends Controller
{
    private EmployeeAssetRepository $employeeAssetRepository;
    private string $filePath;
    public function __construct()
    {
        $this->employeeAssetRepository = new EmployeeAssetRepository(
            new EmployeeAsset()
        );
        $this->filePath = '/uploads/employee/asset/';
    }

    public function getAssets(): Builder
    {

        $query = $this->employeeAssetRepository->getAssets();
        $user = Auth::user();

        $permission = Permission::findByName('lvl3 ' . $this->menu_path());
        if (!empty($permission))
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path()))
                $query->where('employee_positions.leader_id', $user->employee_id);

        return $query;
    }

    public function getAssetById(int $id): EmployeeAsset
    {
        return $this->employeeAssetRepository->getById($id);
    }

    public function getAssetWithSpecificColumn(array $columns): Builder
    {
        return $this->getAssets()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getAssetWithSpecificColumn([
                'employee_assets.id',
                'employee_assets.name',
                'employee_assets.number',
                'employee_assets.type_id',
                'employee_assets.start_date',
                'employee_assets.end_date',
                'employee_assets.filename',
                'employee_assets.status',
                'employee_number',
                'employees.name as employee_name']);
            $filter = $request->get('search')['value'];
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $queryFilter = function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation) {
                if (isset($filter)) $query->where('employees.name', 'like', "%{$filter}%")->orWhere('employee_assets.name', 'like', "%{$filter}%");
                if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                if (isset($filterRank)) $query->where('rank_id', $filterRank);
                if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                if (isset($filterLocation)) $query->where('location_id', $filterLocation);
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'type_id', 'type' => 'master_relationship', 'masters' => 'type'],
                ['name' => 'start_date', 'type' => 'date'],
                ['name' => 'end_date', 'type' => 'date'],
                ['name' => 'filename', 'type' => 'filename'],
                ['name' => 'status', 'type' => 'status'],
            ]);
        }
    }

    public function saveAsset(EmployeeAssetRequest $request, int $id = 0): void
    {

        $fields = [
            'employee_id' => $request->input('employee_id'),
            'number' => $request->input('number'),
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'type_id' => $request->input('type_id'),
            'date' => resetDate($request->input('date')),
            'start_date' => $request->input('start_date') ?  resetDate($request->input('start_date')) : null,
            'end_date' => $request->input('end_date') ?  resetDate($request->input('end_date')) : null,
            'description' => $request->input('description'),
        ];

        if ($id == 0)
            $asset = $this->employeeAssetRepository->create($fields);
        else
            $asset = $this->employeeAssetRepository->update($fields, $id);

        defaultUploadFile($asset, $request, $this->filePath, 'employee-asset' . Str::slug($request->input('name')) . '_' . time());
    }

    public function deleteAsset(int $id): void
    {
        $asset = $this->employeeAssetRepository->getById($id);

        if($asset->filename != null) deleteFile($asset->filename);

        $asset->delete();
    }

    public function exportAsset(Request $request): BinaryFileResponse
    {
        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');

        $sql = $this->getAssetWithSpecificColumn(['employee_assets.*', 'employee_number', 'employees.name as employee_name']);
        if ($filterPosition) $sql->where('employee_positions.position_id', $filterPosition);
        if ($filterRank) $sql->where('employee_positions.rank_id', $filterRank);
        if ($filterGrade) $sql->where('employee_positions.grade_id', $filterGrade);
        if ($filterLocation) $sql->where('employee_positions.location_id', $filterLocation);
        if ($filter) $sql->where(function ($query) use ($filter) {
            $query->where('employee_assets.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.employee_number', 'like', '%' . $filter . '%');
        });

        $data = [];
        $assets = $sql->get();
        foreach ($assets as $k => $asset){
            $data[] = [
                $k + 1,
                $asset->employee_number." ",
                $asset->employee_name,
                $asset->name,
                $asset->number,
                $asset->category->name,
                $asset->type->name,
                setDate($asset->date),
                $asset->start_date != '0000-00-00' ? setDate($asset->start_date) : '',
                $asset->end_date != '0000-00-00' ? setDate($asset->end_date) : '',
                $asset->status == 't' ? 'Aktif' : 'Tidak Aktif',
                $asset->description,
            ];
        }

        $columns = ["no", "data pegawai" => ["nip", "nama"], "data aset" => ["nama", "nomor", "kategori", "tipe", "tanggal", "tanggal mulai", "tanggal selesai"], "status", "keterangan"];

        $widths = [10, 20, 30, 30, 30, 20, 30];

        $aligns = ['center', 'center', 'left', 'left', 'left', 'left', 'left', 'center', 'center', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Aset',
            ]
        ), 'Data Aset.xlsx');
    }
}
