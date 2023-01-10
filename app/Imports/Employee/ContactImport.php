<?php

namespace App\Imports\Employee;

use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeContact;
use App\Models\Setting\AppMasterData;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Storage;

class ContactImport implements ToModel, WithEvents
{
    public array $employees;
    public array $relationships;
    public string $logname;
    public function __construct()
    {
        $today = now()->format('Y-m-d');
        $this->logname = "contact-import $today.log";
        $this->employees = Employee::pluck('id', 'employee_number')->toArray();
        $this->relationships = AppMasterData::whereAppMasterCategoryCode('EHK')->pluck('id', 'name')->toArray();
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function () {
                $storage = Storage::disk('log');
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
        $name = trim($row[2]);

        try {
            $employee_id = $this->employees[trim($row[1])] ?? 0;
            $relationship_id = $this->relationships[trim($row[4])] ?? 0;

            EmployeeContact::updateOrCreate([
                'employee_id' => $employee_id,
                'name' => $row[3],
            ],[
                'relationship_id' => $relationship_id,
                'phone_number' => $row[5],
            ]);

            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} SUCESS {$no} {$name}");
        } catch (\Throwable $th) {
            $now = now()->format("[Y-m-d H:i:s]");
            $storage->append($this->logname, "{$now} ERROR {$no} {$name}");
        }
    }
}
