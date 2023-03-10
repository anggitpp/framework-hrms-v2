<?php

namespace App\Http\Requests\Employee;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class EmployeeWorkRequest extends FormRequest
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
            'company' => 'required',
            'position' => 'required',
            'start_date' => 'required',
        ];
    }
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Nama Karyawan harus diisi',
            'company.required' => 'Nama Perusahaan harus diisi',
            'position.required' => 'Posisi harus diisi',
            'start_date.required' => 'Tanggal Mulai harus diisi',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $errors = (new ValidationException($validator))->errors();
            throw new HttpResponseException(
                response()->json(['errors' => $errors], 200)
            );
        }

        parent::failedValidation($validator);
    }
}
