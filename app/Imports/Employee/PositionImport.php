<?php

namespace App\Imports\Employee;

use App\Http\Requests\Employee\EmployeePositionHistoryRequest;
use App\Services\Attendance\AttendanceShiftService;
use App\Services\Employee\EmployeePositionService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
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

    private EmployeePositionService $employeePositionService;
    private EmployeeService $employeeService;
    private AppMasterDataService $appMasterDataService;
    private AttendanceShiftService $attendanceShiftService;

    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "position-import_$today.log";

        $this->employeeService = new EmployeeService();
        $this->appMasterDataService = new AppMasterDataService();
        $this->employeePositionService = new EmployeePositionService();
        $this->attendanceShiftService = new AttendanceShiftService();

        $this->employees = $this->employeeService->getEmployees()->pluck('employees.id', 'employee_number')->toArray();
        $this->leaders = $this->employeeService->getEmployees()->pluck('employees.id', 'name')->toArray();
        $this->positions = $this->appMasterDataService->getMasterForArray('EMP', 0, 'order', true);
        $this->employee_types = $this->appMasterDataService->getMasterForArray('ETP', 0, 'order', true);

        $this->ranks = $this->appMasterDataService->getMasterForArray('EP', 0, 'order', true);
        $this->grades = $this->appMasterDataService->getMasterForArray('EG', 0, 'order', true);
        $this->locations = $this->appMasterDataService->getMasterForArray('ELK', 0, 'order', true);
        $this->units = $this->appMasterDataService->getMasterForArray('EMU', 0, 'order', true);
        $this->shifts = $this->attendanceShiftService->getShifts()->pluck('id', \DB::raw('lower(name)'))->toArray();
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
        $employee_type_id = trim($row[3]);
        $position_id = trim($row[4]);
        $location_id = trim($row[5]);
        $rank_id = trim($row[6]);
        $grade_id = trim($row[7]);
        $sk_number = trim($row[8]);
        $sk_date = trim($row[9]);
        $sk_date_convert = !empty($sk_date) ? substr($sk_date, -5, 1) == '/' ? $sk_date : Date::excelToDateTimeObject($sk_date)->format('d/m/Y') : '';
        $start_date = trim($row[10]);
        $start_date_convert = !empty($start_date) ? substr($start_date, -5, 1) == '/' ? $start_date : Date::excelToDateTimeObject($start_date)->format('d/m/Y') : '';
        $end_date = trim($row[11]);
        $end_date_convert = !empty($end_date) ? substr($end_date, -5, 1) == '/' ? $end_date : Date::excelToDateTimeObject($end_date)->format('d/m/Y') : '';
        $unit_id = trim($row[12]);
        $shift_id = trim($row[13]);
        $leader_id = trim($row[14]);
        $status = trim($row[15]) == 'Aktif' ? 't' : 'f';

        $errors = "";
        try {
            //EMPTY VALIDATION
            if (empty($employee_number)) $errors .= "\n\t-Kolom NIP tidak boleh kosong";
            if (empty($name)) $errors .= "\n\t-Kolom nama tidak boleh kosong";
            if (empty($employee_type_id)) $errors .= "\n\t-Kolom tipe pegawai tidak boleh kosong";
            if (empty($position_id)) $errors .= "\n\t-Kolom posisi tidak boleh kosong";
            if (empty($location_id)) $errors .= "\n\t-Kolom lokasi tidak boleh kosong";
            if (empty($rank_id)) $errors .= "\n\t-Kolom pangkat tidak boleh kosong";
            if (empty($start_date)) $errors .= "\n\t-Kolom tanggal mulai tidak boleh kosong";
            if (empty($unit_id)) $errors .= "\n\t-Kolom unit tidak boleh kosong";
            if (empty($status)) $errors .= "\n\t-Kolom status tidak boleh kosong";

            //DATE VALIDATION
            if (!empty($sk_date) && $sk_date_convert == '0000-00-00') $errors .= "\n\t-Kolom tanggal sk tidak sesuai format $sk_date_convert";
            if (!empty($start_date) && $start_date_convert == '0000-00-00') $errors .= "\n\t-Kolom tanggal mulai tidak sesuai format $start_date_convert";
            if (!empty($end_date) && $end_date_convert == '0000-00-00') $errors .= "\n\t-Kolom tanggal selesai tidak sesuai format $end_date_convert";

            //MASTER VALIDATION
            if (!array_key_exists($employee_number, $this->employees)) $errors .= "\n\t-Kolom pegawai tidak terdaftar";
            if (!array_key_exists(trim(strtolower($employee_type_id)), $this->employee_types)) $errors .= "\n\t-Kolom tipe pegawai tidak terdaftar";
            if (!array_key_exists(trim(strtolower($position_id)), $this->positions)) $errors .= "\n\t-Kolom posisi tidak terdaftar";
            if (!array_key_exists(trim(strtolower($location_id)), $this->locations)) $errors .= "\n\t-Kolom lokasi tidak terdaftar";
            if (!array_key_exists(trim(strtolower($rank_id)), $this->ranks)) $errors .= "\n\t-Kolom pangkat tidak terdaftar";
            if (!empty($grade_id) && !array_key_exists(trim(strtolower($grade_id)), $this->grades)) $errors .= "\n\t-Kolom grade tidak terdaftar";
            if (!array_key_exists(trim(strtolower($unit_id)), $this->units)) $errors .= "\n\t-Kolom grade tidak terdaftar";
            if (!empty($leader_id) && !array_key_exists($leader_id, $this->leaders)) $errors .= "\n\t-Kolom atasan tidak terdaftar";
            if (!empty($shift_id) && !array_key_exists(trim(strtolower($shift_id)), $this->shifts)) $errors .= "\n\t-Kolom shift tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if (!empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : " . $errors);
            } else {
                \Debugbar::info($this->employees[$employee_number]);

                $arrData = [
                    'employee_id' => $this->employees[$employee_number],
                    'employee_type_id' => $this->employee_types[strtolower($employee_type_id)],
                    'position_id' => $this->positions[strtolower($position_id)],
                    'location_id' => $this->locations[strtolower($location_id)],
                    'rank_id' => $this->ranks[strtolower($rank_id)],
                    'grade_id' => $this->grades[strtolower($grade_id)] ?? 0,
                    'sk_number' => $sk_number,
                    'sk_date' => $sk_date_convert,
                    'start_date' => $start_date_convert,
                    'end_date' => $end_date_convert,
                    'unit_id' => $this->units[strtolower($unit_id)],
                    'shift_id' => $this->shifts[strtolower($shift_id)] ?? 0,
                    'leader_id' => $this->leaders[$leader_id] ?? 0,
                    'status' => $status,
                ];

                \Debugbar::info($arrData);

                $idExist = 0;
                $checkExist = $this->employeePositionService->getPositions()->select('employee_positions.id')->where('employee_positions.employee_id', $arrData['employee_id'])->where('employee_positions.start_date', $start_date_convert)->first();
                if ($checkExist) $idExist = $checkExist->id;

                $request = new EmployeePositionHistoryRequest();
                $request->merge($arrData);

                $this->employeePositionService->savePosition($request, $idExist);

                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} No. {$no} : SUCCESS {$no} {$name}");
            }

        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} No. {$no} : ERROR {$no} {$name} " . $th->getMessage());
        }
    }
}
