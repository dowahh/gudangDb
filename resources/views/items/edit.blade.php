@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
    <header class="page-header">
        <div>
            <h1 class="page-title">Edit Barang</h1>
            <p class="page-meta mono">{{ $item->sku }}</p>
        </div>
        <div class="page-header-aside">
            <span class="page-date">{{ now()->format('d/m/Y') }}</span>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </header>

    <div class="kartu-label form-panel">
        <div class="kartu-label-header">Form Edit Barang</div>

        <form method="POST" action="{{ route('items.update', $item) }}" class="form-grid">
            {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
            @csrf
            @method('PUT')

            <div>
                <label for="sku" class="label">SKU</label>
                <input type="text" id="sku" name="sku" value="{{ old('sku', $item->sku) }}" required class="input mono">
                @error('sku')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="label">Nama Barang</label>
                <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}" required class="input">
                @error('name')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="label">Kategori</label>
                <select id="category_id" name="category_id" required class="input">
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $item->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-row-2">
                <div>
                    <label for="min_stock" class="label">Stok Minimum</label>
                    <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', $item->min_stock) }}"
                           min="0" required class="input">
                    @error('min_stock')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="price" class="label">Harga (Rp)</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $item->price) }}" min="0"
                           step="0.01" required class="input">
                    @error('price')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <p class="hint-text">Stok tidak diedit di sini ({{ $item->stock }} unit) — lewat transaksi masuk/keluar.</p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                <a href="{{ route('items.index') }}" class="link">Batal</a>
            </div>
        </form>
    </div>
@endsection
