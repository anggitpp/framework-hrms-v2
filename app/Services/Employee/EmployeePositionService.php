<?php
namespace App\Services\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeePositionHistoryRequest;
use App\Repositories\Employee\EmployeePositionRepository;
use App\Models\Employee\EmployeePosition;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeePositionService extends Controller
{
    private EmployeePositionRepository $employeePositionRepository;
    public function __construct()
    {
        $this->employeePositionRepository = new EmployeePositionRepository(
            new EmployeePosition()
        );
    }

    public function getPositions(): Builder
    {

        $query = $this->employeePositionRepository->getPositions();
        $user = Auth::user();

        $permission = Permission::where('name', 'lvl3 ' . $this->menu_path())->first();
        if ($permission) {
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path()))
                $query->where('employee_positions.leader_id', $user->employee_id);
        }

        return $query;
    }

    public function getPositionById(int $id): EmployeePosition
    {
        return $this->employeePositionRepository->getById($id);
    }

    public function getPositionByEmployeeId(int $id): Collection
    {
        return $this->employeePositionRepository->getPositionsByEmployeeId($id)->get();
    }

    public function getPositionWithSpecificColumn(array $columns, int $employeeId = 0): Builder
    {
        $query = $this->getPositions()->select($columns);
        if ($employeeId != 0) $query->where('employee_positions.employee_id', $employeeId);
        return $query;
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getPositionWithSpecificColumn([
                'employee_positions.id',
                'employee_positions.position_id',
                'employee_positions.rank_id',
                'employee_positions.grade_id',
                'employee_positions.status',
                'employee_positions.sk_number',
                'employee_positions.start_date',
                'employee_positions.end_date',
                'name',
                'employee_number',
                ]);
            $filter = $request->get('search')['value'];
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $queryFilter = function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation) {
                if (isset($filter)) $query->where('employees.name', 'like', "%{$filter}%")->orWhere('employees.employee_number', 'like', "%{$filter}%");
                if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                if (isset($filterRank)) $query->where('rank_id', $filterRank);
                if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                if (isset($filterLocation)) $query->where('location_id', $filterLocation);
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'position_id', 'type' => 'master_relationship', 'masters' => 'position'],
                ['name' => 'rank_id', 'type' => 'master_relationship', 'masters' => 'rank'],
                ['name' => 'grade_id', 'type' => 'master_relationship', 'masters' => 'grade'],
                ['name' => 'status', 'type' => 'status'],
                ['name' => 'period', 'type' => 'custom', 'value' => function ($data) {
                    $endPeriod = $data->end_date ? Carbon::create($data->end_date)->format('M Y') : 'current';
                    return Carbon::create($data->start_date)->format('M Y')." - ".$endPeriod;
                }],
            ], false, false, '', [], '', false, false);
        }
    }

    public function savePosition(EmployeePositionHistoryRequest $request, int $id = 0): void
    {
        $fields = [
            'employee_id' => $request->input('employee_id'),
            'employee_type_id' => $request->input('employee_type_id'),
            'position_id' => $request->input('position_id'),
            'location_id' => $request->input('location_id'),
            'rank_id' => $request->input('rank_id'),
            'grade_id' => $request->input('grade_id'),
            'sk_number' => $request->input('sk_number'),
            'sk_date' => $request->input('sk_date') ? resetDate($request->input('sk_date')) : null,
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => $request->input('end_date') ? resetDate($request->input('end_date')) : null,
            'unit_id' => $request->input('unit_id'),
            'shift_id' => $request->input('shift_id'),
            'leader_id' => $request->input('leader_id'),
            'payroll_master_id' => $request->input('payroll_master_id'),
            'status' => $request->input('status'),
        ];

        if ($id == 0)
            $position = $this->employeePositionRepository->create($fields);
        else
            $position = $this->employeePositionRepository->update($fields, $id);

        if($request->input('status') == 't')
            $this->getPositions()->where('employee_id', $request->input('employee_id'))
                ->where('employee_positions.id', '!=', $position->id)
                ->update(['employee_positions.status' => 'f']);

    }

    public function deletePosition(int $id): void
    {
        $this->employeePositionRepository->getById($id)->delete();
    }

    public function exportPosition(Request $request): BinaryFileResponse
    {
        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');

        $sql = $this->getPositionWithSpecificColumn(['employee_positions.*', 'employee_number', 'employees.name as employee_name']);
        if ($filterPosition) $sql->where('employee_positions.position_id', $filterPosition);
        if ($filterRank) $sql->where('employee_positions.rank_id', $filterRank);
        if ($filterGrade) $sql->where('employee_positions.grade_id', $filterGrade);
        if ($filterLocation) $sql->where('employee_positions.location_id', $filterLocation);
        if ($filter) $sql->where(function ($query) use ($filter) {
            $query->where('employee_positions.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.employee_number', 'like', '%' . $filter . '%');
        });

        $data = [];
        $positions = $sql->get();
        foreach ($positions as $k => $position){
            $data[] = [
                $k + 1,
                $position->employee_number." ",
                $position->employee_name,
                $position->employeeType->name,
                $position->position->name,
                $position->location->name,
                $position->rank->name,
                $position->grade?->name,
                $position->sk_number,
                $position->sk_date ? setDate($position->sk_date) : '',
                $position->start_date ? setDate($position->start_date) : '',
                $position->end_date ? setDate($position->end_date) : '',
                $position->unit->name,
                $position->shift?->name,
                $position->leader?->name,
                $position->status == 't' ? 'Aktif' : 'Tidak Aktif',
            ];
        }

        $columns = ["no", "data pegawai" => ["nip", "nama"], "data posisi" => ["tipe pegawai", "jabatan", "lokasi kerja", "pangkat", "grade", "nomor sk", "tanggal sk", "tanggal mulai", "tanggal selesai", "unit", "shift", "atasan langsung", "status"]];

        $widths = [10, 20, 30, 20, 30];

        $aligns = ['center', 'center', 'left', 'left', 'left', 'left', 'left', 'left', 'center', 'center', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Riwayat Jabatan',
            ]
        ), 'Data Riwayat Jabatan.xlsx');
    }
}
