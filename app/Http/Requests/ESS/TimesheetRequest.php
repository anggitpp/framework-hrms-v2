<?php

namespace App\Http\Requests\ESS;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class TimesheetRequest extends FormRequest
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
            'activity' => 'required',
            'output' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'volume' => 'required',
            'type' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'activity.required' => 'Kegiatan harus diisi',
            'output.required' => 'Output harus diisi',
            'date.required' => 'Tanggal harus diisi',
            'start_time.required' => 'Jam Mulai harus diisi',
            'end_time.required' => 'Jam Selesai harus diisi',
            'volume.required' => 'Volume harus diisi',
            'type.required' => 'Tipe harus diisi',
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
