<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class OvertimeRequest extends FormRequest
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
            'start_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'Kolom Nomor tidak boleh kosong',
            'employee_id.required' => 'Kolom Nama tidak boleh kosong',
            'date.required' => 'Kolom Tanggal tidak boleh kosong',
            'start_date.required' => 'Kolom Tanggal Mulai tidak boleh kosong',
            'start_time.required' => 'Kolom Jam Mulai tidak boleh kosong',
            'end_time.required' => 'Kolom Jam Selesai tidak boleh kosong',
        ];
    }
}
