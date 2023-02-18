<?php

namespace App\Services\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeEducationRequest;
use App\Repositories\Employee\EmployeeEducationRepository;
use App\Models\Employee\EmployeeEducation;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeEducationService extends Controller
{
    private EmployeeEducationRepository $employeeEducationRepository;
    private string $filePath;

    public function __construct()
    {
        $this->employeeEducationRepository = new EmployeeEducationRepository(
            new EmployeeEducation()
        );
        $this->filePath = '/uploads/employee/education/';
    }

    public function getEducation(): Builder
    {

        $query = $this->employeeEducationRepository->getEducation();
        $user = Auth::user();

        $permission = Permission::findByName('lvl3 ' . $this->menu_path());
        if (!empty($permission))
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path()))
                $query->where('employee_positions.leader_id', $user->employee_id);

        return $query;
    }

    public function getEducationById(int $id): EmployeeEducation
    {
        return $this->employeeEducationRepository->getById($id);
    }

    public function getEducationWithSpecificColumn(array $columns): Builder
    {
        return $this->getEducation()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getEducationWithSpecificColumn([
                'employee_education.id',
                'employee_education.name',
                'employee_education.level_id',
                'employee_education.major',
                'employee_education.start_year',
                'employee_education.end_year',
                'employee_number',
                'employees.name as employee_name']);
            $filter = $request->get('search')['value'];
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $queryFilter = function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation) {
                if (isset($filter)) $query->where('employees.name', 'like', "%{$filter}%")->orWhere('employee_education.name', 'like', "%{$filter}%");
                if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                if (isset($filterRank)) $query->where('rank_id', $filterRank);
                if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                if (isset($filterLocation)) $query->where('location_id', $filterLocation);
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'level_id', 'type' => 'master_relationship', 'masters' => 'level'],
            ]);
        }
    }

    public function saveEducation(EmployeeEducationRequest $request, int $id = 0): void
    {

        $fields = [
            'employee_id' => $request->input('employee_id'),
            'name' => $request->input('name'),
            'level_id' => $request->input('level_id'),
            'major' => $request->input('major'),
            'start_year' => $request->input('start_year'),
            'end_year' => $request->input('end_year'),
            'city' => $request->input('city'),
            'score' => $request->input('score'),
            'description' => $request->input('description'),
        ];

        if ($id == 0)
            $education = $this->employeeEducationRepository->create($fields);
        else
            $education = $this->employeeEducationRepository->update($fields, $id);

        defaultUploadFile($education, $request, $this->filePath, 'employee-education' . Str::slug($request->input('name')) . '_' . time());
    }

    public function deleteEducation(int $id): void
    {
        $this->employeeEducationRepository->destroy($id);
    }

    public function exportEducation(Request $request): BinaryFileResponse
    {
        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');

        $sql = $this->getEducationWithSpecificColumn(['employee_education.*', 'employee_number', 'employees.name as employee_name']);
        if ($filterPosition) $sql->where('employee_positions.position_id', $filterPosition);
        if ($filterRank) $sql->where('employee_positions.rank_id', $filterRank);
        if ($filterGrade) $sql->where('employee_positions.grade_id', $filterGrade);
        if ($filterLocation) $sql->where('employee_positions.location_id', $filterLocation);
        if ($filter) $sql->where(function ($query) use ($filter) {
            $query->where('employee_education.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.employee_number', 'like', '%' . $filter . '%');
        });

        $data = [];
        $educations = $sql->get();
        foreach ($educations as $k => $education){
            $data[] = [
                $k + 1,
                $education->employee_number." ",
                $education->employee_name,
                $education->level->name,
                $education->name,
                $education->major,
                $education->start_year,
                $education->end_year,
                $education->city,
                $education->score,
                $education->description,
            ];
        }

        $columns = ["no", "data pegawai" => ["nip", "nama"], "data pendidikan" => ["tingkatan", "nama institusi", "jurusan", "mulai", "selesai", "kota", "nilai", "keterangan"]];

        $widths = [10, 20, 30, 20, 30];

        $aligns = ['center', 'center', 'left', 'left', 'left', 'left', 'center', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Pendidikan',
            ]
        ), 'Data Pendidikan.xlsx');
    }
}
