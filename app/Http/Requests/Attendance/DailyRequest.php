<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class DailyRequest extends FormRequest
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
            'start_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'start_address' => 'required',
            'end_address' => 'required',
        ];
    }

    //generate message with indonesian message
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Nama karyawan harus diisi',
            'start_date.required' => 'Tanggal harus diisi',
            'start_time.required' => 'Jam mulai harus diisi',
            'end_time.required' => 'Jam selesai harus diisi',
            'start_address.required' => 'Alamat mulai harus diisi',
            'end_address.required' => 'Alamat selesai harus diisi',
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
