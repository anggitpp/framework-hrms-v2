<?php

namespace App\Services\Employee;

use App\Exports\GlobalExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeContactRequest;
use App\Repositories\Employee\EmployeeContactRepository;
use App\Models\Employee\EmployeeContact;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;

class EmployeeContactService extends Controller
{
    private EmployeeContactRepository $employeeContactRepository;

    public function __construct()
    {
        $this->employeeContactRepository = new EmployeeContactRepository(
            new EmployeeContact()
        );
    }

    public function getContacts(): Builder
    {

        $query = $this->employeeContactRepository->getContacts();
        $user = Auth::user();

        $permission = Permission::findByName('lvl3 ' . $this->menu_path());
        if (!empty($permission))
            if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path()))
                $query->where('employee_positions.leader_id', $user->employee_id);

        return $query;
    }

    public function getContactById(int $id): EmployeeContact
    {
        return $this->employeeContactRepository->getById($id);
    }

    public function getContactsWithSpecificColumn(array $columns): Builder
    {
        return $this->getContacts()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $query = $this->getContactsWithSpecificColumn(['employee_contacts.id', 'employee_contacts.name', 'employee_contacts.phone_number', 'employee_number', 'employees.name as employee_name', 'relationship_id']);
            $filter = $request->get('search')['value'];
            $filterPosition = $request->get('combo_1');
            $filterRank = $request->get('combo_2');
            $filterGrade = $request->get('combo_3');
            $filterLocation = $request->get('combo_4');
            $queryFilter = function ($query) use ($filter, $filterGrade, $filterPosition, $filterRank, $filterLocation) {
                if (isset($filter)) $query->where('employees.name', 'like', "%{$filter}%")->orWhere('employee_contacts.name', 'like', "%{$filter}%");
                if (isset($filterPosition)) $query->where('position_id', $filterPosition);
                if (isset($filterRank)) $query->where('rank_id', $filterRank);
                if (isset($filterGrade)) $query->where('grade_id', $filterGrade);
                if (isset($filterLocation)) $query->where('location_id', $filterLocation);
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'relationship_id', 'type' => 'master_relationship', 'masters' => 'relationship'],
            ]);
        }
    }

    public function saveContact(EmployeeContactRequest $request, int $id = 0): void
    {

        $fields = [
            'employee_id' => $request->input('employee_id'),
            'name' => $request->input('name'),
            'relationship_id' => $request->input('relationship_id'),
            'phone_number' => $request->input('phone_number'),
            'description' => $request->input('description'),
        ];

        if ($id == 0)
            $this->employeeContactRepository->create($fields);
        else
            $this->employeeContactRepository->update($fields, $id);
    }

    public function deleteContact(int $id): void
    {
        $this->employeeContactRepository->destroy($id);
    }

    public function exportContact(Request $request)
    {
        $filterPosition = $request->get('combo_1');
        $filterRank = $request->get('combo_2');
        $filterGrade = $request->get('combo_3');
        $filterLocation = $request->get('combo_4');
        $filter = $request->get('filter');

        $sql = $this->getContactsWithSpecificColumn(['employee_contacts.id', 'employee_contacts.name', 'employee_contacts.phone_number', 'employee_number', 'employees.name as employee_name', 'relationship_id']);
        if ($filterPosition) $sql->where('employee_positions.position_id', $filterPosition);
        if ($filterRank) $sql->where('employee_positions.rank_id', $filterRank);
        if ($filterGrade) $sql->where('employee_positions.grade_id', $filterGrade);
        if ($filterLocation) $sql->where('employee_positions.location_id', $filterLocation);
        if ($filter) $sql->where(function ($query) use ($filter) {
            $query->where('employee_contacts.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.name', 'like', '%' . $filter . '%')
                ->orWhere('employees.employee_number', 'like', '%' . $filter . '%');
        });

        $data = [];
        $contacts = $sql->get();
        foreach ($contacts as $k => $contact) {
            $data[] = [
                $k + 1,
                $contact->employee_number . " ",
                $contact->employee_name,
                $contact->name,
                $contact->relationship->name ?? '',
                $contact->phone_number,
            ];
        }

        $columns = ["no", "data pegawai" => ["nip", "nama"], "data kontak" => ["nama", "hubungan", "nomor HP"]];

        $widths = [10, 20, 30, 30];

        $aligns = ['center', 'center'];

        return Excel::download(new GlobalExport(
            [
                'columns' => $columns,
                'widths' => $widths,
                'aligns' => $aligns,
                'data' => $data,
                'title' => 'Data Kontak Darurat',
            ]
        ), 'Data Kontak Darurat.xlsx');
    }
}
