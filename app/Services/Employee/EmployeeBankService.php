<?php
namespace App\Services\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeBankRequest;
use App\Repositories\Employee\EmployeeBankRepository;
use App\Models\Employee\EmployeeBank;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;

class EmployeeBankService extends Controller
{
    private EmployeeBankRepository $employeeBankRepository;
    public function __construct()
    {
        $this->employeeBankRepository = new EmployeeBankRepository(
            new EmployeeBank()
        );
    }

    public function getBanks(): Builder
    {

        $query = $this->employeeBankRepository->getBanks();
        $user = Auth::user();

        $permission = Permission::where('name', 'lvl3 ' . $this->menu_path())->first();
        if ($permission) {
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path()))
                $query->where('employee_positions.leader_id', $user->employee_id);
        }

        return $query;
    }

    public function getBankById(int $id): EmployeeBank
    {
        return $this->employeeBankRepository->getById($id);
    }

    public function getBanksWithSpecificColumn(array $columns): Builder
    {
        return $this->getBanks()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getBanksWithSpecificColumn([
                'employee_banks.id',
                'employee_banks.bank_id',
                'employee_banks.account_number',
                'employee_banks.account_name',
                'employee_banks.branch',
                'employee_banks.status',
                'employee_number',
                'employees.name as employee_name',
            ]);
            $filter = $request->get('search')['value'];
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $queryFilter = function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation) {
                if (isset($filter)) $query->where(function($query) use ($filter) {
                    $query->where('employees.name', 'like', "%{$filter}%")
                        ->orWhere('employees.employee_number', 'like', "%{$filter}%")
                        ->orWhere('employee_banks.account_name', 'like', "%{$filter}%")
                        ->orWhere('employee_banks.account_number', 'like', "%{$filter}%");
                });
                if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                if (isset($filterRank)) $query->where('rank_id', $filterRank);
                if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                if (isset($filterLocation)) $query->where('location_id', $filterLocation);
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'bank_id', 'type' => 'master_relationship', 'masters' => 'bank'],
                ['name' => 'status', 'type' => 'status'],
            ]);
        }
    }

    public function saveBank(EmployeeBankRequest $request, int $id = 0): void
    {

        $fields = [
            'employee_id' => $request->input('employee_id'),
            'bank_id' => $request->input('bank_id'),
            'account_number' => $request->input('account_number'),
            'account_name' => $request->input('account_name'),
            'branch' => $request->input('branch'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ];

        if ($id == 0)
            $bank = $this->employeeBankRepository->create($fields);
        else
            $bank = $this->employeeBankRepository->update($fields, $id);

        if($request->input('status') == 't')
            $this->getBanks()->where('employee_banks.employee_id', $request->input('employee_id'))
                ->where('employee_banks.id', '!=', $bank->id)
                ->update(['employee_banks.status' => 'f']);
    }

    public function deleteBank(int $id): void
    {
        $this->employeeBankRepository->destroy($id);
    }

    public function exportBank(Request $request)
    {
        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');

        $sql = $this->getBanksWithSpecificColumn(['employee_banks.*', 'employee_number', 'employees.name as employee_name']);
        if ($filterPosition) $sql->where('employee_positions.position_id', $filterPosition);
        if ($filterRank) $sql->where('employee_positions.rank_id', $filterRank);
        if ($filterGrade) $sql->where('employee_positions.grade_id', $filterGrade);
        if ($filterLocation) $sql->where('employee_positions.location_id', $filterLocation);
        if ($filter) $sql->where(function($query) use ($filter) {
            $query->where('employees.name', 'like', "%{$filter}%")
                ->orWhere('employees.employee_number', 'like', "%{$filter}%")
                ->orWhere('employee_banks.account_name', 'like', "%{$filter}%")
                ->orWhere('employee_banks.account_number', 'like', "%{$filter}%");
        });

        $data = [];
        $banks = $sql->get();
        foreach ($banks as $k => $bank) {
            $data[] = [
                $k + 1,
                $bank->employee_number . " ",
                $bank->employee_name,
                $bank->bank->name ?? '',
                $bank->account_number,
                $bank->account_name,
                $bank->branch,
                $bank->description,
                $bank->status == 't' ? 'Aktif' : 'Tidak Aktif',
            ];
        }

        $columns = ["no", "data pegawai" => ["nip", "nama"], "data bank" => ["bank", "nomor rekening", "nama pemilik rekening", "cabang", "keterangan", "status"]];

        $widths = [10, 20, 30, 30];

        $aligns = ['center', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Bank',
            ]
        ), 'Data Bank.xlsx');
    }
}
