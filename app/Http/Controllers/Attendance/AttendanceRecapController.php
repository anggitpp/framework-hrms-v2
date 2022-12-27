<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\Attendance\AttendanceRecapExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceHoliday;
use App\Models\Attendance\AttendancePermission;
use App\Models\Attendance\AttendanceShift;
use App\Models\Attendance\AttendanceWorkSchedule;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeSignatureSetting;
use App\Models\Setting\AppMasterData;
use Carbon\Carbon;
use Excel;
use FPDFTable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Yajra\DataTables\DataTables;

ini_set('memory_limit', '4096M');

class AttendanceRecapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $units = AppMasterData::whereAppMasterCategoryCode('EMU')->pluck('name', 'id')->toArray();
        $ranks = AppMasterData::whereAppMasterCategoryCode('EP')->pluck('name', 'id')->toArray();

        $filterMonth = $request->get('filterMonth') ?? date('m');
        $filterYear = $request->get('filterYear') ?? date('Y');

        $arrType = ['PT', 'PC', 'TPT', 'A', 'I', 'C', 'S', 'IB', 'DL', 'HEF', 'HDR', 'JH', 'JA', 'JK', 'HK', 'TA', 'PUNISHMENT'];

        $user = Auth::user();

        Session::put('arrType', $arrType);
        Session::put('user', $user);

        $data['filterMonth'] = $filterMonth;
        $data['filterYear'] = $filterYear;
        $data['arrType'] = $arrType;

        $data['units'] = $units;
        $data['ranks'] = $ranks;

        return view('attendances.attendance-recap.index', $data);
    }

    public function data(Request $request, $filterMonth, $filterYear)
    {
        $arrType = Session::get('arrType');
        $user = Session::get('user');
        $totalDayWithoutWeekend = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, Carbon::create($filterYear, $filterMonth, 1)->endOfMonth());

        if($request->ajax()){
            $filter = $request->get('search')['value'];

            $filterUnit = $request->get('combo_3');
            $filterRank = $request->get('combo_4');
            $sql = Employee::select(['id', 'name', 'employee_number'])->whereHas('position', function($query) use ($filterUnit, $filterRank, $user){
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                    $query->where('leader_id', $user->employee_id);
                if($filterRank) $query->where('rank_id', $filterRank);
                if($filterUnit) $query->where('unit_id', $filterUnit);
            });
            $filteredEmployee = clone $sql;
            $filteredEmployee = $filteredEmployee->get();
            $arrData = $this->datas($filteredEmployee, $filterMonth, $filterYear);
            $table = DataTables::of($sql)
                ->filter(function ($query) use ($filter) {
                    $query->where(function ($query) use ($filter) {
                        $query->where('name', 'like', "%$filter%")
                            ->orWhere('employee_number', 'like', "%$filter%");
                    });
                });
                foreach ($arrType as $key => $type) {
                    $table->addColumn('value_'.$type, function ($row) use ($arrData, $type, $totalDayWithoutWeekend) {
                        if($type == 'HEF'){
                            return $arrData[$row->id][$type] == 0 ? $totalDayWithoutWeekend : $arrData[$row->id][$type];
                        }
                        return $arrData[$row->id][$type];
                    });
                }

            return $table->addIndexColumn()
                ->make();
        }
    }

    public function export(Request $request)
    {
        $masters = AppMasterData::whereIn('app_master_category_code', ['EG', 'EP', 'ETP', 'EMU'])->pluck('name', 'id')->toArray();
        $user = Session::get('user');
        $filterMonth = $request->get('filterMonth');
        $filterYear = $request->get('filterYear');

        $totalDayWithoutWeekend = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, Carbon::create($filterYear, $filterMonth, 1)->endOfMonth());

        $units = AppMasterData::whereAppMasterCategoryCode('EMU');
        if ($request->get('combo_3') && $request->get('combo_3') != 'undefined') $units->whereId($request->get('combo_3'));
        $units = $units->pluck('name', 'id')->toArray();

        $data = [];
        foreach ($units as $key => $value) {
            /** DATA START */
            $employees = Employee::whereHas('position', function ($query) use ($request, $user, $key) {
                $query->where('unit_id', $key);
                if ($request->get('combo_4')) $query->where('rank_id', $request->get('combo_4'));
                if (!$user->hasPermissionTo('lvl3 ' . $this->menu_path()))
                    $query->where('leader_id', $user->employee_id);
            })->get();

            $datas = $this->datas($employees, $filterMonth, $filterYear);

            $no = 0;
            foreach ($employees as $employee) {
                $no++;
                $positionName = $employee->position->position_id ? AppMasterData::find($employee->position->position_id)->name : '';
                $PT = $datas[$employee->id]['PT'];
                $PC = $datas[$employee->id]['PC'];
                $TPT = $datas[$employee->id]['TPT'];
                $A = $datas[$employee->id]['A'];
                $I = $datas[$employee->id]['I'];
                $C = $datas[$employee->id]['C'];
                $S = $datas[$employee->id]['S'];
                $IB = $datas[$employee->id]['IB'];
                $DL = $datas[$employee->id]['DL'];
                $HEF = $datas[$employee->id]['HEF'] == 0 ? $totalDayWithoutWeekend : $datas[$employee->id]['HEF'];
                $HDR = $datas[$employee->id]['HDR'];
                $JH = $datas[$employee->id]['JH'];
                $JA = $datas[$employee->id]['JA'];
                $JK = $datas[$employee->id]['JK'];
                $HK = $datas[$employee->id]['HK'];
                $TA = $datas[$employee->id]['TA'];
                $punishment = $datas[$employee->id]['PUNISHMENT'];
                $data[$key][$employee->id] = [
                    $no,
                    $employee->name,
                    $positionName,
                    $PT,
                    $PC,
                    $TPT,
                    $A,
                    $I,
                    $C,
                    $S,
                    $IB,
                    $DL,
                    $HEF,
                    $HDR,
                    $JH,
                    $JA,
                    $JK,
                    $HK,
                    $TA,
                    $punishment,
                ];
            }
        }

        return Excel::download(new AttendanceRecapExport(
            [
                'data' => $data,
                'headerTitle' => 'Data Rekap Absen',
                'headerSubtitle' => "PERIODE : ".numToMonth($request->get('filterMonth')).' '.$request->get('filterYear'),
                'additional_title' => $request->get('combo_3') ? 'UNIT : '.$masters[$request->get('combo_3')] : 'SEMUA UNIT',
                'units' => $units,
            ]
        ), 'Rekap Absen.xlsx');
    }

    public function pdf(Request $request)
    {
        $user = Session::get('user');
        $filterMonth = $request->get('filterMonth');
        $filterYear = $request->get('filterYear');
        $totalDayWithoutWeekend = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, Carbon::create($filterYear, $filterMonth, 1)->endOfMonth());

        $unitName = 'SEMUA UNIT';
        if($request->get('combo_3')) $unitName = AppMasterData::find($request->get('combo_3'))->name;

        $pdf = new FPDFTable('L', 'mm', ['250', '365']);
        $pdf->SetAutoPageBreak(TRUE);
        $pdf->SetTitle('REKAP ABSEN '.$unitName.' ' . strtoupper(numToMonth($request->get('filterMonth'))) . ' ' . $request->get('filterYear'));

        $units = AppMasterData::whereAppMasterCategoryCode('EMU');
        //FILTER IF ANY UNIT
        if($request->get('combo_3') && $request->get('combo_3') != 'undefined') $units->whereId($request->get('combo_3'));
        $filterRank = $request->get('combo_4');

        $units = $units->get();
        foreach ($units as $unit) {
            $employees = Employee::whereHas('position', function($query) use ($filterRank, $user, $unit){
                $query->where('unit_id', $unit->id);
                if($filterRank && $filterRank != 'undefined') $query->where('rank_id', $filterRank);
                if(!$user->hasPermissionTo('lvl3 '.$this->menu_path()))
                    $query->where('leader_id', $user->employee_id);
            });
            $employees = $employees->orderBy('name')->get();
            $datas = $this->datas($employees, $filterMonth, $filterYear);

            $pdf->AddPage();

            $pdf->Image("assets/media/logos/logo-2.png", $pdf->GetX()+1, $pdf->GetY()+1, 20, 15);

            $pdf->SetXY(32,15);
            $pdf->SetFont('Arial', 'B', 20);
            $pdf->Cell(100, 5, 'KEMENTERIAN AGAMA', 0, 0, 'L');

            $pdf->SetXY(32,22);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(100, 5, 'KANTOR WILAYAH PROVINSI DKI JAKARTA', 0, 0, 'L');

            $pdf->Ln(-10);

            /** TITLE START */
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(180);
            $pdf->Cell(200, 7, 'LAPORAN KEHADIRAN PEGAWAI', 0, 0, 'L');
            $pdf->Ln();

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(180);
            $pdf->Cell(35, 7, 'UNIT KERJA', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(3, 7, ':', 0, 0, 'L');
            $pdf->Cell(30, 7, $unit->name, 0, 0, 'L');
            $pdf->Ln(5);
            $pdf->Cell(180);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(35, 7, 'BULAN LAPORAN', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(3, 7, ':', 0, 0, 'L');
            $pdf->Cell(30, 7, numToMonth($request->get('filterMonth'))." ".$request->get('filterYear'), 0, 0, 'L');
            $pdf->Ln(8);
            /** TITLE END */

            /** HEADER START */
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(10,10,'No.', 1,0, 'C');
            $pdf->Cell(55,10,'Nama Pegawai', 1,0, 'C');
            $pdf->Cell(55,10,'Jabatan', 1,0, 'C');
            $pdf->Cell(10,10,'PT', 1,0, 'C');
            $pdf->Cell(10,10,'PC', 1,0, 'C');
            $pdf->Cell(10,10,'TPT', 1,0, 'C');
            $pdf->Cell(10,10,'A', 1,0, 'C');
            $pdf->Cell(10,10,'I', 1,0, 'C');
            $pdf->Cell(10,10,'C', 1,0, 'C');
            $pdf->Cell(10,10,'S', 1,0, 'C');
            $pdf->Cell(10,10,'IB', 1,0, 'C');
            $pdf->Cell(10,10,'DL', 1,0, 'C');
            $pdf->Cell(10,10,'HEF', 1,0, 'C');
            $pdf->Cell(10,10,'HDR', 1,0, 'C');
            $pdf->Cell(15,10,'JH', 1,0, 'C');
            $pdf->Cell(15,10,'JA', 1,0, 'C');
            $pdf->Cell(15,10,'JK', 1,0, 'C');
            $pdf->Cell(10,10,'HK', 1,0, 'C');
            $pdf->Cell(10,10,'TA', 1,0, 'C');
            $pdf->Cell(50,10,'Hukuman', 1,0, 'C');
            /** HEADER END */

            /** DATA START */
            $pdf->Ln(10);
            $pdf->SetWidths(array(10, 55, 55, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 15, 15, 15, 10, 10, 50));
            $pdf->SetAligns(array('C','L','L','R','R', 'R', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
            $pdf->SetFont('Arial', '', 7);

            $no = 0;
            foreach ($employees as $employee){
                $no++;
                $positionName = $employee->position->position_id ? AppMasterData::find($employee->position->position_id)->name : '';
                $PT = $datas[$employee->id]['PT'];
                $PC = $datas[$employee->id]['PC'];
                $TPT = $datas[$employee->id]['TPT'];
                $A = $datas[$employee->id]['A'];
                $I = $datas[$employee->id]['I'];
                $C = $datas[$employee->id]['C'];
                $S = $datas[$employee->id]['S'];
                $IB = $datas[$employee->id]['IB'];
                $DL = $datas[$employee->id]['DL'];
                $HEF = $datas[$employee->id]['HEF'] == 0 ? $totalDayWithoutWeekend : $datas[$employee->id]['HEF'];
                $HDR = $datas[$employee->id]['HDR'];
                $JH = $datas[$employee->id]['JH'];
                $JA = $datas[$employee->id]['JA'];
                $JK = $datas[$employee->id]['JK'];
                $HK = $datas[$employee->id]['HK'];
                $TA = $datas[$employee->id]['TA'];
                $punishment = $datas[$employee->id]['PUNISHMENT'];
                $pdf->Row(array(
                    $no."\t",
                    $employee->name."\t",
                    $positionName."\t",
                    $PT."\t",
                    $PC."\t",
                    $TPT."\t",
                    $A."\t",
                    $I."\t",
                    $C."\t",
                    $S."\t",
                    $IB."\t",
                    $DL."\t",
                    $HEF."\t",
                    $HDR."\t",
                    $JH."\t",
                    $JA."\t",
                    $JK."\t",
                    $HK."\t",
                    $TA."\t",
                    $punishment."\t"));
            }
            /** DATA END */

            $currentY = $pdf->GetY();

            /** FOOTER START */
            $pdf->Ln(1);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(30, 5,'Keterangan:');
            $pdf->Ln(4);
            $pdf->Cell(150, 5, 'PT : Total potongan keterlambatan | PC : Total potongan pulang sebelum waktu | TPT : Jml. Total PT + PC |');
            $pdf->Ln(4);
            $pdf->Cell(150, 5, 'A : Jml.total alpa | I : Jml. total Izin | C : jml. total Cuti | S : Jml. total Sakit | DL : Jml.total Dinas Luar |');
            $pdf->Ln(4);
            $pdf->Cell(150, 5, 'IB : Jml. Total Izin Belajar | HEF : Jml. Hari Efektif | HDR : Jml. Hadir | JH : Jml. Jam Hadir Normal |');
            $pdf->Ln(4);
            $pdf->Cell(150, 5, 'JA : Jml. Jam Aktif | JK : Jml. Kekurangan Jam (Tand + utk kekurangan jam, tanda - untuk kelebihan jam |');
            $pdf->Ln(4);
            $pdf->Cell(150, 5, 'HK : Konversi Jam Kurang jadi Hari');


            $pdf->SetFont('Arial', '', 9);
            $pdf->SetXY(275, $currentY + 10);
            $pdf->Cell(40,0, 'Jakarta,'.setDate(date('Y-m-d'), 't'), '','','C');
            $pdf->SetXY(275, $currentY + 15);
            $pdf->Cell(40,0, 'Kepala Bagian Tata Usaha', '','','C');

            $getSignature = EmployeeSignatureSetting::active()->first();

            $pdf->SetFont('Arial', 'BU', 9);
            $pdf->SetXY(275, $currentY + 35);
            $pdf->Cell(40,0, '    '.$getSignature->employee->name.'    ', '','','C');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(275, $currentY + 40);
            $pdf->Cell(40,0, 'NIP : '.$getSignature->employee->employee_number, '','','C');
            /**  FOOTER END */
        }

        $pdf->Output();
        exit;
    }

    public function datas($employees, $filterMonth, $filterYear){
        $defaultShift = AttendanceShift::orderBy('id')->first();

        $startDate = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($filterYear, $filterMonth, 1)->endOfMonth()->format('Y-m-d');

        $arrData = [];
        $arrSchedules = [];
        $arrAttendances = [];

        $schedules = AttendanceWorkSchedule::whereBetween('date', [$startDate, $endDate])->select(['employee_id', 'date'])->whereNot('shift_id', '0')->get();
        $attendances = Attendance::whereBetween('start_date', [$startDate, $endDate])->select(['id', 'duration', 'employee_id', 'type', 'type_id', 'start_date', 'start_time', 'end_time'])->get();

        foreach ($schedules as $schedule) {
            $arrSchedules[$schedule->employee_id][$schedule->date] = $schedule;
        }

        foreach ($attendances as $attendance) {
            $arrAttendances[$attendance->employee_id][$attendance->start_date] = $attendance;
        }

        $dateNow = Carbon::now()->format('Y-m-d');
        $totalWeekdayInMonth = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth()->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, Carbon::create($filterYear, $filterMonth, 1)->endOfMonth());

        $holidays = AttendanceHoliday::whereBetween('start_date', [$startDate, $endDate])->whereBetween('end_date', [$startDate, $endDate])->get();
        //get list of date from holidays
        $totalHolidays = 0;
        foreach ($holidays as $holiday) {
            $startDate = Carbon::parse($holiday->start_date);
            $endDate = Carbon::parse($holiday->end_date);
            $diff = $startDate->diffInDays($endDate);
            for ($i = 0; $i <= $diff; $i++) {
                $totalHolidays++;
                $startDate->addDay();
            }
        }

        foreach ($employees as $employee) {
            $arrData[$employee->id]['PT'] = 0;
            $arrData[$employee->id]['PC'] = 0;
            $arrData[$employee->id]['TPT'] = 0;
            $arrData[$employee->id]['A'] = 0;
            $arrData[$employee->id]['I'] = 0;
            $arrData[$employee->id]['C'] = 0;
            $arrData[$employee->id]['S'] = 0;
            $arrData[$employee->id]['IB'] = 0;
            $arrData[$employee->id]['DL'] = 0;
            $arrData[$employee->id]['HEF'] = $totalWeekdayInMonth - $totalHolidays;
            $arrData[$employee->id]['HDR'] = 0;
            $arrData[$employee->id]['JH'] = 0;
            $arrData[$employee->id]['JA'] = 0;
            $arrData[$employee->id]['JK'] = 0;
            $arrData[$employee->id]['HK'] = 0;
            $arrData[$employee->id]['TA'] = 0;
            $arrData[$employee->id]['PUNISHMENT'] = '';

            for ($i = 1; $i <= Carbon::create($filterYear, $filterMonth, 1)->daysInMonth; $i++) {
                $date = Carbon::create($filterYear, $filterMonth, $i)->format('Y-m-d');
                $carbonDate = Carbon::create($date);

                if(isset($arrSchedules[$employee->id][$date]) && !isset($arrAttendances[$employee->id][$date])) {
                    if($carbonDate < $dateNow) $arrData[$employee->id]['A']++;
                }else if($carbonDate->isWeekday() && !isset($arrAttendances[$employee->id][$date])) {
                    if($carbonDate < $dateNow) $arrData[$employee->id]['A']++;
                }else{
                    $data = $arrAttendances[$employee->id][$date] ?? null;
                    if($data){
                        if($data->type == 'C'){
                            $arrData[$employee->id]['C']++;
                        }else if($data->type == 'I'){
                            $getDataIzin = AttendancePermission::find($data->type_id);
                            //CHECK IF MASTER IS "SAKIT"
                            if($getDataIzin->category_id == 30 || $getDataIzin->category_id == 31) {
                                $arrData[$employee->id]['S']++;
                            }else {
                                $arrData[$employee->id]['I']++;
                            }
                        }else{
                            if($data->type == '3' || $data->type == '4' || $data->type == '5') $arrData[$employee->id]['DL']++;
                            $arrData[$employee->id]['HDR']++;

                            $arrData[$employee->id]['JA'] += convertTimeToSeconds($data->duration);

                            $pt = '';
                            $startShift = $defaultShift->start;
                            if (isset($arrSchedule[$employee->id][$date])) $startShift = $arrSchedule[$employee->id][$date]->start_time;
                            if ($data->start_time > $startShift && $data->type != '3' && $data->type != '4') {
                                $diffInMinutes = Carbon::parse($data->start_time)->diffInMinutes($startShift);

                                if($diffInMinutes) {
                                    if ($diffInMinutes <= 30){
                                        $pt = '0.50';
                                    } elseif ($diffInMinutes <= 60){
                                        $pt = '1.00';
                                    } elseif ($diffInMinutes <= 90){
                                        $pt = '1.25';
                                    } else {
                                        $pt = '1.50';
                                    }
                                }
                            }

                            $pc = '';
                            $endShift = $defaultShift->end;
                            if (isset($arrSchedule[$employee->id][$date])) $endShift = $arrSchedule[$employee->id][$date]->end_time;
                            if ($data->end_time < $endShift && $data->type != '3' && $data->type != '5') {
                                $diffInMinutes = Carbon::parse($data->end_time)->diffInMinutes($endShift);

                                if($diffInMinutes) {
                                    if ($diffInMinutes <= 30){
                                        $pc = '0.50';
                                    } elseif ($diffInMinutes <= 60){
                                        $pc = '1.00';
                                    } elseif ($diffInMinutes <= 90){
                                        $pc = '1.25';
                                    } else {
                                        $pc = '1.50';
                                    }
                                }
                            }

                            $arrData[$employee->id]["PT"] += (double)$pt;
                            $arrData[$employee->id]["PC"] += (double)$pc;
                            $arrData[$employee->id]["TPT"] = $arrData[$employee->id]["PT"] + $arrData[$employee->id]["PC"];

                        }
                    }
                }
            }
            $totalMinutesJH = $arrData[$employee->id]['HDR'] * 450;
            $totalMinutesJA = round($arrData[$employee->id]['JA'] / 60);
            $totalMinutesJK = $totalMinutesJH - $totalMinutesJA;
            $arrData[$employee->id]['JH'] = $totalMinutesJH > 0 ? convertMinutesToTime($totalMinutesJH) : "";
            $arrData[$employee->id]['JH'] = empty($arrData[$employee->id]['JH']) ? 0 : $arrData[$employee->id]['JH'];

            $arrData[$employee->id]['JA'] = $totalMinutesJA > 0 ? convertMinutesToTime($totalMinutesJA) : "";
            $arrData[$employee->id]['JA'] = empty($arrData[$employee->id]['JA']) ? 0 : $arrData[$employee->id]['JA'];

            $arrData[$employee->id]['JK'] = abs($totalMinutesJK) > 0 ? convertMinutesToTime(abs($totalMinutesJK)) : "";
            $arrData[$employee->id]['JK'] = (empty($arrData[$employee->id]['JK']) ? 0 : $totalMinutesJK < 0) ? "-" . $arrData[$employee->id]['JK'] : $arrData[$employee->id]['JK'];

            $arrData[$employee->id]['HK'] = (ceil($totalMinutesJK / 24 / 60)) ?? 0;
            $arrData[$employee->id]['TA'] = $arrData[$employee->id]['HK'];
        }

        return $arrData;
    }
}
