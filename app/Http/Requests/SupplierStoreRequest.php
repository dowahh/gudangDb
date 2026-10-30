<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SupplierStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // otorisasi sebenarnya di middleware role:admin pada route
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama supplier wajib diisi.',
            'name.max' => 'Nama supplier maksimal 255 karakter.',
            'phone.string' => 'Nomor telepon tidak valid.',
            'phone.max' => 'Nomor telepon maksimal 255 karakter.',
            'address.string' => 'Alamat tidak valid.',
        ];
    }
}
