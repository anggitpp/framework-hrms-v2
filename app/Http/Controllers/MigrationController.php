<?php

namespace App\Http\Controllers;

use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
use App\Models\Setting\AppParameter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MigrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function employees()
    {
        $datas = DB::table('users_kanwil')->get();

        DB::beginTransaction();

        try {

            foreach ($datas as $data) {
                $getNameUnit = DB::table('jenis')->where('id', $data->jenis)->first();
                if($getNameUnit){
                    $getIdUnit = AppMasterData::whereAppMasterCategoryCode('EMU')->whereName($getNameUnit->name)->first();
                    $data->jenis = $getIdUnit->id;
                }

                $getNamePosition = DB::table('jab')->where('id', $data->jab_id)->first();
                if($getNamePosition){
                    $getIdPosition = AppMasterData::whereAppMasterCategoryCode('EMP')->whereName($getNamePosition->name)->first();
                    $data->jab_id = $getIdPosition->id;
                }

                $getNameRanks = DB::table('jabatan')->where('id', $data->jabatan_id)->first();
                if($getNameRanks){
                    $getIdPosition = AppMasterData::whereAppMasterCategoryCode('EP')->whereName($getNameRanks->nama)->first();
                    $data->jabatan_id = $getIdPosition->id;
                }

                $getNameGrade = DB::table('golongan')->where('id', $data->gol)->first();
                if($getNameGrade){
                    $getIdGrade = AppMasterData::whereAppMasterCategoryCode('EG')->whereName($getNameGrade->name)->first();
                    $data->gol = $getIdGrade->id;
                }

                $data->status_pegawai = AppMasterData::whereName($data->status_pegawai)->first()->id ?? 0;

                $getStatusActive = AppParameter::whereCode('SAP')->first()->value;
                $getShiftDefault = AppParameter::whereCode('SOH')->first()->value;

                $name = $data->name;
                $email = $data->email;
                $mobile_phone_number = $data->phone;
                $employee_number = $data->nik;
                $status_id = $getStatusActive;
                $join_date = Carbon::now()->format('Y-m-d');

                $start_date = Carbon::now()->format('Y-m-d');
                $unit_id = $data->jenis;
                $location_id = AppMasterData::whereName('Kanwil Kemenag Prov. DKI Jakarta')->first()->id;
                $position_id = $data->jab_id;
                $rank_id = $data->jabatan_id;
                $employee_type_id = $data->status_pegawai;
                $grade_id = $data->gol ?? 0;
                $status = 't';

                $employee = Employee::create([
                    'name' => $name,
                    'email' => $email,
                    'mobile_phone_number' => $mobile_phone_number,
                    'employee_number' => $employee_number,
                    'status_id' => $status_id,
                    'join_date' => $join_date,
                ]);

                $employee->position()->create([
                    'start_date' => $start_date,
                    'unit_id' => $unit_id,
                    'location_id' => $location_id,
                    'position_id' => $position_id ?? 0,
                    'rank_id' => $rank_id ?? 0,
                    'grade_id' => $grade_id ?? 0,
                    'employee_type_id' => $employee_type_id,
                    'shift_id' => $getShiftDefault,
                    'status' => $status,
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }

    }

    public function positions()
    {
        $datas = DB::table('jab')->get();

        $no = 0;
        foreach ($datas as $data) {
            $no++;
            $code = 'EMP'.str_pad($no, 3, '0', STR_PAD_LEFT);
            AppMasterData::create([
                'app_master_category_code' => 'EMP',
                'name' => $data->name,
                'code' => $code,
                'order' => $no,
            ]);
        }
    }

    public function units()
    {
        $datas = DB::table('jenis')->get();

        $no = 0;
        foreach ($datas as $data) {
            $no++;
            $code = 'EMU' . str_pad($no, 3, '0', STR_PAD_LEFT);
            AppMasterData::create([
                'app_master_category_code' => 'EMU',
                'name' => $data->name,
                'code' => $code,
                'order' => $no,
            ]);
        }
    }

    public function grades()
    {
        $datas = DB::table('golongan')->get();

        $no = 0;
        foreach ($datas as $data) {
            $no++;
            $code = 'EG' . str_pad($no, 3, '0', STR_PAD_LEFT);
            AppMasterData::create([
                'app_master_category_code' => 'EG',
                'name' => $data->name,
                'code' => $code,
                'order' => $no,
            ]);
        }
    }

    public function ranks()
    {
        $datas = DB::table('jabatan')->get();

        $no = 0;
        foreach ($datas as $data) {
            $no++;
            $code = 'EP' . str_pad($no, 3, '0', STR_PAD_LEFT);
            AppMasterData::create([
                'app_master_category_code' => 'EP',
                'name' => $data->nama,
                'code' => $code,
                'order' => $no,
            ]);
        }
    }

    public function updatePin()
    {
        $datas = DB::table('users_kanwil')->get();

        foreach ($datas as $data) {
            $employee = Employee::whereEmployeeNumber($data->nik)->first();
            $employee?->update([
                'attendance_pin' => $data->nomor_induk ?? '',
            ]);
        }
    }
}
