<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class ShiftRequest extends FormRequest
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
            'code' => 'required',
            'start' => 'required',
            'end' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Kolom Nama tidak boleh kosong',
            'code.required' => 'Kolom Kode tidak boleh kosong',
            'start.required' => 'Kolom Jam Mulai tidak boleh kosong',
            'end.required' => 'Kolom Jam Selesai tidak boleh kosong',
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
