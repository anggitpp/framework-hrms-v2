<?php

namespace App\Repositories\Payroll;

use App\Models\Payroll\PayrollFixed;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class PayrollFixedRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new PayrollFixed());
    }
    public function getPayrollFixeds(): Builder
    {
        return $this->query();
    }

    public function getPayrollFixedById(int $id): PayrollFixed
    {
        return $this->getById($id);
    }

    public function storePayrollFixed(array $data): PayrollFixed
    {
        return $this->create($data);
    }

    public function updatePayrollFixed(array $data, int $id): PayrollFixed
    {
        return $this->update($data, $id);
    }

    public function deletePayrollFixed(int $id): void
    {
        $this->destroy($id);
    }
}
