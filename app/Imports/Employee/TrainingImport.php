<?php

namespace App\Imports\Employee;

use App\Models\Attendance\AttendanceShift;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeEducation;
use App\Models\Employee\EmployeeFamily;
use App\Models\Employee\EmployeePosition;
use App\Models\Employee\EmployeeTraining;
use App\Models\Setting\AppMasterData;
use DB;
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
    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "training-import_$today.log";
        $this->categories = AppMasterData::whereAppMasterCategoryCode('EKPL')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->types = AppMasterData::whereAppMasterCategoryCode('ETPL')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->employees = Employee::pluck('id', 'employee_number')->toArray();
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
        $employee_id = trim($row[1]);
        $name = trim($row[2]);
        $subject = trim($row[3]);
        $institution = trim($row[4]);
        $certificate_number = trim($row[5]);
        $category_id = trim($row[6]);
        $type_id = trim($row[7]);
        $start_date = trim($row[8]);
        $start_date_convert = !empty($start_date) ? substr($start_date, -5, 1) == '/' ? resetDate($start_date) : Date::excelToDateTimeObject($start_date)->format('Y-m-d') : '';
        $end_date = trim($row[9]);
        $end_date_convert = !empty($end_date) ? substr($end_date, -5, 1) == '/' ? resetDate($end_date) : Date::excelToDateTimeObject($end_date)->format('Y-m-d') : '';
        $description = trim($row[10]);

        $errors = "";
        try {
            //EMPTY VALIDATION
            if(empty($employee_id)) $errors.="\n\t-Kolom NIP tidak boleh kosong";
            if(empty($subject)) $errors.="\n\t-Kolom Perihal tidak boleh kosong";
            if(empty($institution)) $errors.="\n\t-Kolom Institusi tidak boleh kosong";
            if(empty($start_date)) $errors.="\n\t-Kolom Tanggal Mulai tidak boleh kosong";
            if(empty($end_date)) $errors.="\n\t-Kolom Tanggal Selesai tidak boleh kosong";

            //DATE VALIDATION
            if(!empty($start_date) && $start_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal sk tidak sesuai format $start_date_convert";
            if(!empty($end_date) && $end_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal sk tidak sesuai format $end_date_convert";

            //MASTER VALIDATION
            if(!empty($employee_id) && empty($this->employees[$employee_id])) $errors.="\n\t-Kolom pegawai tidak terdaftar";
            if(!empty($category_id) && empty($this->categories[strtolower($category_id)])) $errors.="\n\t-Kolom Kategori tidak terdaftar";
            if(!empty($type_id) && empty($this->types[strtolower($type_id)])) $errors.="\n\t-Kolom Tipe tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if(!empty($errors)){
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : ".$errors);
            } else {
                $employee_id = $this->employees[$employee_id] ?? 0;
                $category_id = $this->categories[strtolower($category_id)] ?? 0;
                $type_id = $this->types[strtolower($type_id)] ?? 0;

                EmployeeTraining::updateOrCreate([
                    'employee_id' => $employee_id,
                    'subject' => $subject,
                ],[
                    'institution' => $institution,
                    'certificate_number' => $certificate_number,
                    'category_id' => $category_id,
                    'type_id' => $type_id,
                    'start_date' => $start_date_convert,
                    'end_date' => $end_date_convert,
                    'description' => $description,
                ]);

                $storage->append($this->logname, "{$now} No. {$no} : SUCCESS {$no} {$name}");
            }

        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} No. {$no} : ERROR {$no} {$name} ".$th->getMessage());
        }
    }
}
