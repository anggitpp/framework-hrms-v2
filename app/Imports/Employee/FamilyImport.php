<?php

namespace App\Imports\Employee;

use App\Models\Attendance\AttendanceShift;
use App\Models\Employee\Employee;
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

class FamilyImport implements ToModel, WithEvents
{
    public array $relationships;
    public array $employees;

    public string $logname;
    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "family-import_$today.log";
        $this->relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('id', DB::raw('lower(name)'))->toArray();
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
        $name = trim($row[3]);
        $relationship_id = trim($row[4]);
        $identity_number = trim($row[5]);
        $birth_date = trim($row[6]);
        $birth_date_convert = !empty($birth_date) ? substr($birth_date, -5, 1) == '/' ? resetDate($birth_date) : Date::excelToDateTimeObject($birth_date)->format('Y-m-d') : '';
        $birth_place = trim($row[7]);
        $gender = trim($row[8]);
        $description = trim($row[9]);

        $errors = "";
        try {
            //EMPTY VALIDATION
            if(empty($employee_id)) $errors.="\n\t-Kolom NIP tidak boleh kosong";
            if(empty($name)) $errors.="\n\t-Kolom nama tidak boleh kosong";
            if(empty($relationship_id)) $errors.="\n\t-Kolom hubungan keluarga tidak boleh kosong";

            //DATE VALIDATION
            if(!empty($birth_date) && $birth_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal lahir tidak sesuai format $birth_date_convert";

            //MASTER VALIDATION
            if(!empty($employee_id) && empty($this->employees[$employee_id])) $errors.="\n\t-Kolom pegawai tidak terdaftar";
            if(!empty($relationship_id) && empty($this->relationships[strtolower($relationship_id)])) $errors.="\n\t-Kolom hubungan keluarga tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if(!empty($errors)){
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : ".$errors);
            } else {
                $employee_id = $this->employees[$employee_id] ?? 0;
                $relationship_id = $this->relationships[strtolower($relationship_id)] ?? 0;
                $gender = $gender == 'Laki-Laki' ? 'm' : 'f';

                EmployeeFamily::updateOrCreate([
                    'employee_id' => $employee_id,
                    'name' => $name,
                ],[
                    'relationship_id' => $relationship_id,
                    'identity_number' => $identity_number,
                    'birth_date' => $birth_date_convert,
                    'birth_place' => $birth_place,
                    'gender' => $gender,
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
