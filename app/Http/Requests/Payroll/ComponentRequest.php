<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class ComponentRequest extends FormRequest
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
            'code' => 'required',
            'name' => 'required',
        ];
    }

    //generate meessage with indonesian language
    public function messages(): array
    {
        return [
            'code.required' => 'Kode harus diisi',
            'name.required' => 'Nama harus diisi',
        ];
    }
}
