<?php

namespace App\Imports\Payroll;

use App\Models\Attendance\AttendanceShift;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeEducation;
use App\Models\Employee\EmployeeFamily;
use App\Models\Employee\EmployeePosition;
use App\Models\Employee\EmployeeTraining;
use App\Models\Setting\AppMasterData;
use App\Services\Payroll\PayrollUploadService;
use DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Storage;

class UploadImport implements ToModel, WithEvents
{
    public array $employees;

    private PayrollUploadService $payrollUploadService;
    public string $logname;
    private string $code;
    private int $componentId;
    public function __construct($code, $componentId)
    {
        $today = now()->format('Y-m-d');
        $this->logname = "upload-import_$today.log";
        $this->employees = Employee::pluck('id', 'employee_number')->toArray();
        $this->payrollUploadService = new PayrollUploadService();
        $this->code = $code;
        $this->componentId = $componentId;
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

        $no = trim($row[0]);
        $year = trim($row[1]);
        $month = trim($row[2]);
        $employee_id = trim($row[3]);
        $name = trim($row[4]);
        $amount = trim($row[5]);

        $errors = "";
        try {
            //EMPTY VALIDATION
            if(empty($employee_id)) $errors.="\n\t-Kolom NIP tidak boleh kosong";
            if(empty($year)) $errors.="\n\t-Kolom Tahun tidak boleh kosong";
            if(empty($month)) $errors.="\n\t-Kolom Bulan tidak boleh kosong";
            if(empty($amount)) $errors.="\n\t-Kolom Nilai tidak boleh kosong";

            //MASTER VALIDATION
            if(!empty($employee_id) && empty($this->employees[$employee_id])) $errors.="\n\t-Kolom pegawai tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if(!empty($errors)){
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : ".$errors);
            } else {
                $employee_id = $this->employees[$employee_id] ?? 0;

                $datas = [
                    'code' => $this->code,
                    'component_id' => $this->componentId,
                    'employee_id' => $employee_id,
                    'year' => $year,
                    'month' => $month,
                    'amount' => $amount,
                ];

                $this->payrollUploadService->savePayrollUpload($datas);

                $storage->append($this->logname, "{$now} No. {$no} : SUCCESS {$no} {$name}");
            }

        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} No. {$no} : ERROR {$no} {$name} ".$th->getMessage());
        }
    }
}
