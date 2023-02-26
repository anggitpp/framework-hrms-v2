<?php
namespace App\Services\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeFileRequest;
use App\Repositories\Employee\EmployeeFileRepository;
use App\Models\Employee\EmployeeFile;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeFileService extends Controller
{
    private EmployeeFileRepository $employeeFileRepository;
    private string $filePath;
    public function __construct()
    {
        $this->employeeFileRepository = new EmployeeFileRepository(
            new EmployeeFile()
        );
        $this->filePath = '/uploads/employee/file/';
    }

    public function getFiles(): Builder
    {

        $query = $this->employeeFileRepository->getFiles();
        $user = Auth::user();

        $permission = Permission::findByName('lvl3 ' . $this->menu_path());
        if (!empty($permission))
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path()))
                $query->where('employee_positions.leader_id', $user->employee_id);

        return $query;
    }

    public function getFileById(int $id): EmployeeFile
    {
        return $this->employeeFileRepository->getById($id);
    }

    public function getFileWithSpecificColumn(array $columns): Builder
    {
        return $this->getFiles()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getFileWithSpecificColumn([
                'employee_files.id',
                'employee_files.name',
                'employee_files.filename',
                'employee_files.description',
                'employee_number',
                'employees.name as employee_name']);
            $filter = $request->get('search')['value'];
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $queryFilter = function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation) {
                if (isset($filter)) $query->where('employees.name', 'like', "%{$filter}%")->orWhere('employee_files.name', 'like', "%{$filter}%");
                if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                if (isset($filterRank)) $query->where('rank_id', $filterRank);
                if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                if (isset($filterLocation)) $query->where('location_id', $filterLocation);
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'filename', 'type' => 'filename']
            ], true);
        }
    }

    public function saveFile(EmployeeFileRequest $request, int $id = 0): void
    {

        $fields = [
            'employee_id' => $request->input('employee_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];

        if ($id == 0)
            $file = $this->employeeFileRepository->create($fields);
        else
            $file = $this->employeeFileRepository->update($fields, $id);

        defaultUploadFile($file, $request, $this->filePath, 'employee-file_' . Str::slug($request->input('name')) . '_' . time());
    }

    public function deleteFile(int $id): void
    {
        $file = $this->employeeFileRepository->getById($id);

        if($file->filename != null) deleteFile($file->filename);

        $file->delete();
    }

    public function exportFile(Request $request): BinaryFileResponse
    {
        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');

        $sql = $this->getFileWithSpecificColumn(['employee_files.*', 'employee_number', 'employees.name as employee_name']);
        if ($filterPosition) $sql->where('employee_positions.position_id', $filterPosition);
        if ($filterRank) $sql->where('employee_positions.rank_id', $filterRank);
        if ($filterGrade) $sql->where('employee_positions.grade_id', $filterGrade);
        if ($filterLocation) $sql->where('employee_positions.location_id', $filterLocation);
        if ($filter) $sql->where(function ($query) use ($filter) {
            $query->where('employee_files.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.employee_number', 'like', '%' . $filter . '%');
        });

        $data = [];
        $files = $sql->get();
        foreach ($files as $k => $file){
            $data[] = [
                $k + 1,
                $file->employee_number." ",
                $file->employee_name,
                $file->name,
                $file->description,
            ];
        }

        $columns = ["no", "data pegawai" => ["nip", "nama"], "data file" => ["nama", "deskripsi"]];

        $widths = [10, 20, 30];

        $aligns = ['center', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data File',
            ]
        ), 'Data File.xlsx');
    }
}
