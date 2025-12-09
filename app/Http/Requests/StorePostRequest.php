<?php

namespace App\Http\Requests;
use App\Rules\NoSpam;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|min:5|max:255|NoSpam',
            'slug' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:50',
            'status' => 'sometimes|in:draft,published'
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori harus diisi',
            'category_id.exists' => 'Kategori tidak ada',
            'title.required' => 'Judul Wajib Diisi',
            'title.min' => 'Judul minimal 5 karakter',
            'content.required' => 'Content Wajib Diisi',
            'content.min' => 'Content minimal 50 Karakter',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi Gagal',
            'errors' => $validator->errors()
        ], 422));
    }

}
