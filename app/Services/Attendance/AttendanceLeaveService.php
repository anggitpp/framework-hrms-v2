<?php
namespace App\Services\Attendance;

use App\Http\Controllers\Controller;
use App\Repositories\Attendance\AttendanceLeaveRepository;
use App\Models\Attendance\AttendanceLeave;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceLeaveService extends Controller
{
    private AttendanceLeaveRepository $attendanceLeaveRepository;
    public function __construct()
    {
        $this->attendanceLeaveRepository = new AttendanceLeaveRepository(
            new AttendanceLeave()
        );
    }

    public function getLeaves() : Builder
    {
        return $this->attendanceLeaveRepository->query();
    }

    public function getLeaveById(int $id) : AttendanceLeave
    {
        return $this->attendanceLeaveRepository->getById($id);
    }

    public function getLeavesWithSpecificColumn(array $columns) : Builder
    {
        return $this->getLeaves()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request) : JsonResponse
    {
        if($request->ajax()) {
            $query = $this->getLeavesWithSpecificColumn(['id', 'location_id', 'code', 'name', 'start', 'end', 'description', 'status']);
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

    public function saveShift(LeaveReq $request, int $id = 0): array
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
                $this->attendanceLeaveRepository->create($fields);
            else
                $this->attendanceLeaveRepository->update($fields, $id);

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
            $this->attendanceLeaveRepository->destroy($id);
            Alert::success('Success', 'Data cuti berhasil dihapus!');

        } catch (Exception $e) {
            Alert::error('Error', 'Data cuti gagal dihapus, '.$e->getMessage());
        }
    }
}
