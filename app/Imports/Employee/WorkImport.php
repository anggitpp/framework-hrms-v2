<?php

namespace App\Imports\Employee;

use App\Http\Requests\Employee\EmployeeWorkRequest;
use App\Services\Employee\EmployeeService;
use App\Services\Employee\EmployeeWorkService;
use App\Services\Setting\AppMasterDataService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Storage;

class WorkImport implements ToModel, WithEvents
{
    public array $employees;

    public string $logname;
    private EmployeeWorkService $employeeWorkService;
    private EmployeeService $employeeService;
    private AppMasterDataService $appMasterDataService;
    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "work-import_$today.log";
        $this->employeeWorkService = new EmployeeWorkService();
        $this->employeeService = new EmployeeService();
        $this->appMasterDataService = new AppMasterDataService();

        $this->employees = $this->employeeService->getEmployees()->pluck('employees.id', 'employee_number')->toArray();
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function () {
                $storage = Storage::disk('log');
                if($storage->exists($this->logname)){
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
        $company = trim($row[3]);
        $position = trim($row[4]);
        $start_date = trim($row[5]);
        $start_date_convert = !empty($start_date) ? substr($start_date, -5, 1) == '/' ? resetDate($start_date) : Date::excelToDateTimeObject($start_date)->format('Y-m-d') : '';
        $end_date = trim($row[6]);
        $end_date_convert = !empty($end_date) ? substr($end_date, -5, 1) == '/' ? resetDate($end_date) : Date::excelToDateTimeObject($end_date)->format('Y-m-d') : '';
        $city = trim($row[7]);
        $description = trim($row[8]);

        $errors = "";
        try {
            //EMPTY VALIDATION
            if(empty($employee_number)) $errors.="\n\t-Kolom NIP tidak boleh kosong";
            if(empty($company)) $errors.="\n\t-Kolom Perusahaan tidak boleh kosong";
            if(empty($position)) $errors.="\n\t-Kolom Posisi tidak boleh kosong";
            if(empty($start_date)) $errors.="\n\t-Kolom Tanggal Mulai tidak boleh kosong";

            //DATE VALIDATION
            if(!empty($start_date) && $start_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal mulai tidak sesuai format $start_date_convert";
            if(!empty($end_date) && $end_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal selesai tidak sesuai format $end_date_convert";

            //MASTER VALIDATION
            if(!empty($employee_number) && empty($this->employees[$employee_number])) $errors.="\n\t-Kolom pegawai tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if(!empty($errors)){
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : ".$errors);
            } else {
                $arrData = [
                    'employee_id' => $this->employees[$employee_number],
                    'company' => $company,
                    'position' => $position,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'city' => $city,
                    'description' => $description,
                ];

                $idExist = 0;
                $checkExist = $this->employeeWorkService->getWorks()->select('employee_works.id')->where('employee_works.employee_id', $arrData['employee_id'])->where('employee_works.company', $arrData['company'])->first();
                if ($checkExist) $idExist = $checkExist->id;

                $request = new EmployeeWorkRequest();
                $request->merge($arrData);

                $this->employeeWorkService->saveWork($request, $idExist);

                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} No. {$no} : SUCCESS {$no} {$name}");
            }

        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} No. {$no} : ERROR {$no} {$name} ".$th->getMessage());
        }
    }
}
