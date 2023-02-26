<?php

namespace App\Imports\Employee;

use App\Http\Requests\Employee\EmployeeContactRequest;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeContact;
use App\Models\Setting\AppMasterData;
use App\Services\Employee\EmployeeContactService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Storage;

class ContactImport implements ToModel, WithEvents
{
    public array $employees;
    public array $relationships;
    public string $logname;
    private EmployeeContactService $employeeContactService;
    private EmployeeService $employeeService;
    private AppMasterDataService $appMasterDataService;
    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "contact-import $today.log";
        $this->employeeContactService = new EmployeeContactService();
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

        $no = trim($row[0]);
        $employee_number = trim($row[1]);
        $name = trim($row[3]);
        $relationship = trim($row[4]);
        $phone_number = trim($row[5]);

        $errors = "";
        try {
            //VALIDATION EMPTY
            if(empty($name)) $errors.="\n\t-Kolom nama kontak tidak boleh kosong";
            if(empty($relationship)) $errors.="\n\t-Kolom hubungan tidak boleh kosong";
            if(empty($employee_number)) $errors.="\n\t-Kolom nomor pegawai tidak boleh kosong";

            //VALIDATION MASTER
            if(!array_key_exists($employee_number, $this->employees)) $errors.="\n\t-Kolom pegawai tidak terdaftar";
            if(!array_key_exists(trim(strtolower($relationship)), $this->relationships)) $errors.="\n\t-Kolom hubungan tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if(!empty($errors)){
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : ".$errors);
            } else {

                $arrData = [
                    'employee_id' => $this->employees[$employee_number],
                    'name' => $name,
                    'relationship_id' => $this->relationships[trim(strtolower($relationship))],
                    'phone_number' => $phone_number,
                ];

                $idExist = 0;
                $checkExist = $this->employeeContactService->getContacts()->select('employee_contacts.id')->where('employee_contacts.employee_id', $arrData['employee_id'])->where('employee_contacts.name', $arrData['name'])->first();
                if($checkExist) $idExist = $checkExist->id;

                $request = new EmployeeContactRequest();
                $request->merge($arrData);

                $this->employeeContactService->saveContact($request, $idExist);

                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} SUCESS {$no} {$name}");
            }
        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} ERROR {$no} {$name} {$th->getMessage()}");
        }
    }
}
