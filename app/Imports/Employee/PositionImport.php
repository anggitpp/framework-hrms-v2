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

class PositionImport implements ToModel, WithEvents
{
    public array $employees;
    public array $positions;
    public array $employee_types;
    public array $ranks;
    public array $grades;
    public array $locations;
    public array $units;
    public array $shifts;
    public array $leaders;

    public string $logname;
    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "position-import_$today.log";
        $this->positions = AppMasterData::whereAppMasterCategoryCode('EMP')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->employee_types = AppMasterData::whereAppMasterCategoryCode('ETP')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->ranks = AppMasterData::whereAppMasterCategoryCode('EP')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->grades = AppMasterData::whereAppMasterCategoryCode('EG')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->locations = AppMasterData::whereAppMasterCategoryCode('ELK')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->units = AppMasterData::whereAppMasterCategoryCode('EMU')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->shifts = AttendanceShift::pluck('id', DB::raw('lower(name)'))->toArray();
        $this->employees = Employee::pluck('id', 'employee_number')->toArray();
        $this->leaders = Employee::pluck('id', DB::raw('lower(name)'))->toArray();
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
        $employee_type_id = trim($row[3]);
        $position_id = trim($row[4]);
        $location_id = trim($row[5]);
        $rank_id = trim($row[6]);
        $grade_id = trim($row[7]);
        $sk_number = trim($row[8]);
        $sk_date = trim($row[9]);
        $sk_date_convert = !empty($sk_date) ? substr($sk_date, -5, 1) == '/' ? resetDate($sk_date) : Date::excelToDateTimeObject($sk_date)->format('Y-m-d') : '';
        $start_date = trim($row[10]);
        $start_date_convert = !empty($start_date) ? substr($start_date, -5, 1) == '/' ? resetDate($start_date) : Date::excelToDateTimeObject($start_date)->format('Y-m-d') : '';
        $end_date = trim($row[11]);
        $end_date_convert = !empty($end_date) ? substr($end_date, -5, 1) == '/' ? resetDate($end_date) : Date::excelToDateTimeObject($end_date)->format('Y-m-d') : '';
        $unit_id = trim($row[12]);
        $shift_id = trim($row[13]);
        $leader_id = trim($row[14]);
        $status = trim($row[15]) == 'Aktif' ? 't' : 'f';

        $errors = "";
        try {
            //EMPTY VALIDATION
            if(empty($employee_id)) $errors.="\n\t-Kolom NIP tidak boleh kosong";
            if(empty($name)) $errors.="\n\t-Kolom nama tidak boleh kosong";
            if(empty($employee_type_id)) $errors.="\n\t-Kolom tipe pegawai tidak boleh kosong";
            if(empty($position_id)) $errors.="\n\t-Kolom posisi tidak boleh kosong";
            if(empty($location_id)) $errors.="\n\t-Kolom lokasi tidak boleh kosong";
            if(empty($rank_id)) $errors.="\n\t-Kolom pangkat tidak boleh kosong";
            if(empty($start_date)) $errors.="\n\t-Kolom tanggal mulai tidak boleh kosong";
            if(empty($unit_id)) $errors.="\n\t-Kolom unit tidak boleh kosong";
            if(empty($status)) $errors.="\n\t-Kolom status tidak boleh kosong";

            //DATE VALIDATION
            if(!empty($sk_date) && $sk_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal sk tidak sesuai format $sk_date_convert";
            if(!empty($start_date) && $start_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal mulai tidak sesuai format $start_date_convert";
            if(!empty($end_date) && $end_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal selesai tidak sesuai format $end_date_convert";

            //MASTER VALIDATION
            if(!empty($employee_id) && empty($this->employees[$employee_id])) $errors.="\n\t-Kolom pegawai tidak terdaftar";
            if(!empty($employee_type_id) && empty($this->employee_types[strtolower($employee_type_id)])) $errors.="\n\t-Kolom tipe pegawai tidak terdaftar";
            if(!empty($position_id) && empty($this->positions[strtolower($position_id)])) $errors.="\n\t-Kolom posisi tidak terdaftar";
            if(!empty($location_id) && empty($this->locations[strtolower($location_id)])) $errors.="\n\t-Kolom lokasi tidak terdaftar";
            if(!empty($rank_id) && empty($this->ranks[strtolower($rank_id)])) $errors.="\n\t-Kolom pangkat tidak terdaftar";
            if(!empty($grade_id) && empty($this->grades[strtolower($grade_id)])) $errors.="\n\t-Kolom grade tidak terdaftar";
            if(!empty($unit_id) && empty($this->units[strtolower($unit_id)])) $errors.="\n\t-Kolom grade tidak terdaftar";
            if(!empty($shift_id) && empty($this->shifts[strtolower($shift_id)])) $errors.="\n\t-Kolom shift tidak terdaftar";
            if(!empty($leader_id) && empty($this->leaders[strtolower($leader_id)])) $errors.="\n\t-Kolom leader tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if(!empty($errors)){
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : ".$errors);
            } else {
                $employee_id = $this->employees[$employee_id] ?? 0;
                $employee_type_id = $this->employee_types[strtolower($employee_type_id)] ?? 0;
                $position_id = $this->positions[strtolower($position_id)] ?? 0;
                $location_id = $this->locations[strtolower($location_id)] ?? 0;
                $rank_id = $this->ranks[strtolower($rank_id)] ?? 0;
                $grade_id = $this->grades[strtolower($grade_id)] ?? 0;
                $unit_id = $this->units[strtolower($unit_id)] ?? 0;
                $shift_id = $this->shifts[strtolower($shift_id)] ?? 0;
                $leader_id = $this->employees[$leader_id] ?? 0;

                $position = EmployeePosition::updateOrCreate([
                    'employee_id' => $employee_id,
                    'start_date' => $start_date_convert,
                ],[
                    'employee_type_id' => $employee_type_id,
                    'position_id' => $position_id,
                    'location_id' => $location_id,
                    'rank_id' => $rank_id,
                    'grade_id' => $grade_id,
                    'sk_number' => $sk_number,
                    'sk_date' => $sk_date_convert,
                    'start_date' => $start_date_convert,
                    'end_date' => $end_date_convert,
                    'unit_id' => $unit_id,
                    'shift_id' => $shift_id,
                    'leader_id' => $leader_id,
                    'status' => $status,
                ]);

                if($status == 't'){
                    EmployeePosition::where('employee_id', $employee_id)->where('id', '!=', $position->id)->update(['status' => 'f']);
                }

                $storage->append($this->logname, "{$now} No. {$no} : SUCCESS {$no} {$name}");
            }

        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} No. {$no} : ERROR {$no} {$name} ".$th->getMessage());
        }
    }
}
