<?php

namespace App\Imports\Employee;

use App\Http\Requests\Employee\EmployeeEducationRequest;
use App\Services\Employee\EmployeeEducationService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Storage;

class EducationImport implements ToModel, WithEvents
{
    public array $levels;
    public array $employees;
    public string $logname;
    private EmployeeEducationService $employeeEducationService;
    private EmployeeService $employeeService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "education-import_$today.log";
        $this->employeeEducationService = new EmployeeEducationService();
        $this->employeeService = new EmployeeService();
        $this->appMasterDataService = new AppMasterDataService();

        $this->employees = $this->employeeService->getEmployees()->pluck('employees.id', 'employee_number')->toArray();
        $this->levels = $this->appMasterDataService->getMasterForArray('EMJP', 0, 'order', true);
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
        $level_id = trim($row[3]);
        $name = trim($row[4]);
        $major = trim($row[5]);
        $start_year = trim($row[6]);
        $end_year = trim($row[7]);
        $city = trim($row[8]);
        $score = trim($row[9]);
        $description = trim($row[10]);

        $errors = "";
        try {
            //EMPTY VALIDATION
            if (empty($employee_number)) $errors .= "\n\t-Kolom NIP tidak boleh kosong";
            if (empty($name)) $errors .= "\n\t-Kolom nama tidak boleh kosong";
            if (empty($level_id)) $errors .= "\n\t-Kolom hubungan keluarga tidak boleh kosong";

            //MASTER VALIDATION
            if (!array_key_exists($employee_number, $this->employees)) $errors .= "\n\t-Kolom pegawai tidak terdaftar";
            if (!array_key_exists(trim(strtolower($level_id)), $this->levels)) $errors .= "\n\t-Kolom tingkatan pendidikan tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if (!empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : " . $errors);
            } else {
                $arrData = [
                    'employee_id' => $this->employees[$employee_number],
                    'name' => $name,
                    'level_id' => $this->levels[trim(strtolower($level_id))],
                    'major' => $major,
                    'start_year' => $start_year,
                    'end_year' => $end_year,
                    'city' => $city,
                    'score' => $score,
                    'description' => $description,
                ];

                $idExist = 0;
                $checkExist = $this->employeeEducationService->getEducation()->select('employee_education.id')->where('employee_education.employee_id', $arrData['employee_id'])->where('employee_education.level_id', $arrData['level_id'])->first();
                if ($checkExist) $idExist = $checkExist->id;

                $request = new EmployeeEducationRequest();
                $request->merge($arrData);

                $this->employeeEducationService->saveEducation($request, $idExist);

                $storage->append($this->logname, "{$now} No. {$no} : SUCCESS {$no} {$name}");
            }

        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} No. {$no} : ERROR {$no} {$name} " . $th->getMessage());
        }
    }
}
