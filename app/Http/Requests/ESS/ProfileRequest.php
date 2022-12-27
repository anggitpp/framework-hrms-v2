<?php

namespace App\Http\Requests\ESS;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'status_id' => 'required',
            'join_date' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'employee_number.required' => 'Nomor Karyawan wajib diisi',
            'status_id.required' => 'Status wajib diisi',
            'join_date.required' => 'Tanggal Masuk wajib diisi',
        ];
    }
}
