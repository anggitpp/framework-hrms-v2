<?php

namespace App\Imports\Employee;

use App\Models\Attendance\AttendanceShift;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeePosition;
use App\Models\Setting\AppMasterData;
use DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Storage;

class EmployeeImport implements ToModel, WithEvents
{
    public array $positions;
    public array $employees;
    public array $employee_types;
    public array $ranks;
    public array $grades;
    public array $locations;
    public array $units;
    public array $shifts;
    public array $statuses;
    public array $marital_statuses;
    public array $religions;

    public string $logname;
    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "employee-import_$today.log";
        $this->positions = AppMasterData::whereAppMasterCategoryCode('EMP')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->employee_types = AppMasterData::whereAppMasterCategoryCode('ETP')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->ranks = AppMasterData::whereAppMasterCategoryCode('EP')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->grades = AppMasterData::whereAppMasterCategoryCode('EG')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->locations = AppMasterData::whereAppMasterCategoryCode('ELK')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->units = AppMasterData::whereAppMasterCategoryCode('EMU')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->shifts = AttendanceShift::pluck('id', DB::raw('lower(name)'))->toArray();
        $this->statuses = AppMasterData::whereAppMasterCategoryCode('ESP')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->marital_statuses = AppMasterData::whereAppMasterCategoryCode('ESPK')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->religions = AppMasterData::whereAppMasterCategoryCode('EAG')->pluck('id', DB::raw('lower(name)'))->toArray();
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

        \Debugbar::info($row);

        //IDENTITY
        $no = trim($row[0]);
        $name = trim($row[1]);
        $nickname = trim($row[2]);
        $place_of_birth = trim($row[3]);
        $date_of_birth = trim($row[4]);
        $date_of_birth_convert = !empty($date_of_birth) ? substr($date_of_birth, -5, 1) == '/' ? resetDate($date_of_birth) : Date::excelToDateTimeObject($date_of_birth)->format('Y-m-d') : '';
        $employee_number = trim($row[5]);
        $identity_number = trim($row[6]);
        $gender = trim($row[7]) == 'Perempuan' ? 'f' : 'm';
        $email = trim($row[8]);
        $identity_address = trim($row[9]);
        $address = trim($row[10]);
        $mobile_phone_number = trim($row[11]);
        $phone_number = trim($row[12]);

        //POSITION
        $position_id = trim($row[13]);
        $employee_type_id = trim($row[14]);
        $rank_id = trim($row[15]);
        $grade_id = trim($row[16]);
        $sk_number = trim($row[17]);
        $sk_date = trim($row[18]);
        $sk_date_convert = !empty($sk_date) ? substr($sk_date, -5, 1) == '/' ? resetDate($sk_date) : Date::excelToDateTimeObject($sk_date)->format('Y-m-d') : '';
        $start_date = trim($row[19]);
        $start_date_convert = !empty($start_date) ? substr($start_date, -5, 1) == '/' ? resetDate($start_date) : Date::excelToDateTimeObject($start_date)->format('Y-m-d') : '';
        $end_date = trim($row[20]);
        $end_date_convert = !empty($end_date) ? substr($end_date, -5, 1) == '/' ? resetDate($end_date) : Date::excelToDateTimeObject($end_date)->format('Y-m-d') : '';
        $location_id = trim($row[21]);
        $unit_id = trim($row[22]);
        $shift_id = trim($row[23]);
        $leader_id = trim($row[24]);

        //STATUS
        $status_id = trim($row[25]);
        $join_date = trim($row[26]);
        $join_date_convert = !empty($join_date) ? substr($join_date, -5, 1) == '/' ? resetDate($join_date) : Date::excelToDateTimeObject($join_date)->format('Y-m-d') : '';
        $leave_date = trim($row[27]);
        $leave_date_convert = !empty($leave_date) ? substr($leave_date, -5, 1) == '/' ? resetDate($leave_date) : Date::excelToDateTimeObject($leave_date)->format('Y-m-d') : '';
        $attendance_pin = trim($row[28]);
        $marital_status_id = trim($row[29]);
        $religion_id = trim($row[30]);

        $errors = "";
        try {
            //VALIDASI IDENTITY EMPTY
            if(empty($name)) $errors.="\n\t-Kolom nama tidak boleh kosong";
            if(empty($employee_number)) $errors.="\n\t-Kolom nomor pegawai tidak boleh kosong";

            //VALIDASI IDENTITY DATE
            if(!empty($date_of_birth) && $date_of_birth_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal lahir tidak sesuai format $date_of_birth_convert";

            //VALIDASI POSITION EMPTY
            if(empty($position_id)) $errors.="\n\t-Kolom posisi tidak boleh kosong";
            if(empty($employee_type_id)) $errors.="\n\t-Kolom tipe pegawai tidak boleh kosong";
            if(empty($rank_id)) $errors.="\n\t-Kolom pangkat tidak boleh kosong";
            if(empty($start_date)) $errors.="\n\t-Kolom tanggal mulai tidak boleh kosong";
            if(empty($location_id)) $errors.="\n\t-Kolom lokasi tidak boleh kosong";
            if(empty($unit_id)) $errors.="\n\t-Kolom unit tidak boleh kosong";

            $getLeaderId = 0;
            $getLeader = Employee::where(DB::raw('lower(name)'), $leader_id)->first();
            if($getLeader) $getLeaderId = $getLeader->id;

            //VALIDASI POSITION MASTER
            if(!empty($position_id) && empty($this->positions[strtolower($position_id)])) $errors.="\n\t-Kolom posisi tidak terdaftar";
            if(!empty($employee_type_id) && empty($this->employee_types[strtolower($employee_type_id)])) $errors.="\n\t-Kolom tipe pegawai tidak terdaftar";
            if(!empty($rank_id) && empty($this->ranks[strtolower($rank_id)])) $errors.="\n\t-Kolom pangkat tidak terdaftar";
            if(!empty($grade_id) && empty($this->grades[strtolower($grade_id)])) $errors.="\n\t-Kolom grade tidak terdaftar";
            if(!empty($location_id) && empty($this->locations[strtolower($location_id)])) $errors.="\n\t-Kolom lokasi tidak terdaftar";
            if(!empty($unit_id) && empty($this->units[strtolower($unit_id)])) $errors.="\n\t-Kolom unit tidak terdaftar";
            if(!empty($shift_id) && empty($this->shifts[strtolower($shift_id)])) $errors.="\n\t-Kolom shift tidak terdaftar";
            if(!empty($leader_id) && empty($getLeaderId)) $errors.="\n\t-Kolom atasan tidak terdaftar";

            //VALIDASI POSITION DATE
            if(!empty($sk_date) && $sk_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal SK tidak sesuai format, format harus dd/mm/YYYY $sk_date_convert";
            if(!empty($start_date) && $start_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal mulai tidak sesuai format, format harus dd/mm/YYYY $start_date_convert";
            if(!empty($end_date) && $end_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal selesai tidak sesuai format, format harus dd/mm/YYYY $end_date_convert";

            //VALIDASI STATUS EMPTY
            if(empty($status_id)) $errors.="\n\t-Kolom Status tidak boleh kosong";
            if(empty($join_date)) $errors.="\n\t-Kolom tanggal masuk tidak boleh kosong";

            //VALIDASI STATUS DATE
            if(!empty($join_date) && $join_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal masuk tidak sesuai format, format harus dd/mm/YYYY $join_date_convert";
            if(!empty($leave_date) && $leave_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal keluar tidak sesuai format, format harus dd/mm/YYYY $leave_date_convert";

            //VALIDASI STATUS MASTER
            if(!empty($status_id) && empty($this->statuses[strtolower($status_id)])) $errors.="\n\t-Kolom status tidak terdaftar";
            if(!empty($marital_status_id) && empty($this->marital_statuses[strtolower($marital_status_id)])) $errors.="\n\t-Kolom status perkawinan tidak terdaftar";
            if(!empty($religion_id) && empty($this->religions[strtolower($religion_id)])) $errors.="\n\t-Kolom agama tidak terdaftar $religion_id";

            $now = now()->format("[Y-m-d H:i:s]");
            if(!empty($errors)){
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : ".$errors);
            } else {
                $status_id = $this->statuses[strtolower($status_id)] ?? 0;
                $marital_status_id = $this->marital_statuses[strtolower($marital_status_id)] ?? 0;
                $religion_id = $this->religions[strtolower($religion_id)] ?? 0;
                $position_id = $this->positions[strtolower($position_id)] ?? 0;
                $employee_type_id = $this->employee_types[strtolower($employee_type_id)] ?? 0;
                $rank_id = $this->ranks[strtolower($rank_id)] ?? 0;
                $grade_id = $this->grades[strtolower($grade_id)] ?? 0;
                $location_id = $this->locations[strtolower($location_id)] ?? 0;
                $unit_id = $this->units[strtolower($unit_id)] ?? 0;
                $shift_id = $this->shifts[strtolower($shift_id)] ?? 0;
                $leader_id = $getLeaderId ?? 0;

                $employee = Employee::updateOrCreate([
                    'employee_number' => $employee_number,
                ],[
                    'name' => $name,
                    'nickname' => $nickname,
                    'place_of_birth' => $place_of_birth,
                    'date_of_birth' => $date_of_birth_convert,
                    'employee_number' => $employee_number,
                    'identity_number' => $identity_number,
                    'gender' => $gender,
                    'email' => $email,
                    'address' => $address,
                    'identity_address' => $identity_address,
                    'mobile_phone_number' => $mobile_phone_number,
                    'phone_number' => $phone_number,
                    'status_id' => $status_id,
                    'join_date' => $join_date_convert,
                    'leave_date' => $leave_date_convert,
                    'attendance_pin' => $attendance_pin,
                    'marital_status_id' => $marital_status_id,
                    'religion_id' => $religion_id,
                ]);

                EmployeePosition::updateOrCreate([
                    'employee_id' => $employee->id,
                    'start_date' => $start_date_convert,
                ],[
                    'position_id' => $position_id,
                    'employee_type_id' => $employee_type_id,
                    'rank_id' => $rank_id,
                    'grade_id' => $grade_id,
                    'location_id' => $location_id,
                    'unit_id' => $unit_id,
                    'shift_id' => $shift_id,
                    'leader_id' => $leader_id,
                    'sk_number' => $sk_number,
                    'sk_date' => $sk_date_convert,
                    'end_date' => $end_date_convert,
                    'status' => 't',
                ]);

                $storage->append($this->logname, "{$now} No. {$no} : SUCCESS {$no} {$name}");
            }

        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} No. {$no} : ERROR {$no} {$name} ".$th->getMessage());
        }
    }
}
