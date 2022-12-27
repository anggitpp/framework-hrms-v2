<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class EmployeePositionHistoryRequest extends FormRequest
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
            'position_id' => 'required',
            'employee_type_id' => 'required',
            'rank_id' => 'required',
            'location_id' => 'required',
            'unit_id' => 'required',
            'start_date' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Kolom Pegawai tidak boleh kosong',
            'position_id.required' => 'Kolom Posisi tidak boleh kosong',
            'employee_type_id.required' => 'Kolom Tipe Pegawai tidak boleh kosong',
            'rank_id.required' => 'Kolom Pangkat tidak boleh kosong',
            'location_id.required' => 'Kolom Lokasi tidak boleh kosong',
            'unit_id.required' => 'Kolom Unit tidak boleh kosong',
            'start_date.required' => 'Kolom Tanggal Mulai tidak boleh kosong',
        ];
    }
}
