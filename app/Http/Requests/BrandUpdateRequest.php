<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:brands,name,' . $this->route('brand'),
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama Brand'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama brand harus diisi',
            'name.unique' => 'Nama brand sudah digunakan',
            'name.max' => 'Nama brand maksimal 255 karakter'
        ];
    }
}
