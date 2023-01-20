<?php

namespace App\Imports\Employee;

use App\Models\Attendance\AttendanceShift;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeAsset;
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

class AssetImport implements ToModel, WithEvents
{
    public array $categories;
    public array $types;
    public array $employees;

    public string $logname;
    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "asset-import_$today.log";
        $this->categories = AppMasterData::whereAppMasterCategoryCode('EKAS')->pluck('id', DB::raw('lower(name)'))->toArray();
        $this->types = AppMasterData::whereAppMasterCategoryCode('ETAS')->pluck('id', DB::raw('lower(name)'))->toArray();
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
        $assetName = trim($row[3]);
        $number = trim($row[4]);
        $category_id = trim($row[5]);
        $type_id = trim($row[6]);
        $date = trim($row[7]);
        $date_convert = !empty($date) ? substr($date, -5, 1) == '/' ? resetDate($date) : Date::excelToDateTimeObject($date)->format('Y-m-d') : '';
        $start_date = trim($row[8]);
        $start_date_convert = !empty($start_date) ? substr($start_date, -5, 1) == '/' ? resetDate($start_date) : Date::excelToDateTimeObject($start_date)->format('Y-m-d') : '';
        $end_date = trim($row[9]);
        $end_date_convert = !empty($end_date) ? substr($end_date, -5, 1) == '/' ? resetDate($end_date) : Date::excelToDateTimeObject($end_date)->format('Y-m-d') : '';
        $status = trim($row[10]) == 'Aktif' ? 't' : 'f';
        $description = trim($row[11]);

        $errors = "";
        try {
            //EMPTY VALIDATION
            if(empty($employee_id)) $errors.="\n\t-Kolom NIP tidak boleh kosong";
            if(empty($assetName)) $errors.="\n\t-Kolom Perihal tidak boleh kosong";
            if(empty($date)) $errors.="\n\t-Kolom Tanggal tidak boleh kosong";

            //DATE VALIDATION
            if(!empty($start_date) && $start_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal mulai tidak sesuai format $start_date_convert";
            if(!empty($end_date) && $end_date_convert == '0000-00-00') $errors.="\n\t-Kolom tanggal selesai tidak sesuai format $end_date_convert";

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

                EmployeeAsset::updateOrCreate([
                    'employee_id' => $employee_id,
                    'name' => $assetName,
                ],[
                    'number' => $number,
                    'category_id' => $category_id,
                    'type_id' => $type_id,
                    'date' => $date_convert,
                    'start_date' => $start_date_convert,
                    'end_date' => $end_date_convert,
                    'status' => $status,
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
