<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class AppMasterDataRequest extends FormRequest
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
            'parent_id' => 'required',
            'name' => 'required|string|max:255',
            'code' => 'required',
            'order' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'parent_id.required' => 'Induk harus diisi',
            'name.required' => 'Nama harus diisi',
            'code.required' => 'Kode harus diisi',
            'order.required' => 'Urutan harus diisi',
        ];
    }
}
