<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemUpdateRequest extends FormRequest
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
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'sku' => [
                'required',
                'string',
                'max:255',
                // unique saat update: abaikan record sendiri
                Rule::unique('items', 'sku')->ignore($this->route('item')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.integer' => 'Kategori tidak valid.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'sku.required' => 'SKU wajib diisi.',
            'sku.max' => 'SKU maksimal 255 karakter.',
            'sku.unique' => 'SKU sudah dipakai barang lain.',
            'name.required' => 'Nama barang wajib diisi.',
            'name.max' => 'Nama barang maksimal 255 karakter.',
            'min_stock.required' => 'Stok minimum wajib diisi.',
            'min_stock.integer' => 'Stok minimum harus bilangan bulat.',
            'min_stock.min' => 'Stok minimum tidak boleh minus.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh minus.',
        ];
    }
}
