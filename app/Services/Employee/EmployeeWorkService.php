<?php
namespace App\Services\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeWorkRequest;
use App\Repositories\Employee\EmployeeWorkRepository;
use App\Models\Employee\EmployeeWork;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeWorkService extends Controller
{
    private EmployeeWorkRepository $employeeWorkRepository;
    private string $filePath;
    public function __construct()
    {
        $this->employeeWorkRepository = new EmployeeWorkRepository(
            new EmployeeWork()
        );
        $this->filePath = '/uploads/employee/work/';
    }

    public function getWorks(): Builder
    {

        $query = $this->employeeWorkRepository->getWorks();
        $user = Auth::user();

        $permission = Permission::findByName('lvl3 ' . $this->menu_path());
        if (!empty($permission))
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path()))
                $query->where('employee_positions.leader_id', $user->employee_id);

        return $query;
    }

    public function getWorkById(int $id): EmployeeWork
    {
        return $this->employeeWorkRepository->getById($id);
    }

    public function getWorkWithSpecificColumn(array $columns): Builder
    {
        return $this->getWorks()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getWorkWithSpecificColumn([
                'employee_works.id',
                'employee_works.company',
                'employee_works.position',
                'employee_works.start_date',
                'employee_works.end_date',
                'employee_works.filename',
                'employee_number',
                'employees.name as employee_name']);
            $filter = $request->get('search')['value'];
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $queryFilter = function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation) {
                if (isset($filter)) $query->where('employees.name', 'like', "%{$filter}%")->orWhere('employee_works.company', 'like', "%{$filter}%");
                if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                if (isset($filterRank)) $query->where('rank_id', $filterRank);
                if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                if (isset($filterLocation)) $query->where('location_id', $filterLocation);
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'start_date', 'type' => 'date'],
                ['name' => 'end_date', 'type' => 'date'],
                ['name' => 'filename', 'type' => 'filename' ]
            ]);
        }
    }

    public function saveWork(EmployeeWorkRequest $request, int $id = 0): void
    {

        $fields = [
            'employee_id' => $request->input('employee_id'),
            'position' => $request->input('position'),
            'company' => $request->input('company'),
            'city' => $request->input('city'),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : '',
            'description' => $request->input('description'),
        ];

        if ($id == 0)
            $work = $this->employeeWorkRepository->create($fields);
        else
            $work = $this->employeeWorkRepository->update($fields, $id);

        defaultUploadFile($work, $request, $this->filePath, 'employee-work_' . Str::slug($request->input('company')) . '_' . time());
    }

    public function deleteWork(int $id): void
    {
        $work = $this->employeeWorkRepository->getById($id);

        if($work->filename != null) deleteFile($work->filename);

        $work->delete();
    }

    public function exportWork(Request $request): BinaryFileResponse
    {
        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');

        $sql = $this->getWorkWithSpecificColumn(['employee_works.*', 'employee_number', 'employees.name as employee_name']);
        if ($filterPosition) $sql->where('employee_positions.position_id', $filterPosition);
        if ($filterRank) $sql->where('employee_positions.rank_id', $filterRank);
        if ($filterGrade) $sql->where('employee_positions.grade_id', $filterGrade);
        if ($filterLocation) $sql->where('employee_positions.location_id', $filterLocation);
        if ($filter) $sql->where(function ($query) use ($filter) {
            $query->where('employee_works.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.employee_number', 'like', '%' . $filter . '%');
        });

        $data = [];
        $works = $sql->get();
        foreach ($works as $k => $work){
            $data[] = [
                $k + 1,
                $work->employee_number." ",
                $work->employee_name,
                $work->company,
                $work->position,
                setDate($work->start_date),
                $work->end_date ? setDate($work->end_date) : '',
                $work->city,
                $work->job_desc,
            ];
        }

        $columns = ["no", "data pegawai" => ["nip", "nama"], "data riwayat kerja" => ["perusahaan", "posisi", "tanggal mulai", "tanggal selesai", "kota", "keterangan"]];

        $widths = [10, 20, 30, 30, 30, 20, 20, 20, 50];

        $aligns = ['center', 'center', 'left', 'left', 'left', 'center', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Riwayat Kerja',
            ]
        ), 'Data Riwayat Kerja.xlsx');
    }
}
