<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeTerminationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'employee_id' => 'required',
            'number' => 'required',
            'reason_id' => 'required',
            'date' => 'required',
            'type_id' => 'required',
            'effective_date' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'Nama Karyawan harus diisi',
            'number.required' => 'Nomor harus diisi',
            'reason_id.required' => 'Alasan harus diisi',
            'date.required' => 'Tanggal harus diisi',
            'type_id.required' => 'Tipe harus diisi',
            'effective_date.required' => 'Tanggal Efektif harus diisi',
        ];
    }
}
