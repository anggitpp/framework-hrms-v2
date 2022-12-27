<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CorrectionRequest extends FormRequest
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
            'number' => 'required',
            'employee_id' => 'required',
            'date' => 'required',
            'attendance_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ];
    }

    //generate message with indonesian language
    public function messages(): array
    {
        return [
            'number.required' => 'Nomor tidak boleh kosong',
            'employee_id.required' => 'Karyawan tidak boleh kosong',
            'date.required' => 'Tanggal tidak boleh kosong',
            'attendance_date.required' => 'Tanggal Absensi tidak boleh kosong',
            'start_time.required' => 'Jam Mulai tidak boleh kosong',
            'end_time.required' => 'Jam Selesai tidak boleh kosong',
        ];
    }
}
