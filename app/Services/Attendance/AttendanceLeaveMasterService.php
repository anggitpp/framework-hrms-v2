<?php
namespace App\Services\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\LeaveMasterRequest;
use App\Repositories\Attendance\AttendanceLeaveMasterRepository;
use App\Models\Attendance\AttendanceLeaveMaster;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceLeaveMasterService extends Controller
{
    private AttendanceLeaveMasterRepository $attendanceLeaveMasterRepository;
    public function __construct()
    {
        $this->attendanceLeaveMasterRepository = new AttendanceLeaveMasterRepository(
            new AttendanceLeaveMaster()
        );
    }

    public function getLeaveMasters() : Builder
    {
        return $this->attendanceLeaveMasterRepository->query();
    }

    public function getLeaveMasterById(int $id) : AttendanceLeaveMaster
    {
        return $this->attendanceLeaveMasterRepository->getById($id);
    }

    public function getLeaveMastersWithSpecificColumn(array $columns) : Builder
    {
        return $this->getLeaveMasters()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request) : JsonResponse
    {
        if($request->ajax()) {
            $query = $this->getLeaveMastersWithSpecificColumn(['id', 'name', 'balance', 'start_date', 'end_date', 'location_id', 'work_period', 'status']);
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) $query->where('name', 'like', "%{$filter}%")->orWhere('code', 'like', "%{$filter}%");
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'location_id', 'type' => 'master_relationship', 'masters' => 'location', 'null_value' => 'Semua Lokasi'],
                ['name' => 'status', 'type' => 'status'],
                ['name' => 'start_date', 'type' => 'custom', 'value' => function($data) {
                    return setDate($data->start_date).' - '.setDate($data->end_date);
                }],
            ], true);
        }
    }

    public function saveLeaveMaster(LeaveMasterRequest $request, int $id = 0): array
    {

        $fields = [
            'name' => $request->input('name'),
            'balance' => $request->input('balance'),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($request->input('end_date')),
            'location_id' => $request->input('location_id') ?? 0,
            'work_period' => $request->input('work_period'),
            'gender' => $request->input('gender'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ];

        try {
            if($id == 0)
                $this->attendanceLeaveMasterRepository->create($fields);
            else
                $this->attendanceLeaveMasterRepository->update($fields, $id);

            $arrResponse = [
                'message' => 'Data berhasil disimpan',
            ];
        } catch (Exception $e) {
            $arrResponse = [
                'message' => 'Error, Data gagal disimpan, '.$e->getMessage(),
            ];
        }

        return $arrResponse;
    }

    public function deleteLeaveMaster(int $id) : void
    {
        try {
            $this->attendanceLeaveMasterRepository->destroy($id);
            Alert::success('Success', 'Data master cuti berhasil dihapus!');

        } catch (Exception $e) {
            Alert::error('Error', 'Data master cuti gagal dihapus, '.$e->getMessage());
        }
    }
}
