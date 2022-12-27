<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class MachineRequest extends FormRequest
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
            'location_id' => 'required',
            'name' => 'required',
            'ip_address' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'location_id.required' => 'Lokasi harus diisi',
            'name.required' => 'Nama harus diisi',
            'ip_address.required' => 'IP Address harus diisi',
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
