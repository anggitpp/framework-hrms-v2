<?php

namespace App\Imports\Employee;

use App\Http\Requests\Employee\EmployeeAssetRequest;
use App\Services\Employee\EmployeeAssetService;
use App\Services\Employee\EmployeeService;
use App\Services\Setting\AppMasterDataService;
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
    private EmployeeAssetService $employeeAssetService;
    private EmployeeService $employeeService;
    private AppMasterDataService $appMasterDataService;

    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "asset-import_$today.log";

        $this->employeeAssetService = new EmployeeAssetService();
        $this->employeeService = new EmployeeService();
        $this->appMasterDataService = new AppMasterDataService();

        $this->employees = $this->employeeService->getEmployees()->pluck('employees.id', 'employee_number')->toArray();
        $this->categories = $this->appMasterDataService->getMasterForArray('EKAS', 0, 'order', true);
        $this->types = $this->appMasterDataService->getMasterForArray('ETAS', 0, 'order', true);
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
            if (empty($employee_number)) $errors .= "\n\t-Kolom NIP tidak boleh kosong";
            if (empty($assetName)) $errors .= "\n\t-Kolom Perihal tidak boleh kosong";
            if (empty($date)) $errors .= "\n\t-Kolom Tanggal tidak boleh kosong";

            //DATE VALIDATION
            if (!empty($date) && $date_convert == '0000-00-00') $errors .= "\n\t-Kolom tanggal tidak sesuai format $date_convert";
            if (!empty($start_date) && $start_date_convert == '0000-00-00') $errors .= "\n\t-Kolom tanggal mulai tidak sesuai format $start_date_convert";
            if (!empty($end_date) && $end_date_convert == '0000-00-00') $errors .= "\n\t-Kolom tanggal selesai tidak sesuai format $end_date_convert";

            //MASTER VALIDATION
            if (!array_key_exists($employee_number, $this->employees)) $errors .= "\n\t-Kolom pegawai tidak terdaftar";
            if (!array_key_exists(trim(strtolower($category_id)), $this->categories)) $errors .= "\n\t-Kolom Kategori tidak terdaftar";
            if (!array_key_exists(trim(strtolower($type_id)), $this->types)) $errors .= "\n\t-Kolom Tipe tidak terdaftar";

            $now = now()->format("[Y-m-d H:i:s]");
            if (!empty($errors)) {
                $storage->append($this->logname, "{$now} No. {$no} : GAGAL, {$name} TERKENA VALIDASI : " . $errors);
            } else {
                $arrData = [
                    'employee_id' => $this->employees[$employee_number],
                    'number' => $number,
                    'name' => $assetName,
                    'date' => $date,
                    'category_id' => $this->categories[trim(strtolower($category_id))],
                    'type_id' => $this->types[trim(strtolower($type_id))],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'status' => $status,
                    'description' => $description,
                ];

                $idExist = 0;
                $checkExist = $this->employeeAssetService->getAssets()->select('employee_assets.id')->where('employee_assets.employee_id', $arrData['employee_id'])->where('employee_assets.name', $arrData['name'])->first();
                if ($checkExist) $idExist = $checkExist->id;

                $request = new EmployeeAssetRequest();
                $request->merge($arrData);

                $this->employeeAssetService->saveAsset($request, $idExist);

                $now = now()->format("[Y-m-d H:i:s]");
                $storage->append($this->logname, "{$now} No. {$no} : SUCCESS {$no} {$name}");
            }

        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} No. {$no} : ERROR {$no} {$name} " . $th->getMessage());
        }
    }
}
