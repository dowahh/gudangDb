<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // staf & admin sama-sama boleh catat transaksi (route dijaga auth)
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'type' => ['required', 'string', Rule::in(['masuk', 'keluar'])],
            'qty' => ['required', 'integer', 'min:1'],
            // masuk boleh pilih supplier; keluar supplier diabaikan di controller
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'note' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang wajib dipilih.',
            'item_id.exists' => 'Barang tidak ditemukan.',
            'type.required' => 'Jenis transaksi wajib dipilih.',
            'type.in' => 'Jenis transaksi harus masuk atau keluar.',
            'qty.required' => 'Jumlah wajib diisi.',
            'qty.integer' => 'Jumlah harus bilangan bulat.',
            'qty.min' => 'Jumlah minimal 1.',
            'supplier_id.exists' => 'Supplier tidak ditemukan.',
            'note.string' => 'Catatan tidak valid.',
            'note.max' => 'Catatan maksimal 255 karakter.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Tanggal transaksi tidak valid.',
        ];
    }
}
