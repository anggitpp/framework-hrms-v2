<?php
namespace App\Services\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\HolidayRequest;
use App\Repositories\Attendance\AttendanceHolidayRepository;
use App\Models\Attendance\AttendanceHoliday;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceHolidayService extends Controller
{
    private AttendanceHolidayRepository $attendanceHolidayRepository;
    public function __construct()
    {
        $this->attendanceHolidayRepository = new AttendanceHolidayRepository(
            new AttendanceHoliday()
        );
    }

    public function getHolidays() : Builder
    {
        return $this->attendanceHolidayRepository->query();
    }

    public function getHolidayById(int $id) : AttendanceHoliday
    {
        return $this->attendanceHolidayRepository->getById($id);
    }

    public function getHolidaysWithSpecificColumn(array $columns) : Builder
    {
        return $this->getHolidays()->select($columns);
    }

    /**
     * @throws Exception
     */
    public function data(Request $request) : JsonResponse
    {
        if($request->ajax()) {
            $query = $this->getHolidaysWithSpecificColumn(['id', 'name', 'start_date', 'end_date', 'description', 'status']);
            $filter = $request->get('search')['value'];
            $queryFilter = function ($query) use ($filter) {
                if (isset($filter)) $query->where('name', 'like', "%{$filter}%");
            };

            return generateDatatable($query, $queryFilter, [
                ['name' => 'start_date', 'type' => 'date'],
                ['name' => 'end_date', 'type' => 'date'],
                ['name' => 'status', 'type' => 'status'],
            ], true);
        }
    }

    public function saveHoliday(HolidayRequest $request, int $id = 0): array
    {

        $fields = [
            'name' => $request->input('name'),
            'start_date' => resetDate($request->input('start_date')),
            'end_date' => resetDate($request->input('end_date')),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ];

        try {
            if($id == 0)
                $this->attendanceHolidayRepository->create($fields);
            else
                $this->attendanceHolidayRepository->update($fields, $id);

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

    public function deleteHoliday(int $id) : void
    {
        try {
            $this->attendanceHolidayRepository->destroy($id);
            Alert::success('Success', 'Data libur resmi berhasil dihapus!');

        } catch (Exception $e) {
            Alert::error('Error', 'Data libur resmi gagal dihapus, '.$e->getMessage());
        }

    }
}
