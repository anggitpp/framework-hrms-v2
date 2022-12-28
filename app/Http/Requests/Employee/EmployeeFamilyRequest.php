<?php

namespace App\Http\Requests\Employee;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class EmployeeFamilyRequest extends FormRequest
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
            'employee_id' => 'required|integer',
            'relationship_id' => 'required|integer',
            'name' => 'required|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    //geenreate message with indonesian language
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Karyawan harus diisi',
            'relationship_id.required' => 'Hubungan harus diisi',
            'name.required' => 'Nama harus diisi',
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
