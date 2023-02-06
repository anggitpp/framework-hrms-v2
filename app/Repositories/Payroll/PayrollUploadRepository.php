<?php

namespace App\Repositories\Payroll;

use App\Models\Payroll\PayrollFixed;
use App\Models\Payroll\PayrollUpload;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PayrollUploadRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new PayrollUpload());
    }
    public function getPayrollUploads(): Builder
    {
        return $this->query();
    }

    public function getPayrollUploadById(int $id): PayrollUpload
    {
        return $this->getById($id);
    }

    public function getPayrollUploadsByMonthAndYear(string $month, string $year, string $code): Builder
    {
        return $this->query()->where('month', $month)->where('year', $year)->where('code', $code);
    }

    public function getPayrollUploadsDetailByMonthAndYearWithEmployeeeAndPosition(string $month, string $year, string $code): Builder
    {
        return $this->model->join('employees', 'payroll_uploads.employee_id', 'employees.id')
            ->join('employee_positions', function ($join){
                $join->on('employees.id', 'employee_positions.employee_id')
                    ->where('employee_positions.status', 't');
            })
            ->join('app_master_data', 'employee_positions.position_id', 'app_master_data.id')
            ->where('payroll_uploads.code', $code)
            ->where('month', $month)
            ->where('year',$year)
            ->select(['payroll_uploads.*', 'employees.name', 'app_master_data.name as position_id']);
    }

    public function getPayrollUploadsByYear(string $year, string $code): Builder
    {
        return $this->query()->where('year', $year)->where('code', $code);
    }

    public function storePayrollUpload(array $data): PayrollUpload
    {
        return $this->create($data);
    }

    public function updatePayrollUpload(array $data, int $id): PayrollUpload
    {
        return $this->update($data, $id);
    }

    public function updateOrCreatePayrollUpload(array $data, array $updateData)
    {
        return $this->updateOrCreate($data, $updateData);
    }

    public function deletePayrollUpload(int $id): void
    {
        $this->destroy($id);
    }
}
