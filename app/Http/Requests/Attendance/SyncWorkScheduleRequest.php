<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class SyncWorkScheduleRequest extends FormRequest
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
            'shift_id' => 'required',
            'start_date' => 'required|date_format:d/m/Y|before_or_equal:end_date',
            'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'shift_id.required' => 'Shift harus diisi',
            'start_date.required' => 'Tanggal mulai harus diisi',
            'start_date.before_or_equal' => 'Tanggal mula harus sebelum atau sama dengan tanggal akhir',
            'end_date.required' => 'Tanggal akhir harus diisi',
            'end_date.after_or_equal' => 'Tanggal akhir harus sesudah atau sama dengan tanggal mulai',
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
