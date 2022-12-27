<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class LeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'employee_id' => 'required',
            'leave_master_id' => 'required',
            'number' => 'required',
            'date' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Kolom Nama Pegawai tidak boleh kosong',
            'leave_master_id.required' => 'Kolom Jenis Cuti tidak boleh kosong',
            'number.required' => 'Kolom Jumlah Cuti tidak boleh kosong',
            'date.required' => 'Kolom Tanggal Cuti tidak boleh kosong',
            'start_date.required' => 'Kolom Tanggal Mulai Cuti tidak boleh kosong',
            'end_date.required' => 'Kolom Tanggal Akhir Cuti tidak boleh kosong',
        ];
    }
}
