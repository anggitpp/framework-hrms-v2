<?php

namespace App\Imports\Employee;

use App\Http\Requests\Employee\EmployeeFamilyRequest;
use App\Services\Employee\EmployeeFamilyService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Storage;

class FamilyImport implements ToModel, WithEvents
{
    public array $relationships;
    public array $employees;
    private EmployeeFamilyService $employeeFamilyService;
    private EmployeeService $employeeService;
    private AppMasterDataService $appMasterDataService;
    public string $logname;

    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "family-import_$today.log";
        $this->employeeFamilyService = new EmployeeFamilyService();
        $this->employeeService = new EmployeeService();
        $this->appMasterDataService = new AppMasterDataService();

        $this->employees = $this->employeeService->getEmployees()->pluck('employees.id', 'employee_number')->toArray();
        $this->relationships = $this->appMasterDataService->getMasterForArray('EHK', 0, 'order', true);
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function () {
                $storage = Storage::disk('log');
                if ($storage->exists($this->logname)) {
                    $storage->delete($this->logname);
                }
                $storage->put($this->logname, '');
                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} START");
            },
            AfterImport::class => function () {
                $storage = Storage::disk('log');
                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} FINISH");
            },
        ];
    }

    /**
     * @param array $row
     *
     * @return void|null
     */
    public function model(array $row)
    {
        if (!is_numeric($row[0])) {
            return null;
        }

        $storage = Storage::disk("log");

        //IDENTITY
        $no = trim($row[0]);
        $employee_number = trim($row[1]);
        $name = trim($row[3]);
        $relationship_id = trim($row[4]);
        $identity_number = trim($row[5]);
        $birth_date = trim($row[6]);
        $birth_date_convert = !empty($birth_date) ? substr($birth_date, -5, 1) == '/' ? $birth_date : Date::excelToDateTimeObject($birth_date)->format('d/m/Y') : '';
        $birth_place = trim($row[7]);
        $gender = trim($row[8]);
        $description = trim($row[9]);

        $errors = "";
        try {
            //EMPTY VALIDATION
            if (empty($employee_number)) $errors .= "\n\t-Kolom NIP tidak boleh kosong";
            if (empty($name)) $errors .= "\n\t-Kolom nama tidak boleh kosong";
            if (empty($relationship_id)) $errors .= "\n\t-Kolom hubungan keluarga tidak boleh kosong";

            //DATE VALIDATION
            if (!empty($birth_date) && $birth_date_convert == '0000-00-00') $errors .= "\n\t-Kolom tanggal lahir tidak sesuai format $birth_date_convert";

            //MASTER VALIDATION
            if (!array_key_exists($employee_number, $this->employees)) $errors .= "\n\t-Kolom pegawai tidak terdaftar";
            if (!array_key_exists(trim(strtolower($relationship_id)), $this->relationships)) $errors .= "\n\t-Kolom hubungan tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if (!empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : " . $errors);
            } else {
                $arrData = [
                    'employee_id' => $this->employees[$employee_number],
                    'name' => $name,
                    'relationship_id' => $this->relationships[trim(strtolower($relationship_id))],
                    'identity_number' => $identity_number,
                    'birth_date' => $birth_date_convert,
                    'birth_place' => $birth_place,
                    'gender' => $gender,
                    'description' => $description,
                ];

                $idExist = 0;
                $checkExist = $this->employeeFamilyService->getFamilies()->select('employee_families.id')->where('employee_families.employee_id', $arrData['employee_id'])->where('employee_families.name', $arrData['name'])->first();
                if ($checkExist) $idExist = $checkExist->id;

                $request = new EmployeeFamilyRequest();
                $request->merge($arrData);

                $this->employeeFamilyService->saveFamily($request, $idExist);

                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} No. {$no} : SUCCESS {$no} {$name}");
            }

        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} No. {$no} : ERROR {$no} {$name} " . $th->getMessage());
        }
    }
}
