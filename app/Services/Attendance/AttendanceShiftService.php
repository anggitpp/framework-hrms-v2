<?php
namespace App\Services\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\ShiftRequest;
use App\Repositories\Attendance\AttendanceShiftRepository;
use App\Models\Attendance\AttendanceShift;
use Illuminate\Database\Eloquent\Builder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceShiftService extends Controller
{
    private AttendanceShiftRepository $attendanceShiftRepository;
    public function __construct()
    {
        $this->attendanceShiftRepository = new AttendanceShiftRepository(
            new AttendanceShift()
        );
    }

    public function getShifts() : Builder
    {
        return $this->attendanceShiftRepository->query();
    }

    public function getShiftById(int $id) : AttendanceShift
    {
        return $this->attendanceShiftRepository->getById($id);
    }

    public function getShiftsWithSpecificColumn(array $columns) : Builder
    {
        return $this->getShifts()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request) : JsonResponse
    {
        if($request->ajax()) {
            $query = $this->getShiftsWithSpecificColumn(['id', 'location_id', 'code', 'name', 'start', 'end', 'description', 'status']);
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) $query->where('name', 'like', "%{$filter}%")->orWhere('code', 'like', "%{$filter}%");
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'location_id', 'type' => 'master_relationship', 'masters' => 'location', 'null_value' => 'Semua Lokasi'],
                ['name' => 'start', 'type' => 'time'],
                ['name' => 'end', 'type' => 'time'],
                ['name' => 'status', 'type' => 'status'],
            ], true);
        }
    }

    public function saveShift(ShiftRequest $request, int $id = 0): array
    {

        $fields = [
            'name' => $request->input('name'),
            'location_id' => $request->input('location_id'),
            'code' => $request->input('code'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'description' => $request->input('description'),
            'night_shift' => $request->input('night_shift') ? 't' : 'f',
            'status' => $request->input('status'),
        ];

        try {
            if($id == 0)
                $this->attendanceShiftRepository->create($fields);
            else
                $this->attendanceShiftRepository->update($fields, $id);

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

    public function deleteShift(int $id) : void
    {
        try {
            $this->attendanceShiftRepository->destroy($id);
            Alert::success('Success', 'Data shift berhasil dihapus!');

        } catch (Exception $e) {
            Alert::error('Error', 'Data shift gagal dihapus, '.$e->getMessage());
        }

    }
}
