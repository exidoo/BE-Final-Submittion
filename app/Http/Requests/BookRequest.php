<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'summary' => 'required|string',
            'page' => 'required|string',
            'stok' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'category_id' => 'required|exists:categories,id'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Inputan title tidak boleh kosong',
            'summary.required' => 'Inputan summary tidak boleh kosong',
            'stok.required' => 'Inputan tahun tidak boleh kosong',
            'page.required' => 'Jumlah page harus diisi',
            'image.mimes' => 'Format poster hanya boleh png, jpg, jpeg',
            'category_id.required' => 'Category id tidak boleh kosong',
            'category_id.exists' => 'Category id tidak valid',
            'title.max' => 'Inputan title maksimal 255 karakter'
        ];
    }
}
