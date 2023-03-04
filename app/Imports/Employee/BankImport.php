<?php

namespace App\Imports\Employee;

use App\Http\Requests\Employee\EmployeeAssetRequest;
use App\Http\Requests\Employee\EmployeeBankRequest;
use App\Services\Employee\EmployeeAssetService;
use App\Services\Employee\EmployeeBankService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Storage;

class BankImport implements ToModel, WithEvents
{
    public array $banks;
    public array $employees;

    public string $logname;
    private EmployeeBankService $employeeBankService;
    private EmployeeService $employeeService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "bank-import_$today.log";

        $this->employeeBankService = new EmployeeBankService();
        $this->employeeService = new EmployeeService();
        $this->appMasterDataService = new AppMasterDataService();

        $this->employees = $this->employeeService->getEmployees()->pluck('employees.id', 'employee_number')->toArray();
        $this->banks = $this->appMasterDataService->getMasterForArray('SMB', 0, 'order', true);
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
        $name = trim($row[2]);
        $bank_id = trim($row[3]);
        $account_number = trim($row[4]);
        $account_name = trim($row[5]);
        $branch = trim($row[6]);
        $description = trim($row[7]);
        $status = trim($row[8]) == 'Aktif' ? 't' : 'f';

        $errors = "";
        try {
            //EMPTY VALIDATION
            if (empty($employee_number)) $errors .= "\n\t-Kolom NIP tidak boleh kosong";
            if (empty($bank_id)) $errors .= "\n\t-Kolom Bank tidak boleh kosong";
            if (empty($account_number)) $errors .= "\n\t-Kolom Nomor Rekening tidak boleh kosong";
            if (empty($account_name)) $errors .= "\n\t-Kolom Nama Pemilik Rekening tidak boleh kosong";

            //MASTER VALIDATION
            if (!array_key_exists($employee_number, $this->employees)) $errors .= "\n\t-Kolom pegawai tidak terdaftar";
            if (!empty(!$bank_id) && array_key_exists(trim(strtolower($bank_id)), $this->banks)) $errors .= "\n\t-Kolom Bank tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if (!empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : " . $errors);
            } else {
                $arrData = [
                    'employee_id' => $this->employees[$employee_number],
                    'bank_id' => $this->banks[trim(strtolower($bank_id))] ?? 0,
                    'account_number' => $account_number,
                    'account_name' => $account_name,
                    'branch' => $branch,
                    'description' => $description,
                    'status' => $status,
                ];

                $idExist = 0;
                $checkExist = $this->employeeBankService->getBanks()->select('employee_banks.id')->where('employee_banks.employee_id', $arrData['employee_id'])->where('employee_banks.account_number', $arrData['account_number'])->first();
                if ($checkExist) $idExist = $checkExist->id;

                $request = new EmployeeBankRequest();
                $request->merge($arrData);

                $this->employeeBankService->saveBank($request, $idExist);

                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} No. {$no} : SUCCESS {$no} {$name}");
            }

        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} No. {$no} : ERROR {$no} {$name} " . $th->getMessage());
        }
    }
}
