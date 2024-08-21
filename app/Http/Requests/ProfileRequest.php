<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'age' => 'required|integer',
            'bio' => 'required|string'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages()
    {
        return [
            'age.required' => 'Umur wajib diisi',
            'age.integer' => 'Umur harus berupa angka',
            'bio.required' => 'Biodata wajib diisi',
            'bio.string' => 'Biodata harus berupa teks',
        ];
    }
}
