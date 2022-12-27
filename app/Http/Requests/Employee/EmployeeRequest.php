<?php

namespace App\Http\Requests\Employee;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class EmployeeRequest extends FormRequest
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
            'name' => 'required',
            'employee_number' => 'required',
            'position_id' => 'required',
            'employee_type_id' => 'required',
            'rank_id' => 'required',
            'start_date' => 'required',
            'location_id' => 'required',
            'unit_id' => 'required',
            'status_id' => 'required',
            'join_date' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama harus diisi',
            'employee_number.required' => 'Nomor Karyawan harus diisi',
            'position_id.required' => 'Jabatan harus diisi',
            'employee_type_id.required' => 'Tipe Karyawan harus diisi',
            'rank_id.required' => 'Pangkat harus diisi',
            'start_date.required' => 'Tanggal Mulai harus diisi',
            'location_id.required' => 'Lokasi harus diisi',
            'unit_id.required' => 'Unit harus diisi',
            'status_id.required' => 'Status harus diisi',
            'join_date.required' => 'Tanggal Masuk harus diisi',
        ];
    }
}
