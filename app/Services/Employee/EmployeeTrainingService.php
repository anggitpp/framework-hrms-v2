<?php

namespace App\Services\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeTrainingRequest;
use App\Repositories\Employee\EmployeeTrainingRepository;
use App\Models\Employee\EmployeeTraining;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeTrainingService extends Controller
{
    private EmployeeTrainingRepository $employeeTrainingRepository;
    private string $filePath;

    public function __construct()
    {
        $this->employeeTrainingRepository = new EmployeeTrainingRepository(
            new EmployeeTraining()
        );
        $this->filePath = '/uploads/employee/training/';
    }

    public function getTrainings(): Builder
    {

        $query = $this->employeeTrainingRepository->getTrainings();
        $user = Auth::user();

        $permission = Permission::where('name', 'lvl3 ' . $this->menu_path())->first();
        if ($permission) {
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path()))
                $query->where('employee_positions.leader_id', $user->employee_id);
        }

        return $query;
    }

    public function getTrainingById(int $id): EmployeeTraining
    {
        return $this->employeeTrainingRepository->getById($id);
    }

    public function getTrainingWithSpecificColumn(array $columns): Builder
    {
        return $this->getTrainings()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request, bool $isModal = false): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getTrainingWithSpecificColumn([
                'employee_trainings.id',
                'employee_trainings.institution',
                'employee_trainings.subject',
                'employee_trainings.certificate_number',
                'employee_trainings.type_id',
                'employee_trainings.filename',
                'employee_trainings.start_date',
                'employee_trainings.end_date',
                'employee_number',
                'employees.name as employee_name']);
            $filter = $request->get('search')['value'];
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $queryFilter = function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation) {
                if (isset($filter)) $query->where('employees.name', 'like', "%{$filter}%")->orWhere('employee_trainings.name', 'like', "%{$filter}%");
                if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                if (isset($filterRank)) $query->where('rank_id', $filterRank);
                if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                if (isset($filterLocation)) $query->where('location_id', $filterLocation);
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'type_id', 'type' => 'master_relationship', 'masters' => 'type'],
                ['name' => 'start_date', 'type' => 'date'],
                ['name' => 'end_date', 'type' => 'date'],
                ['name' => 'filename', 'type' => 'filename']
            ], $isModal);
        }
    }

    public function saveTraining(EmployeeTrainingRequest $request, int $id = 0): void
    {

        $fields = [
            'employee_id' => $request->input('employee_id'),
            'certificate_number' => $request->input('certificate_number'),
            'subject' => $request->input('subject'),
            'institution' => $request->input('institution'),
            'category_id' => $request->input('category_id'),
            'type_id' => $request->input('type_id'),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($request->input('end_date')),
            'description' => $request->input('description'),
        ];

        if ($id == 0)
            $training = $this->employeeTrainingRepository->create($fields);
        else
            $training = $this->employeeTrainingRepository->update($fields, $id);

        defaultUploadFile($training, $request, $this->filePath, 'employee-training_' . Str::slug($request->input('number')) . '_' . time());
    }

    public function deleteTraining(int $id): void
    {
        $training = $this->employeeTrainingRepository->getById($id);

        if($training->filename != null) deleteFile($training->filename);

        $training->delete();
    }

    public function exportTraining(Request $request): BinaryFileResponse
    {
        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');

        $sql = $this->getTrainingWithSpecificColumn(['employee_trainings.*', 'employee_number', 'employees.name as employee_name']);
        if ($filterPosition) $sql->where('employee_positions.position_id', $filterPosition);
        if ($filterRank) $sql->where('employee_positions.rank_id', $filterRank);
        if ($filterGrade) $sql->where('employee_positions.grade_id', $filterGrade);
        if ($filterLocation) $sql->where('employee_positions.location_id', $filterLocation);
        if ($filter) $sql->where(function ($query) use ($filter) {
            $query->where('employee_trainings.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.employee_number', 'like', '%' . $filter . '%');
        });

        $data = [];
        $trainings = $sql->get();
        foreach ($trainings as $k => $training){
            $data[] = [
                $k + 1,
                $training->employee_number." ",
                $training->employee_name,
                $training->subject,
                $training->institution,
                $training->certificate_number,
                $training->category?->name,
                $training->type?->name,
                setDate($training->start_date),
                setDate($training->end_date),
                $training->description,
            ];
        }

        $columns = ["no", "data pegawai" => ["nip", "nama"], "data pelatihan" => ["perihal", "institusi", "no. sertifikat", "kategori", "tipe", "tanggal mulai", "tanggal selesai", "description"]];

        $widths = [10, 20, 30, 30, 30, 30, 20, 20, 20, 20, 50];

        $aligns = ['center', 'center', 'left', 'left', 'left', 'left', 'left', 'left', 'center', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Pelatihan',
            ]
        ), 'Data Pelatihan.xlsx');
    }
}
