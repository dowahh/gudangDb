@extends('layouts.app')

@section('title', 'Catat Transaksi')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Catat Transaksi Stok</h1>

    <div class="bg-white rounded border border-gray-200 p-6 max-w-lg">
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf

            <div>
                <label for="item_id" class="block text-sm font-medium mb-1">Barang</label>
                <select id="item_id" name="item_id" required
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <option value="">Pilih barang</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                            {{ $item->name }} ({{ $item->sku }}) — stok {{ $item->stock }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="type" class="block text-sm font-medium mb-1">Jenis</label>
                <select id="type" name="type" required
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <option value="masuk" @selected(old('type', 'masuk') === 'masuk')>Masuk</option>
                    <option value="keluar" @selected(old('type') === 'keluar')>Keluar</option>
                </select>
            </div>

            <div>
                <label for="qty" class="block text-sm font-medium mb-1">Jumlah</label>
                <input type="number" id="qty" name="qty" value="{{ old('qty') }}" min="1" required
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div>
                <label for="supplier_id" class="block text-sm font-medium mb-1">Supplier (opsional, untuk masuk)</label>
                <select id="supplier_id" name="supplier_id"
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <option value="">Tanpa supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Untuk transaksi keluar, supplier otomatis diabaikan.</p>
            </div>

            <div>
                <label for="transaction_date" class="block text-sm font-medium mb-1">Tanggal</label>
                <input type="date" id="transaction_date" name="transaction_date"
                       value="{{ old('transaction_date', now()->toDateString()) }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div>
                <label for="note" class="block text-sm font-medium mb-1">Catatan</label>
                <input type="text" id="note" name="note" value="{{ old('note') }}"
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded bg-slate-800 text-white px-4 py-2 text-sm hover:bg-slate-700">
                    Simpan Transaksi
                </button>
                <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 underline">Batal</a>
            </div>
        </form>
    </div>
@endsection
