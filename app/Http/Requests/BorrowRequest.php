<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowRequest extends FormRequest
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
            'load_date' => 'required|date_format:Y-m-d H:i:s',
            'barrow_date' => 'required|date_format:Y-m-d H:i:s',
            'book_id' => 'required|exists:books,id|uuid',
            'user_id' => 'required|exists:users,id|uuid',
        ];
    }

    public function messages(): array
    {
        return [
            'load_date.required' => 'Load date tidak boleh kosong',
            'load_date.date_format' => 'Format load date tidak valid, harus Y-m-d H:i:s',
            'barrow_date.required' => 'Barrow date tidak boleh kosong',
            'barrow_date.date_format' => 'Format barrow date tidak valid, harus Y-m-d H:i:s',
            'book_id.required' => 'Book id tidak boleh kosong',
            'book_id.exists' => 'Book id tidak valid',
            'book_id.uuid' => 'Format book id harus UUID',
            'user_id.required' => 'User id tidak boleh kosong',
            'user_id.exists' => 'User id tidak valid',
            'user_id.uuid' => 'Format user id harus UUID',
        ];
    }
}
