<?php

namespace App\Imports\Employee;

use App\Models\Attendance\AttendanceShift;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeEducation;
use App\Models\Employee\EmployeeFamily;
use App\Models\Employee\EmployeePosition;
use App\Models\Setting\AppMasterData;
use DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Storage;

class EducationImport implements ToModel, WithEvents
{
    public array $levels;
    public array $employees;

    public string $logname;
    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "education-import_$today.log";
        $this->levels = AppMasterData::whereAppMasterCategoryCode('EMJP')->pluck('id', DB::raw('lower(name)'))->toArray();
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
            if(empty($employee_id)) $errors.="\n\t-Kolom NIP tidak boleh kosong";
            if(empty($name)) $errors.="\n\t-Kolom nama tidak boleh kosong";
            if(empty($level_id)) $errors.="\n\t-Kolom hubungan keluarga tidak boleh kosong";

            //MASTER VALIDATION
            if(!empty($employee_id) && empty($this->employees[$employee_id])) $errors.="\n\t-Kolom pegawai tidak terdaftar";
            if(!empty($level_id) && empty($this->levels[strtolower($level_id)])) $errors.="\n\t-Kolom tingkatan pendidikan tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if(!empty($errors)){
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : ".$errors);
            } else {
                $employee_id = $this->employees[$employee_id] ?? 0;
                $level_id = $this->levels[strtolower($level_id)] ?? 0;

                EmployeeEducation::updateOrCreate([
                    'employee_id' => $employee_id,
                    'level_id' => $level_id,
                ],[
                    'name' => $name,
                    'major' => $major,
                    'start_year' => $start_year,
                    'end_year' => $end_year,
                    'city' => $city,
                    'score' => $score,
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
