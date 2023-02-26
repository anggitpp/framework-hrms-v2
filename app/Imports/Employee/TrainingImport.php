<?php

namespace App\Imports\Employee;

use App\Http\Requests\Employee\EmployeeTrainingRequest;
use App\Services\Employee\EmployeeService;
use App\Services\Employee\EmployeeTrainingService;
use App\Services\Setting\AppMasterDataService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Storage;

class TrainingImport implements ToModel, WithEvents
{
    public array $categories;
    public array $types;
    public array $employees;
    public string $logname;
    private EmployeeTrainingService $employeeTrainingService;
    private EmployeeService $employeeService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "training-import_$today.log";

        $this->employeeTrainingService = new EmployeeTrainingService();
        $this->employeeService = new EmployeeService();
        $this->appMasterDataService = new AppMasterDataService();

        $this->employees = $this->employeeService->getEmployees()->pluck('employees.id', 'employee_number')->toArray();
        $this->categories = $this->appMasterDataService->getMasterForArray('EKPL', 0, 'order', true);
        $this->types = $this->appMasterDataService->getMasterForArray('ETPL', 0, 'order', true);
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
        $subject = trim($row[3]);
        $institution = trim($row[4]);
        $certificate_number = trim($row[5]);
        $category_id = trim($row[6]);
        $type_id = trim($row[7]);
        $start_date = trim($row[8]);
        $start_date_convert = !empty($start_date) ? substr($start_date, -5, 1) == '/' ? $start_date : Date::excelToDateTimeObject($start_date)->format('d/m/Y') : '';
        $end_date = trim($row[9]);
        $end_date_convert = !empty($end_date) ? substr($end_date, -5, 1) == '/' ? $end_date : Date::excelToDateTimeObject($end_date)->format('d/m/Y') : '';
        $description = trim($row[10]);

        $errors = "";
        try {
            //EMPTY VALIDATION
            if (empty($employee_number)) $errors .= "\n\t-Kolom NIP tidak boleh kosong";
            if (empty($subject)) $errors .= "\n\t-Kolom Perihal tidak boleh kosong";
            if (empty($institution)) $errors .= "\n\t-Kolom Institusi tidak boleh kosong";
            if (empty($start_date)) $errors .= "\n\t-Kolom Tanggal Mulai tidak boleh kosong";
            if (empty($end_date)) $errors .= "\n\t-Kolom Tanggal Selesai tidak boleh kosong";

            //DATE VALIDATION
            if (!empty($start_date) && $start_date_convert == '0000-00-00') $errors .= "\n\t-Kolom tanggal mulai tidak sesuai format $start_date_convert";
            if (!empty($end_date) && $end_date_convert == '0000-00-00') $errors .= "\n\t-Kolom tanggal selesai tidak sesuai format $end_date_convert";

            //MASTER VALIDATION
            if (!array_key_exists($employee_number, $this->employees)) $errors .= "\n\t-Kolom pegawai tidak terdaftar";
            if (!empty($category_id) && !array_key_exists(trim(strtolower($category_id)), $this->categories)) $errors .= "\n\t-Kolom Kategori tidak terdaftar";
            if (!empty($type_id) && !array_key_exists(trim(strtolower($type_id)), $this->types)) $errors .= "\n\t-Kolom Tipe tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if (!empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : " . $errors);
            } else {
                $arrData = [
                    'employee_id' => $this->employees[$employee_number],
                    'subject' => $subject,
                    'institution' => $institution,
                    'certificate_number' => $certificate_number,
                    'category_id' => $this->categories[trim(strtolower($category_id))] ?? 0,
                    'type_id' => $this->types[trim(strtolower($type_id))] ?? 0,
                    'start_date' => $start_date_convert,
                    'end_date' => $end_date_convert,
                    'description' => $description,
                ];

                $idExist = 0;
                $checkExist = $this->employeeTrainingService->getTrainings()->select('employee_trainings.id')->where('employee_trainings.employee_id', $arrData['employee_id'])->where('employee_trainings.subject', $arrData['subject'])->first();
                if ($checkExist) $idExist = $checkExist->id;

                $request = new EmployeeTrainingRequest();
                $request->merge($arrData);

                $this->employeeTrainingService->saveTraining($request, $idExist);

                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} No. {$no} : SUCCESS {$no} {$name}");
            }

        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} No. {$no} : ERROR {$no} {$name} " . $th->getMessage());
        }
    }
}
