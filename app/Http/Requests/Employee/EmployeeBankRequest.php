<?php

namespace App\Http\Requests\Employee;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class EmployeeBankRequest extends FormRequest
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
            'bank_id' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'branch' => 'nullable',
            'description' => 'nullable',
            'status' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Karyawan harus diisi',
            'bank_id.required' => 'Bank harus diisi',
            'account_number.required' => 'Nomor Rekening harus diisi',
            'account_name.required' => 'Nama Rekening harus diisi',
            'status.required' => 'Status harus diisi',
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
