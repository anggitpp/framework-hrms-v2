<?php
namespace App\Services\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeFamilyRequest;
use App\Repositories\Employee\EmployeeFamilyRepository;
use App\Models\Employee\EmployeeFamily;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Str;

class EmployeeFamilyService extends Controller
{
    private EmployeeFamilyRepository $employeeFamilyRepository;
    private string $filePath;
    public function __construct()
    {
        $this->employeeFamilyRepository = new EmployeeFamilyRepository(
            new EmployeeFamily()
        );
        $this->filePath = '/uploads/employee/family/';
    }

    public function getFamilies(): Builder
    {

        $query = $this->employeeFamilyRepository->getFamilies();
        $user = Auth::user();

        $permission = Permission::findByName('lvl3 ' . $this->menu_path());
        if (!empty($permission))
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path()))
                $query->where('employee_positions.leader_id', $user->employee_id);

        return $query;
    }

    public function getFamilyById(int $id): EmployeeFamily
    {
        return $this->employeeFamilyRepository->getById($id);
    }

    public function getFamiliesWithSpecificColumn(array $columns): Builder
    {
        return $this->getFamilies()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getFamiliesWithSpecificColumn(['employee_families.id', 'employee_families.name', 'employee_families.birth_date', 'employee_families.birth_place', 'employee_number', 'employees.name as employee_name', 'relationship_id']);
            $filter = $request->get('search')['value'];
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $queryFilter = function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation) {
                if (isset($filter)) $query->where('employees.name', 'like', "%{$filter}%")->orWhere('employee_families.name', 'like', "%{$filter}%");
                if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                if (isset($filterRank)) $query->where('rank_id', $filterRank);
                if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                if (isset($filterLocation)) $query->where('location_id', $filterLocation);
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'relationship_id', 'type' => 'master_relationship', 'masters' => 'relationship'],
                ['name' => 'birth_date', 'type' => 'date'],
            ]);
        }
    }

    public function saveFamily(EmployeeFamilyRequest $request, int $id = 0): void
    {

        $fields = [
            'employee_id' => $request->input('employee_id'),
            'name' => $request->input('name'),
            'relationship_id' => $request->input('relationship_id'),
            'identity_number' => $request->input('identity_number'),
            'birth_place' => $request->input('birth_place'),
            'birth_date' => $request->input('birth_date') ? resetDate($request->input('birth_date')) : null,
            'gender' => $request->input('gender'),
            'description' => $request->input('description'),
        ];

        if ($id == 0)
            $family = $this->employeeFamilyRepository->create($fields);
        else
            $family = $this->employeeFamilyRepository->update($fields, $id);

        defaultUploadFile($family, $request, $this->filePath, 'employee-family' . Str::slug($request->input('name')) . '_' . time());
    }

    public function deleteFamily(int $id): void
    {
        $this->employeeFamilyRepository->destroy($id);
    }

    public function exportFamily(Request $request)
    {
        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');

        $sql = $this->getFamiliesWithSpecificColumn(['employee_families.*', 'employee_number', 'employees.name as employee_name']);
        if ($filterPosition) $sql->where('employee_positions.position_id', $filterPosition);
        if ($filterRank) $sql->where('employee_positions.rank_id', $filterRank);
        if ($filterGrade) $sql->where('employee_positions.grade_id', $filterGrade);
        if ($filterLocation) $sql->where('employee_positions.location_id', $filterLocation);
        if ($filter) $sql->where(function ($query) use ($filter) {
            $query->where('employee_families.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.employee_number', 'like', '%' . $filter . '%');
        });

        $data = [];
        $families = $sql->get();
        foreach ($families as $k => $family) {
            $data[] = [
                $k + 1,
                $family->employee_number." ",
                $family->employee_name,
                $family->name,
                $family->relationship->name,
                $family->identity_number,
                $family->birth_date ? setDate($family->birth_date) : '',
                $family->birth_place,
                $family->gender == 'm' ? 'Laki-Laki' : 'Pria',
                $family->description,
            ];
        }

        $columns = ["no", "data pegawai" => ["nip", "nama"], "data keluarga" => ["nama", "hubungan", "nomor ktp", "tanggal lahir", "tempat lahir", "gender", "keterangan"]];

        $widths = [10, 20, 30, 30, 20, 20, 20, 20, 20, 50];

        $aligns = ['center', 'center', 'left', 'left', 'left', 'left', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Keluarga',
            ]
        ), 'Data Keluarga.xlsx');
    }
}
