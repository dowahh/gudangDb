@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Tambah Barang</h1>

    <div class="bg-white rounded border border-gray-200 p-6 max-w-lg">
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('items.store') }}" class="space-y-4">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf

            <div>
                <label for="category_id" class="block text-sm font-medium mb-1">Kategori</label>
                <select id="category_id" name="category_id" required
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sku" class="block text-sm font-medium mb-1">SKU</label>
                <input type="text" id="sku" name="sku" value="{{ old('sku') }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div>
                <label for="name" class="block text-sm font-medium mb-1">Nama Barang</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div>
                <label for="min_stock" class="block text-sm font-medium mb-1">Stok Minimum</label>
                <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', 0) }}" min="0" required
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <div>
                <label for="price" class="block text-sm font-medium mb-1">Harga (Rp)</label>
                <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" required
                       class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-500">
            </div>

            <p class="text-xs text-gray-500">Stok awal otomatis 0. Perubahan stok lewat transaksi masuk/keluar.</p>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded bg-slate-800 text-white px-4 py-2 text-sm hover:bg-slate-700">
                    Simpan
                </button>
                <a href="{{ route('items.index') }}" class="text-sm text-gray-600 underline">Batal</a>
            </div>
        </form>
    </div>
@endsection
